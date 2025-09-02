<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\MembershipApplication;
use App\Models\TraineeApplication;
use App\Models\Payment;

class ReportsController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        // Simple summary for now
        $summary = [
            'membership_total' => MembershipApplication::countByStatus(),
            'trainee_total' => TraineeApplication::countByStatus(),
            'payments_total' => Payment::countWithFilters([]),
        ];
        $this->render('admin/reports/index', compact('summary'));
    }
}
