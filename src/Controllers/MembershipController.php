<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Response;
use App\Core\Validator;
use App\Models\MembershipApplication;
use App\Models\Enum;
use App\Models\Upload;
use App\Models\ShareLink;
use App\Models\ConsentLog;
use App\Models\AuditLog;
use App\Core\Auth as CoreAuth;

class MembershipController extends Controller
{
    public function applyForm(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $application = $id ? MembershipApplication::find($id) : null;
        $prefill = [];
        if ($application && $application['draft_data']) {
            $prefill = json_decode($application['draft_data'], true) ?: [];
        }
        $enums = [
            'membership_types' => Enum::getMembershipTypes(),
            'genders' => Enum::getGenders(),
            'religions' => array_merge([['slug' => 'not_specified', 'label' => 'Not specified']], Enum::getReligions()),
            'marital_statuses' => array_merge([['slug' => 'not_specified', 'label' => 'Not specified']], Enum::getMaritalStatuses()),
            'blood_groups' => array_merge([['slug' => 'not_specified', 'label' => 'Not specified']], Enum::getBloodGroups()),
        ];
        $uploads = $id ? Upload::findByOwner('membership', $id) : [];
        $this->render('membership/form', [
            'title' => 'Membership Application',
            'draftId' => $id,
            'prefill' => $prefill,
            'enums' => $enums,
            'uploads' => $uploads,
        ]);
    }

    public function saveDraft(): void
    {
        // Rate limiting
        $rateLimiter = new RateLimiter();
        if (!$rateLimiter->allow('membership_draft_' . $this->getClientIp(), 60)) {
            $this->json(['error' => 'Too many requests'], 429);
            return;
        }
        
        $this->requireCsrf();
        
        $input = $this->getJsonInput();
        // Ensure associative array
        if (!is_array($input)) { $input = []; }
        $draftId = $input['draft_id'] ?? null;
        $data = $input['data'] ?? [];
        // If data is an array-of-pairs like [[k,v], ...], coerce to associative
        if (is_array($data) && !self::isAssoc($data)) {
            $coerced = [];
            foreach ($data as $row) {
                if (is_array($row) && count($row) >= 2) {
                    $coerced[(string)$row[0]] = $row[1];
                }
            }
            if (!empty($coerced)) {
                $data = $coerced;
            }
        }
        // Gracefully no-op if no meaningful data provided
        if (!is_array($data) || count($data) === 0) {
            $this->json([
                'ok' => true,
                'noop' => true,
                'draft_id' => $draftId ? (int)$draftId : null,
                'saved_at' => date('c')
            ]);
            return;
        }
        
        $applicationData = [
            'draft_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'created_ip' => $this->getClientIp()
        ];
        
        if ($draftId) {
            // Update existing draft
            $existing = MembershipApplication::find((int) $draftId);
            if (!$existing || $existing['status'] !== 'draft') {
                $this->json(['error' => 'Draft not found'], 404);
                return;
            }
            
            MembershipApplication::updateDraft((int) $draftId, $applicationData);
        } else {
            // Create new draft
            $draftId = MembershipApplication::createDraft($applicationData, $this->getClientIp());
        }
        
        $this->json([
            'ok' => true,
            'draft_id' => (int) $draftId,
            'saved_at' => date('c')
        ]);
    }

    private static function isAssoc(array $arr): bool
    {
        if ($arr === []) return true; // treat empty as assoc for our purposes
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function preview(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $application = $id ? MembershipApplication::find($id) : null;
        if (!$application) {
            $this->flash('error', 'Application not found');
            Response::redirect('/membership/apply');
            return;
        }
        $uploads = Upload::findByOwner('membership', $id);
        $merged = $application;
        if (!empty($application['draft_data'])) {
            $draft = json_decode($application['draft_data'], true) ?: [];
            foreach ($draft as $k => $v) {
                if ($v !== null && $v !== '') {
                    $merged[$k] = $v;
                }
            }
        }
        $this->render('membership/preview', [
            'title' => 'Preview Application',
            'application' => $application,
            'data' => $merged,
            'uploads' => $uploads,
            'is_share_view' => false
        ]);
    }

    public function submit(): void
    {
        $this->requireCsrf();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $application = $id ? MembershipApplication::find($id) : null;
        if (!$application) {
            $this->flash('error', 'Application not found');
            Response::redirect('/membership/apply');
            return;
        }
        // Merge posted fields with draft data for submission
        $input = array_merge(
            json_decode($application['draft_data'] ?? '[]', true) ?: [],
            $_POST
        );
        // validation rules
        $validator = Validator::make($input)
            ->required('full_name')->maxLength('full_name', 150)
            ->required('email')->email('email')->maxLength('email', 150)
            ->required('gender')->in('gender', array_map(fn($g) => $g['slug'], Enum::getGenders()))
            ->required('dob')->date('dob');

        if (!isset($input['terms_confirmed'])) {
            $validator->required('terms_confirmed', 'You must accept the terms');
        }

        if ($validator->fails()) {
            $this->flash('error', $validator->firstError() ?? 'Validation failed');
            Response::redirect('/membership/preview?id=' . $id);
            return;
        }

        // persist fields
        $fields = [
            'full_name','email','gender','dob','membership_type','nationality','father_name','mother_name','religion','marital_status','nid_no','passport_no','organization','designation','profession','education_qualifications','blood_group','spouse_name','num_children','children_names','address_office','address_permanent','address_present','mobile','emergency_name','emergency_relationship','emergency_phone','emergency_address','hobbies_interests','previous_club_memberships','proposer_name','proposer_membership_no','seconder_name','seconder_membership_no','confirmed_bgf_id','confirmed_argc_id'
        ];
    $update = [];
        foreach ($fields as $f) {
            if (isset($input[$f])) { $update[$f] = $input[$f]; }
        }
        $update['status'] = 'submitted';
        $update['submitted_at'] = date('Y-m-d H:i:s');
        $update['submitted_ip'] = $this->getClientIp();
        MembershipApplication::updateFieldsOnSubmit($id, $update);

        // consent log
        ConsentLog::createConsent('membership', $id, TERMS_VERSION, TERMS_TEXT, $this->getClientIp(), $_SERVER['HTTP_USER_AGENT'] ?? '');

        $this->flash('success', 'Application submitted successfully');
        Response::redirect('/membership/preview?id=' . $id);
    }

    public function share(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();

        // Accept id from form posts or JSON body
        $id = 0;
        if (isset($_POST['id'])) {
            $id = (int) $_POST['id'];
        } else {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
            $raw = file_get_contents('php://input') ?: '';
            if ($raw !== '' && stripos($contentType, 'application/json') !== false) {
                $data = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($data['id'])) {
                    $id = (int) $data['id'];
                }
            }
        }

        if ($id <= 0) {
            $this->json(['error' => 'Invalid or missing id'], 400);
            return;
        }

        $app = MembershipApplication::find($id);
        if (!$app) {
            $this->json(['error' => 'Not found'], 404);
            return;
        }

    $actorId = CoreAuth::id();
    $token = ShareLink::createFor('membership', $id, $actorId);
        $url = rtrim(APP_URL, '/') . '/s/' . $token;
    // audit
    AuditLog::create($actorId, $this->getClientIp(), 'share.created', 'membership', $id, [ 'url' => $url ]);
        $this->json(['ok' => true, 'url' => $url]);
    }
}