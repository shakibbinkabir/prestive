<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Validator;
use App\Models\MembershipApplication;
use App\Models\TraineeApplication;
use App\Models\Upload;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Core\Auth;

class ApplicationController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();

        $type = ($_GET['type'] ?? 'membership') === 'trainee' ? 'trainee' : 'membership';
        $status = $_GET['status'] ?? '';
        $q = trim($_GET['q'] ?? '');
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
    // Extra optional filters
    $membershipType = $_GET['membership_type'] ?? '';
    $bgfId = $_GET['bgf_id'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $sort = $_GET['sort'] ?? 'id';
        $dir = $_GET['dir'] ?? 'desc';
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [];
        if ($status) { $filters['status'] = $status; }
        if ($dateFrom) { $filters['date_from'] = $dateFrom; }
        if ($dateTo) { $filters['date_to'] = $dateTo; }
    if ($q) { $filters['q'] = $q; }
    if ($type === 'membership' && $membershipType) { $filters['membership_type'] = $membershipType; }
    if ($type === 'trainee' && $bgfId) { $filters['bgf_id'] = $bgfId; }

        if ($type === 'membership') {
            $total = MembershipApplication::countWithFilters($filters);
            $rows = MembershipApplication::listWithFilters($filters, $sort, $dir, $perPage, $offset);
        } else {
            $total = TraineeApplication::countWithFilters($filters);
            $rows = TraineeApplication::listWithFilters($filters, $sort, $dir, $perPage, $offset);
        }

        $pages = (int)ceil($total / $perPage);

        $this->render('admin/applications/index', [
            'title' => 'Applications',
            'type' => $type,
            'rows' => $rows,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
            'sort' => $sort,
            'dir' => $dir,
            'filters' => [
                'status' => $status,
                'q' => $q,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'membership_type' => $membershipType,
                'bgf_id' => $bgfId,
            ],
            'csrf' => $this->csrfToken(),
        ]);
    }

    public function show(string $type, int $id): void
    {
        $this->requireAdmin();
        $type = $type === 'trainee' ? 'trainee' : 'membership';
        $app = $type === 'membership' ? MembershipApplication::findWithUploads($id) : TraineeApplication::findWithUploads($id);
        if (!$app) {
            $this->flash('error', 'Not found');
            Response::redirect('/admin/applications?type=' . $type);
            return;
        }
        $logs = AuditLog::listForTarget($type, $id, 50, 0);
        $this->render('admin/applications/show', [
            'title' => ucfirst($type) . ' Application',
            'type' => $type,
            'app' => $app,
            'logs' => $logs,
            'csrf' => $this->csrfToken(),
        ]);
    }

    public function transition(string $type, int $id): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $type = $type === 'trainee' ? 'trainee' : 'membership';
    $to = $_POST['to_status'] ?? '';
    $note = trim($_POST['note'] ?? '');
        if (!in_array($to, ['payment_received','paid'], true)) {
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            if (stripos($accept, 'application/json') === false) {
                $this->flash('error', 'Invalid status');
                \App\Core\Response::redirect('/admin/applications/' . $type . '/' . $id);
            }
            $this->json(['error' => 'Invalid status'], 400);
            return;
        }
        try {
            if ($type === 'membership') {
                MembershipApplication::transition($id, $to, Auth::id() ?? 0, $note !== '' ? ['note' => $note] : []);
            } else {
                TraineeApplication::transition($id, $to, Auth::id() ?? 0, $note !== '' ? ['note' => $note] : []);
            }
            $this->flash('success', 'Status updated');
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            if (stripos($accept, 'application/json') === false) {
                \App\Core\Response::redirect('/admin/applications/' . $type . '/' . $id);
            }
            $this->json(['ok' => true]);
        } catch (\Throwable $e) {
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            if (stripos($accept, 'application/json') === false) {
                $this->flash('error', $e->getMessage());
                \App\Core\Response::redirect('/admin/applications/' . $type . '/' . $id);
            }
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(string $type, int $id): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $type = $type === 'trainee' ? 'trainee' : 'membership';

        // Accept application/x-www-form-urlencoded
        $input = $_POST;
        // Field whitelists per type
        if ($type === 'membership') {
            $allowed = ['full_name','email','gender','dob','membership_type','nationality','father_name','mother_name','religion','marital_status','nid_no','passport_no','organization','designation','profession','education_qualifications','blood_group','spouse_name','num_children','children_names','address_office','address_permanent','address_present','mobile','emergency_name','emergency_relationship','emergency_phone','emergency_address','hobbies_interests','previous_club_memberships','proposer_name','proposer_membership_no','seconder_name','seconder_membership_no','confirmed_bgf_id','confirmed_argc_id','status'];
            $current = MembershipApplication::find($id);
        } else {
            $allowed = ['training_for','trainee_type','bgf_id','name','dob','phone','email','last_or_current_education','institution','club_name','membership_no','father_name','father_profession','mother_name','mother_profession','address_present','gender','religion','blood_group','hobby','specialty','marital_status','occupation','status'];
            $current = TraineeApplication::find($id);
        }
        if (!$current) { $this->json(['error' => 'Not found'], 404); return; }

        // Build update payload with only allowed keys
        $update = [];
        foreach ($allowed as $k) {
            if (array_key_exists($k, $input)) { $update[$k] = $input[$k]; }
        }
        if (empty($update)) { $this->json(['error' => 'No fields to update'], 400); return; }

        // Persist and audit
        if ($type === 'membership') { MembershipApplication::update($id, $update); }
        else { TraineeApplication::update($id, $update); }

        // Diff minimal log
        $changes = [];
        foreach ($update as $k => $v) {
            $old = $current[$k] ?? null;
            if ((string)$old !== (string)$v) { $changes[$k] = ['from' => $old, 'to' => $v]; }
        }
        if (!empty($changes)) {
            AuditLog::create(\App\Core\Auth::id(), $this->getClientIp(), 'admin.update', $type, $id, $changes);
        }

        $this->json(['ok' => true]);
    }
}