<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

$backupDir = __DIR__ . '/../' . BACKUP_DIR;
if (!is_dir($backupDir)) { @mkdir($backupDir, 0755, true); }

$ts = date('Ymd_His');
$zipPath = $backupDir . "/storage_${ts}.zip";

$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Failed to open ZIP for writing: ${zipPath}\n");
    exit(1);
}

$root = realpath(__DIR__ . '/../storage');
$rootLen = strlen($root) + 1;

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($rii as $file) {
    /** @var SplFileInfo $file */
    if ($file->isDir()) continue;
    $full = $file->getRealPath();
    $rel = substr($full, $rootLen);
    $zip->addFile($full, $rel);
}

$zip->close();
echo "Saved: ${zipPath}\n";
