<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
// Use safeLoad so the app can run with defaults if .env is missing during first-time setup
$dotenv->safeLoad();

// Configuration constants
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('APP_URL', $_ENV['APP_URL'] ?? 'https://join.prestive.club');

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? '127.0.0.1');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'prestive');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Session configuration
define('SESSION_NAME', $_ENV['SESSION_NAME'] ?? 'prestive_session');

// Admin configuration
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com');
define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD'] ?? 'ChangeMe123!');

// Ensure storage directories exist
$storageDirs = ['storage/raw', 'storage/optimized', 'storage/logs'];
foreach ($storageDirs as $dir) {
    if (!is_dir(__DIR__ . '/../' . $dir)) {
        mkdir(__DIR__ . '/../' . $dir, 0755, true);
    }
}