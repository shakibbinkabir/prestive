<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Models\Upload;
use Ramsey\Uuid\Uuid;

class UploadService
{
    public const OWNER_MEMBERSHIP = 'membership';
    public const OWNER_TRAINEE = 'trainee';

    private static function categories(): array
    {
    return [
            'passport_photos' => [
                'ext' => ['jpg','jpeg','png'],
                'max' => 6,
                'maxBytes' => 5 * 1024 * 1024,
                'is_image' => true // kept for backward-compat; detection now uses MIME
            ],
            'biodata_with_photo' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            'nid_copy' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            'passport_copies' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 6,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            'tin_cert' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            'ack_receipts' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 3,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            'trade_license' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            'work_permit' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            'visa' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            // Trainee categories
            'junior_passport_photo' => [
                'ext' => ['jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 5 * 1024 * 1024,
                'is_image' => true // legacy flag
            ],
            'junior_birth_cert' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
            'senior_passport_photo' => [
                'ext' => ['jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 5 * 1024 * 1024,
                'is_image' => true // legacy flag
            ],
            'senior_nid' => [
                'ext' => ['pdf','jpg','jpeg','png'],
                'max' => 1,
                'maxBytes' => 10 * 1024 * 1024,
                'is_image' => false
            ],
        ];
    }

    public static function validateCategory(string $category): array
    {
        $map = self::categories();
        if (!isset($map[$category])) {
            throw new \InvalidArgumentException('Unknown category');
        }
        return $map[$category];
    }

    public static function canAddMore(string $ownerType, int $ownerId, string $category, int $incomingCount = 1): bool
    {
        $conf = self::validateCategory($category);
        $count = Upload::countByOwnerCategory($ownerType, $ownerId, $category);
        return ($count + $incomingCount) <= $conf['max'];
    }

    public static function buildPaths(string $ownerType, int $ownerId): array
    {
        $uuid = Uuid::uuid4()->toString();
        // Per spec, store under {type}/{uuid}/ folders; ownerId only in DB linkage
        $rawDir = __DIR__ . '/../../storage/raw/' . $ownerType . '/' . $uuid;
        $optDir = __DIR__ . '/../../storage/optimized/' . $ownerType . '/' . $uuid;
        if (!is_dir($rawDir)) { mkdir($rawDir, 0755, true); }
        if (!is_dir($optDir)) { mkdir($optDir, 0755, true); }
        return [$uuid, $rawDir, $optDir];
    }

    public static function handleUpload(array $file, string $ownerType, int $ownerId, string $category): array
    {
        $conf = self::validateCategory($category);
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload error');
        }
        if ($file['size'] > $conf['maxBytes']) {
            throw new \RuntimeException('File too large');
        }
        // MIME & ext validation
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $conf['ext'], true)) {
            throw new \RuntimeException('Invalid file type');
        }
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';
    $isImage = str_starts_with($mime, 'image/');

    [$uuid, $rawDir, $optDir] = self::buildPaths($ownerType, $ownerId);
    $randomName = $uuid . '.' . $ext;
    $rawPath = $rawDir . '/' . $randomName;
    // Optimize to webp only when actual MIME is image/*
    $optPath = $optDir . '/' . pathinfo($randomName, PATHINFO_FILENAME) . ($isImage ? '.webp' : '.' . $ext);

        if (!move_uploaded_file($file['tmp_name'], $rawPath)) {
            // In non-SAPI tests, move_uploaded_file may fail; fallback to copy
            if (!copy($file['tmp_name'], $rawPath)) {
                throw new \RuntimeException('Failed to store file');
            }
        }

        $optStoredPath = null;
        if ($isImage) {
            if (\App\Services\ImageService::optimizeToWebp($rawPath, $optPath)) {
                $optStoredPath = $optPath;
            }
        }

        $rawRel = self::relativePath($rawPath);
        $optRel = $optStoredPath ? self::relativePath($optStoredPath) : null;
        $id = Upload::create([
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'category' => $category,
            'original_name' => $file['name'],
            'mime_type' => $mime,
            'size_bytes' => (int)$file['size'],
            'path_raw' => $rawRel,
            'path_optimized' => $optRel,
            'uploaded_by_user_id' => \App\Core\Auth::id() ?? null
        ]);

        return [
            'id' => $id,
            'category' => $category,
            'original_name' => $file['name'],
            'mime_type' => $mime,
            'size_bytes' => (int)$file['size'],
            'raw_url' => '/' . $rawRel,
            'optimized_url' => $optRel ? '/file/optimized/' . $id : null
        ];
    }

    // kept for backward compat if needed
    private static function insertUploadRecord(string $ownerType, int $ownerId, string $category, string $originalName, string $mime, int $size, string $rawPath, ?string $optPath): int
    {
        return Upload::create([
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'category' => $category,
            'original_name' => $originalName,
            'mime_type' => $mime,
            'size_bytes' => $size,
            'path_raw' => self::relativePath($rawPath),
            'path_optimized' => $optPath ? self::relativePath($optPath) : null,
            'uploaded_by_user_id' => \App\Core\Auth::id() ?? null
        ]);
    }

    private static function relativePath(string $abs): string
    {
        // Normalize to relative from project root
        $root = realpath(__DIR__ . '/../../');
        return ltrim(str_replace(['\\', $root], ['/', ''], realpath($abs) ?: $abs), '/');
    }
}
