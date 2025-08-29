<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\MembershipApplication;
use App\Models\TraineeApplication;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        
        $stats = [
            'membership_total' => MembershipApplication::countByStatus(),
            'membership_drafts' => MembershipApplication::countByStatus('draft'),
            'membership_submitted' => MembershipApplication::countByStatus('submitted'),
            'membership_payment_received' => MembershipApplication::countByStatus('payment_received'),
            'membership_paid' => MembershipApplication::countByStatus('paid'),
            'membership_confirmed' => MembershipApplication::countByStatus('confirmed'),
            'trainee_total' => TraineeApplication::countByStatus(),
            'trainee_drafts' => TraineeApplication::countByStatus('draft'),
            'trainee_submitted' => TraineeApplication::countByStatus('submitted'),
            'trainee_payment_received' => TraineeApplication::countByStatus('payment_received'),
            'trainee_paid' => TraineeApplication::countByStatus('paid'),
            'trainee_confirmed' => TraineeApplication::countByStatus('confirmed'),
        ];
        
        $this->render('admin/dashboard', compact('stats'));
    }
}