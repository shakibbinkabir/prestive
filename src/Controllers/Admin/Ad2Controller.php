<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\MembershipApplication;
use App\Models\TraineeApplication;
use App\Models\AuditLog;
use App\Services\AdmissionIdService;

class Ad2Controller extends Controller
{
    public function confirm(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $ownerType = ($_POST['owner_type'] ?? '') === 'trainee' ? 'trainee' : 'membership';
        $ownerId = (int)($_POST['owner_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        $owner = $ownerType === 'membership' ? MembershipApplication::find($ownerId) : TraineeApplication::find($ownerId);
        if (!$owner) {
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            if (stripos($accept, 'application/json') === false) {
                $this->flash('error', 'Not found');
                \App\Core\Response::redirect('/admin/applications');
            }
            $this->json(['error' => 'Not found'], 404); return;
        }
        if (($owner['status'] ?? '') !== 'paid') {
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            if (stripos($accept, 'application/json') === false) {
                $this->flash('error', 'Only paid applications can be confirmed');
                \App\Core\Response::redirect('/admin/applications/' . $ownerType . '/' . $ownerId);
            }
            $this->json(['error' => 'Only paid applications can be confirmed'], 400); return;
        }
        $svc = new AdmissionIdService();
        $admissionId = $svc->assign($ownerType, $ownerId, Auth::id() ?? 0, $notes);
        AuditLog::create(Auth::id(), $this->getClientIp(), 'ad2.confirmed', $ownerType, $ownerId, [
            'admission_id' => $admissionId,
            'notes' => $notes,
        ]);
        $this->flash('success', 'Ad-2 confirmed: ' . $admissionId);
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (stripos($accept, 'application/json') === false) {
            \App\Core\Response::redirect('/admin/applications/' . $ownerType . '/' . $ownerId);
        }
        $this->json(['ok' => true, 'admission_id' => $admissionId]);
    }
}