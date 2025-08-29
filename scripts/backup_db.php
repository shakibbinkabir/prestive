<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function e(string $s): void { fwrite(STDERR, $s . PHP_EOL); }

$host = DB_HOST;
$port = DB_PORT;
$db = DB_NAME;
$user = DB_USER;
$pass = DB_PASS;
$backupDir = __DIR__ . '/../' . BACKUP_DIR;
if (!is_dir($backupDir)) { @mkdir($backupDir, 0755, true); }

$ts = date('Ymd_His');
$tmpSql = $backupDir . "/db_${ts}.sql";
$outGz = $backupDir . "/db_${ts}.sql.gz";

// Build mysqldump command; quote args for Windows PowerShell/cmd compatibility
$envPass = getenv('MYSQL_PWD');
putenv('MYSQL_PWD=' . $pass);
$cmd = sprintf('mysqldump -h %s -P %s -u %s %s > "%s"', escapeshellarg($host), escapeshellarg((string)$port), escapeshellarg($user), escapeshellarg($db), $tmpSql);
$ret = 0;
system($cmd, $ret);
// Restore previous env
if ($envPass !== false) { putenv('MYSQL_PWD=' . $envPass); } else { putenv('MYSQL_PWD'); }

if ($ret !== 0 || !file_exists($tmpSql)) {
    e('mysqldump failed. Ensure mysqldump is in PATH and credentials are correct.');
    exit(1);
}

// Gzip using zlib
$in = fopen($tmpSql, 'rb');
$out = gzopen($outGz, 'wb9');
if (!$in || !$out) {
    e('Failed to open files for compression');
    @unlink($tmpSql);
    exit(1);
}
while (!feof($in)) {
    $buf = fread($in, 1024 * 1024);
    if ($buf === false) break;
    gzwrite($out, $buf);
}
fclose($in);
gzclose($out);
@unlink($tmpSql);
echo "Saved: ${outGz}\n";
