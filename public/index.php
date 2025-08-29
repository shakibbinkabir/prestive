<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

use App\Core\Router;
use App\Core\Response;
use App\Controllers\LandingController;
use App\Controllers\AuthController;
use App\Controllers\MembershipController;
use App\Controllers\TraineeController;
use App\Controllers\FileController;
use App\Controllers\ShareController;
use App\Controllers\PagesController;
use App\Controllers\Admin\DashboardController;

// Configure session
session_name(SESSION_NAME);
// Mark cookie secure only when served over HTTPS (or forwarded as https)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Initialize router
$router = new Router();

// Public routes
$router->get('/', [LandingController::class, 'index']);

// Auth routes
$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->post('/admin/logout', [AuthController::class, 'logout']);

// Admin routes
$router->get('/admin/dashboard', [DashboardController::class, 'index']);

// API routes
$router->post('/api/membership/draft/save', [MembershipController::class, 'saveDraft']);
$router->post('/api/trainee/draft/save', [TraineeController::class, 'saveDraft']);
$router->post('/api/upload', [FileController::class, 'upload']);

$router->get('/file/optimized/{id}', [FileController::class, 'optimized']);
$router->get('/file/raw/{id}', [FileController::class, 'raw']);

// Membership
$router->get('/membership/apply', [MembershipController::class, 'applyForm']);
$router->get('/membership/preview', [MembershipController::class, 'preview']);
$router->post('/membership/submit', [MembershipController::class, 'submit']);
$router->post('/membership/share', [MembershipController::class, 'share']);

// Share links
$router->get('/s/{token}', [ShareController::class, 'show']);

// Terms page
$router->get('/terms', [PagesController::class, 'terms']);

// Placeholder routes
$router->get('/trainee/apply', [LandingController::class, 'comingSoon']);

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    if (APP_DEBUG) {
        echo "<h1>Error</h1><pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    } else {
        Response::setStatus(500);
        echo "<h1>Internal Server Error</h1>";
    }
}