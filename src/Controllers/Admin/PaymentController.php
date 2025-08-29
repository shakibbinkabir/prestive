<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\Response;
use App\Models\Payment;
use App\Models\MembershipApplication;
use App\Models\TraineeApplication;
use App\Models\AuditLog;
use App\Core\Auth;

class PaymentController extends Controller
{
    public function create(): void
    {
        $this->requireAdmin();
        $this->requireCsrf();

        $ownerType = ($_POST['owner_type'] ?? '') === 'trainee' ? 'trainee' : 'membership';
        $ownerId = (int)($_POST['owner_id'] ?? 0);
        $paymentDate = $_POST['payment_date'] ?? '';
        $mode = $_POST['mode'] ?? '';
        $amount = (float)($_POST['amount'] ?? 0);
        $currency = strtoupper($_POST['currency'] ?? 'BDT');
        $trxId = $_POST['trx_id'] ?? null;
        $notes = $_POST['notes'] ?? null;

        $v = Validator::make($_POST)
            ->required('owner_type')->in('owner_type', ['membership','trainee'])
            ->required('owner_id')
            ->required('payment_date')->date('payment_date')
            ->required('mode')->in('mode', ['cheque','bank_transfer'])
            ->required('amount');
        if ($v->fails() || $amount <= 0 || !preg_match('/^[A-Z]{3}$/', $currency)) {
            // If not expecting JSON, redirect back with flash
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            if (stripos($accept, 'application/json') === false) {
                $this->flash('error', $v->firstError() ?: 'Invalid input');
                Response::redirect('/admin/applications/' . $ownerType . '/' . $ownerId);
            }
            $this->json(['error' => $v->firstError() ?: 'Invalid input'], 422);
            return;
        }

        $owner = $ownerType === 'membership' ? MembershipApplication::find($ownerId) : TraineeApplication::find($ownerId);
        if (!$owner) { $this->json(['error' => 'Owner not found'], 404); return; }

        $id = Payment::createFull($ownerType, $ownerId, $paymentDate, $mode, $amount, $currency, $trxId, null, Auth::id() ?? 0, $notes);
        AuditLog::create(Auth::id(), $this->getClientIp(), 'payment.created', 'payment', $id, [
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'amount' => $amount,
            'mode' => $mode,
            'currency' => $currency,
        ]);

        // auto-transition if currently submitted
        if (($owner['status'] ?? '') === 'submitted') {
            if ($ownerType === 'membership') {
                MembershipApplication::transition($ownerId, 'payment_received', Auth::id() ?? 0, ['auto' => true, 'payment_id' => $id]);
            } else {
                TraineeApplication::transition($ownerId, 'payment_received', Auth::id() ?? 0, ['auto' => true, 'payment_id' => $id]);
            }
        }

        $this->flash('success', 'Payment recorded');
        // Content negotiation: redirect for form posts, JSON for API/fetch
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (stripos($accept, 'application/json') === false) {
            Response::redirect('/admin/applications/' . $ownerType . '/' . $ownerId);
        }
        $this->json(['ok' => true, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        $this->requireCsrf();
        $payment = Payment::find($id);
        if (!$payment) {
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            if (stripos($accept, 'application/json') === false) {
                $this->flash('error', 'Payment not found');
                // best-effort redirect to applications list
                Response::redirect('/admin/applications');
            }
            $this->json(['error' => 'Not found'], 404); return;
        }
        // Simple hard delete for this phase
        \App\Core\DB::query('DELETE FROM payments WHERE id = ?', [$id]);
        AuditLog::create(Auth::id(), $this->getClientIp(), 'payment.deleted', 'payment', $id, [
            'owner_type' => $payment['owner_type'],
            'owner_id' => (int)$payment['owner_id'],
            'amount' => (float)($payment['amount'] ?? 0),
            'mode' => $payment['mode'],
        ]);
        $this->flash('success', 'Payment deleted');
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (stripos($accept, 'application/json') === false) {
            // redirect back to the owner detail if possible
            $ownerType = $payment['owner_type'];
            $ownerId = (int)$payment['owner_id'];
            Response::redirect('/admin/applications/' . $ownerType . '/' . $ownerId);
        }
        $this->json(['ok' => true]);
    }
}