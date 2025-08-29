<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\ShareLink;
use App\Models\MembershipApplication;
use App\Models\TraineeApplication;
use App\Services\PdfService;

class PdfController extends Controller
{
    public function share(string $token): void
    {
        $link = ShareLink::findByToken($token);
        if (!$link) { Response::setStatus(404); echo 'Not found'; return; }
        $type = $link['target_type'] === 'trainee' ? 'trainee' : 'membership';
        $id = (int)$link['target_id'];
        $app = $type === 'membership' ? MembershipApplication::findWithUploads($id) : TraineeApplication::findWithUploads($id);
        if (!$app) { Response::setStatus(404); echo 'Not found'; return; }
        // Hide admin-only fields
        unset($app['payments']);
        unset($app['draft_data']);
        $uploads = $app['uploads'] ?? [];
        $pdf = (new PdfService())->renderApplication($type, $app, $uploads);
        $slug = $app['admission_id'] ?? ($app['status'] ?? 'shared');
        $filename = $type . '-' . $app['id'] . '-' . $slug . '.pdf';
        (new PdfService())->streamDownload($filename, $pdf);
    }
}
