<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\View;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function requireAdmin(): void
    {
        if (!Auth::checkAdmin()) {
            if ($this->isJsonRequest()) {
                $this->json(['error' => 'Admin access required'], 401);
            } else {
                Response::redirect('/admin/login');
            }
            exit;
        }
    }

    protected function csrfToken(): string
    {
        return CSRF::generateToken();
    }

    protected function validateCsrf(): bool
    {
        $token = null;
        
        if ($this->isJsonRequest()) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        } else {
            $token = $_POST['_token'] ?? null;
        }
        
        return CSRF::validate($token);
    }

    protected function requireCsrf(): void
    {
        if (!$this->validateCsrf()) {
            if ($this->isJsonRequest()) {
                $this->json(['error' => 'Invalid CSRF token'], 403);
            } else {
                $this->flash('error', 'Invalid security token');
                Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            exit;
        }
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function isJsonRequest(): bool
    {
        return isset($_SERVER['CONTENT_TYPE']) && 
               str_contains($_SERVER['CONTENT_TYPE'], 'application/json');
    }

    protected function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    protected function getClientIp(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
               $_SERVER['HTTP_X_REAL_IP'] ?? 
               $_SERVER['REMOTE_ADDR'] ?? 
               '0.0.0.0';
    }
}