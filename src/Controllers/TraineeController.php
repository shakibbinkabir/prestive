<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Response;
use App\Core\Validator;
use App\Models\TraineeApplication;
use App\Models\MembershipApplication;
use App\Models\Enum;
use App\Models\Upload;
use App\Models\ShareLink;
use App\Models\ConsentLog;

class TraineeController extends Controller
{
    public function applyForm(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $application = $id ? TraineeApplication::find($id) : null;
        $prefill = [];
        if ($application && $application['draft_data']) {
            $prefill = json_decode($application['draft_data'], true) ?: [];
        }
        $enums = [
            'genders' => Enum::getGenders(),
            'religions' => array_merge([['slug' => 'not_specified', 'label' => 'Not specified']], Enum::getReligions()),
            'marital_statuses' => array_merge([['slug' => 'unmarried', 'label' => 'Unmarried'], ['slug' => 'married', 'label' => 'Married']], Enum::getMaritalStatuses()),
            'blood_groups' => array_merge([['slug' => 'not_specified', 'label' => 'Not specified']], Enum::getBloodGroups()),
        ];
        $uploads = $id ? Upload::findByOwner('trainee', $id) : [];
        $this->render('trainee/form', [
            'title' => 'Trainee Application',
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
        if (!$rateLimiter->allow('trainee_draft_' . $this->getClientIp(), 60)) {
            $this->json(['error' => 'Too many requests'], 429);
            return;
        }
        
        $this->requireCsrf();
        
        $input = $this->getJsonInput();
        if (!is_array($input)) { $input = []; }
        $draftId = $input['draft_id'] ?? null;
        $data = $input['data'] ?? [];
        if (is_array($data) && array_keys($data) === range(0, count($data)-1)) {
            // coerce array-of-pairs
            $coerced = [];
            foreach ($data as $row) {
                if (is_array($row) && count($row) >= 2) { $coerced[(string)$row[0]] = $row[1]; }
            }
            if (!empty($coerced)) { $data = $coerced; }
        }
        if (!is_array($data) || count($data) === 0) {
            $this->json(['ok' => true, 'noop' => true, 'draft_id' => $draftId ? (int)$draftId : null, 'saved_at' => date('c')]);
            return;
        }

        $applicationData = [
            'draft_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'created_ip' => $this->getClientIp()
        ];

        if ($draftId) {
            $existing = TraineeApplication::find((int)$draftId);
            if (!$existing || $existing['status'] !== 'draft') {
                $this->json(['error' => 'Draft not found'], 404);
                return;
            }
            TraineeApplication::updateDraft((int)$draftId, $applicationData);
        } else {
            $draftId = TraineeApplication::createDraft($applicationData, $this->getClientIp());
        }

        $this->json(['ok' => true, 'draft_id' => (int)$draftId, 'saved_at' => date('c')]);
    }

    public function preview(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $application = $id ? TraineeApplication::find($id) : null;
        if (!$application) {
            $this->flash('error', 'Application not found');
            Response::redirect('/trainee/apply');
            return;
        }
        $uploads = Upload::findByOwner('trainee', $id);
        $merged = $application;
        if (!empty($application['draft_data'])) {
            $draft = json_decode($application['draft_data'], true) ?: [];
            foreach ($draft as $k => $v) { if ($v !== null && $v !== '') { $merged[$k] = $v; } }
        }
        $this->render('trainee/preview', [
            'title' => 'Trainee Preview',
            'application' => $application,
            'data' => $merged,
            'uploads' => $uploads,
            'is_share_view' => false,
        ]);
    }

    public function submit(): void
    {
        $this->requireCsrf();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $application = $id ? TraineeApplication::find($id) : null;
        if (!$application) {
            $this->flash('error', 'Application not found');
            Response::redirect('/trainee/apply');
            return;
        }
        $input = array_merge(json_decode($application['draft_data'] ?? '[]', true) ?: [], $_POST);

        $genders = array_map(fn($g) => $g['slug'], Enum::getGenders());
        $religions = array_merge(['not_specified'], array_map(fn($r) => $r['slug'], Enum::getReligions()));
        $bloods = array_merge(['not_specified'], array_map(fn($b) => $b['slug'], Enum::getBloodGroups()));

        $v = Validator::make($input)
            ->required('training_for')->in('training_for', ['self','other'])
            ->required('name')->maxLength('name', 150)
            ->required('dob')->date('dob')
            ->required('phone')->maxLength('phone', 40)
            ->required('email')->email('email')->maxLength('email', 150)
            ->required('last_or_current_education')->maxLength('last_or_current_education', 150)
            ->required('institution')->maxLength('institution', 200)
            ->required('gender')->in('gender', $genders)
            ->required('religion')->in('religion', $religions)
            ->required('blood_group')->in('blood_group', $bloods)
            ->required('father_name')->maxLength('father_name', 150)
            ->required('father_profession')->maxLength('father_profession', 150)
            ->required('mother_name')->maxLength('mother_name', 150)
            ->required('mother_profession')->maxLength('mother_profession', 150)
            ->required('address_present');

        // trainee_type rules
        if (($input['training_for'] ?? '') === 'self') {
            $input['trainee_type'] = 'senior'; // enforce
            $v->required('bgf_id');
        } else {
            $v->required('trainee_type')->in('trainee_type', ['junior','senior']);
        }
        if (($input['trainee_type'] ?? '') === 'senior') {
            $v->required('marital_status')->maxLength('marital_status', 30);
            $v->required('occupation')->maxLength('occupation', 150);
        }

        if (!isset($input['terms_confirmed'])) {
            $v->required('terms_confirmed', 'You must accept the terms');
        }

        if ($v->fails()) {
            $this->flash('error', $v->firstError() ?? 'Validation failed');
            Response::redirect('/trainee/preview?id=' . $id);
            return;
        }

        $fields = [
            'training_for','trainee_type','bgf_id','name','dob','phone','email','last_or_current_education','institution','club_name','membership_no','father_name','father_profession','mother_name','mother_profession','address_present','gender','religion','blood_group','hobby','specialty','marital_status','occupation'
        ];
        $update = [];
        foreach ($fields as $f) { if (isset($input[$f])) { $update[$f] = $input[$f]; } }
        $update['status'] = 'submitted';
        $update['submitted_at'] = date('Y-m-d H:i:s');
        $update['submitted_ip'] = $this->getClientIp();
        TraineeApplication::updateFieldsOnSubmit($id, $update);

        ConsentLog::createConsent('trainee', $id, TERMS_VERSION, TERMS_TEXT, $this->getClientIp(), $_SERVER['HTTP_USER_AGENT'] ?? '');

        $this->flash('success', 'Application submitted successfully');
        Response::redirect('/trainee/preview?id=' . $id);
    }

    public function share(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) { $this->json(['error' => 'Invalid id'], 400); return; }
        $app = TraineeApplication::find($id);
        if (!$app) { $this->json(['error' => 'Not found'], 404); return; }
        $token = ShareLink::createFor('trainee', $id, \App\Core\Auth::id());
        $url = rtrim(APP_URL, '/') . '/s/' . $token;
        $this->json(['ok' => true, 'url' => $url]);
    }

    // Public BGF lookup for self path
    public function lookupByBGF(string $bgf): void
    {
        // rate limit
        $limiter = new RateLimiter();
        if (!$limiter->allow('bgf_lookup_' . $this->getClientIp(), 60)) {
            $this->json(['error' => 'Too many requests'], 429);
            return;
        }
        $bgf = trim($bgf);
        if ($bgf === '') { $this->json(['error' => 'Invalid id'], 400); return; }
        $row = MembershipApplication::findConfirmedByBGF($bgf);
        if (!$row) { $this->json(['error' => 'Not found'], 404); return; }
        // Map to trainee fields
        $data = [
            'name' => $row['full_name'] ?? null,
            'dob' => $row['dob'] ?? null,
            'gender' => $row['gender'] ?? null,
            'religion' => $row['religion'] ?? null,
            'blood_group' => $row['blood_group'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['mobile'] ?? null,
            'address_present' => $row['address_present'] ?? null,
            'club_name' => $row['club_name'] ?? null,
            'membership_no' => $row['membership_no'] ?? null,
            'bgf_id' => $bgf,
        ];
        $this->json(['ok' => true, 'data' => $data]);
    }
}