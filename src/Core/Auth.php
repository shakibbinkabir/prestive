<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    public static function login(string $email, string $password): bool
    {
        $user = User::findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        
        // Prevent session fixation
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = (bool) $user['is_admin'];
        
        // Update last login
        User::updateLastLogin($user['id']);
        
        return true;
    }

    public static function logout(): void
    {
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return User::find($_SESSION['user_id']);
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function checkAdmin(): bool
    {
        return self::check() && ($_SESSION['is_admin'] ?? false);
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}