<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Response;
use App\Models\MembershipApplication;
use App\Models\TraineeApplication;
use App\Services\PdfService;

class PdfController extends Controller
{
    public function application(string $type, int $id): void
    {
        $this->requireAdmin();
        $type = $type === 'trainee' ? 'trainee' : 'membership';
        $app = $type === 'membership' ? MembershipApplication::findWithUploads($id) : TraineeApplication::findWithUploads($id);
        if (!$app) {
            Response::setStatus(404);
            echo 'Not found';
            return;
        }
        // Exclude any sensitive internal fields by default
        unset($app['draft_data']);
        $uploads = $app['uploads'] ?? [];
        $pdf = (new PdfService())->renderApplication($type, $app, $uploads);
        $slug = $app['admission_id'] ?? ($app['status'] ?? 'unknown');
        $filename = $type . '-' . $app['id'] . '-' . $slug . '.pdf';
        (new PdfService())->streamDownload($filename, $pdf);
    }
}
