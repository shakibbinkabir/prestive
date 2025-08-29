<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\MembershipApplication;
use App\Models\TraineeApplication;

class ExportService
{
    public function membershipsCsv(array $filters): void
    {
        $filename = 'memberships_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        // BOM for Excel
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        $headers = ['id','admission_id','status','created_at','submitted_at','full_name','email','gender','dob','membership_type','mobile','address_present'];
        fputcsv($out, $headers);

        foreach (MembershipApplication::exportWithFilters($filters, 1000) as $row) {
            $csv = [
                $row['id'] ?? '',
                $row['admission_id'] ?? '',
                $row['status'] ?? '',
                $row['created_at'] ?? '',
                $row['submitted_at'] ?? '',
                $row['full_name'] ?? '',
                $row['email'] ?? '',
                $row['gender'] ?? '',
                $row['dob'] ?? '',
                $row['membership_type'] ?? '',
                $row['mobile'] ?? '',
                $row['address_present'] ?? '',
            ];
            fputcsv($out, $csv);
        }
        fclose($out);
    }

    public function traineesCsv(array $filters): void
    {
        $filename = 'trainees_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        $headers = ['id','admission_id','status','created_at','submitted_at','name','email','phone','gender','dob','training_for','trainee_type','bgf_id'];
        fputcsv($out, $headers);

        foreach (TraineeApplication::exportWithFilters($filters, 1000) as $row) {
            $csv = [
                $row['id'] ?? '',
                $row['admission_id'] ?? '',
                $row['status'] ?? '',
                $row['created_at'] ?? '',
                $row['submitted_at'] ?? '',
                $row['name'] ?? '',
                $row['email'] ?? '',
                $row['phone'] ?? '',
                $row['gender'] ?? '',
                $row['dob'] ?? '',
                $row['training_for'] ?? '',
                $row['trainee_type'] ?? '',
                $row['bgf_id'] ?? '',
            ];
            fputcsv($out, $csv);
        }
        fclose($out);
    }
}
