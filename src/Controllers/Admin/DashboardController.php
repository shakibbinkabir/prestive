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
            'trainee_total' => TraineeApplication::countByStatus(),
            'trainee_drafts' => TraineeApplication::countByStatus('draft'),
            'trainee_submitted' => TraineeApplication::countByStatus('submitted')
        ];
        
        $this->render('admin/dashboard', compact('stats'));
    }
}