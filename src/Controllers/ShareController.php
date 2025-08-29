<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ShareLink;
use App\Models\MembershipApplication;
use App\Models\Upload;

class ShareController extends Controller
{
    public function show(string $token): void
    {
        $link = ShareLink::findByToken($token);
        if (!$link || $link['target_type'] !== 'membership') {
            $this->render('landing', ['title' => 'Link invalid', 'content' => '<div class="p-8">Invalid or expired link.</div>']);
            return;
        }
        $app = MembershipApplication::find((int)$link['target_id']);
        if (!$app) {
            $this->render('landing', ['title' => 'Not found', 'content' => '<div class="p-8">Application not found.</div>']);
            return;
        }
        $uploads = Upload::findByOwner('membership', (int)$link['target_id']);
        // Merge any draft_data for consistent read-only rendering
        $merged = $app;
        if (!empty($app['draft_data'])) {
            $draft = json_decode($app['draft_data'], true) ?: [];
            foreach ($draft as $k => $v) {
                if ($v !== null && $v !== '') {
                    $merged[$k] = $v;
                }
            }
        }
        $this->render('membership/preview', [
            'title' => 'Shared Preview',
            'application' => $app,
            'data' => $merged,
            'uploads' => $uploads,
            'is_share_view' => true
        ]);
    }
}