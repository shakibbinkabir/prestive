<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

use App\Core\Router;
use App\Core\Response;
use App\Controllers\LandingController;
use App\Controllers\AuthController;
use App\Controllers\MembershipController;
use App\Controllers\TraineeController;
use App\Controllers\Admin\DashboardController;

// Configure session
session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => !APP_DEBUG, // true in production
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

// Placeholder routes
$router->get('/membership/apply', [LandingController::class, 'comingSoon']);
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