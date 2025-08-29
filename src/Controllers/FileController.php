<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Response;
use App\Models\MembershipApplication;
use App\Models\Upload;
use App\Services\UploadService;

class FileController extends Controller
{
    public function upload(): void
    {
        // Rate limit
        $limiter = new RateLimiter();
        if (!$limiter->allow('upload_' . $this->getClientIp(), 60)) {
            $this->json(['error' => 'Too many requests'], 429);
            return;
        }

        // Require CSRF via header
        $this->requireCsrf();

        $ownerType = $_POST['owner_type'] ?? '';
        $ownerId = isset($_POST['owner_id']) ? (int)$_POST['owner_id'] : 0;
        $category = $_POST['category'] ?? '';

        if ($ownerType !== UploadService::OWNER_MEMBERSHIP || $ownerId <= 0) {
            $this->json(['error' => 'Invalid owner'], 400);
            return;
        }

        $app = MembershipApplication::find($ownerId);
        if (!$app || !in_array($app['status'], ['draft','submitted'], true)) {
            $this->json(['error' => 'Owner not found or invalid status'], 404);
            return;
        }

        try {
            $conf = UploadService::validateCategory($category);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Invalid category'], 400);
            return;
        }

        if (!isset($_FILES['file'])) {
            $this->json(['error' => 'No file'], 400);
            return;
        }

        if (!UploadService::canAddMore($ownerType, $ownerId, $category, 1)) {
            $this->json(['error' => 'Upload limit reached for this category'], 400);
            return;
        }

        try {
            $result = UploadService::handleUpload($_FILES['file'], $ownerType, $ownerId, $category);
            $this->json(['ok' => true, 'files' => [$result]]);
        } catch (\Throwable $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    public function optimized(string $id): void
    {
        $upload = Upload::find((int)$id);
        if (!$upload || empty($upload['path_optimized'])) {
            Response::setStatus(404);
            echo 'Not found';
            return;
        }
        $path = __DIR__ . '/../../' . $upload['path_optimized'];
        if (!file_exists($path)) {
            Response::setStatus(404);
            echo 'Not found';
            return;
        }
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($path) ?: 'application/octet-stream';
    Response::setHeader('Content-Type', $mime);
        Response::setHeader('Cache-Control', 'public, max-age=31536000, immutable');
        readfile($path);
    }

    public function raw(string $id): void
    {
        $upload = Upload::find((int)$id);
        if (!$upload || empty($upload['path_raw'])) {
            Response::setStatus(404);
            echo 'Not found';
            return;
        }
        $path = __DIR__ . '/../../' . $upload['path_raw'];
        if (!file_exists($path)) {
            Response::setStatus(404);
            echo 'Not found';
            return;
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path) ?: 'application/octet-stream';
        Response::setHeader('Content-Type', $mime);
        Response::setHeader('Cache-Control', 'private, max-age=86400');
        readfile($path);
    }
}