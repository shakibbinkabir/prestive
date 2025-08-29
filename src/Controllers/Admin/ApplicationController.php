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
}