<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ExportService;

class ExportController extends Controller
{
    public function membershipsCsv(): void
    {
        $this->requireAdmin();
        $filters = [];
        foreach (['status','q','date_from','date_to','email','name','admission_id','membership_type'] as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') { $filters[$k] = $_GET[$k]; }
        }
        (new ExportService())->membershipsCsv($filters);
    }

    public function traineesCsv(): void
    {
        $this->requireAdmin();
        $filters = [];
        foreach (['status','q','date_from','date_to','email','name','admission_id','bgf_id','trainee_type'] as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') { $filters[$k] = $_GET[$k]; }
        }
        (new ExportService())->traineesCsv($filters);
    }
}
