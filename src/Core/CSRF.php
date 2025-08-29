<?php
declare(strict_types=1);

namespace App\Core;

class CSRF
{
    public static function generateToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    public static function validate(?string $token): bool
    {
        if (!$token || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function field(): string
    {
        $token = self::generateToken();
        return "<input type='hidden' name='_token' value='$token'>";
    }

    public static function meta(): string
    {
        $token = self::generateToken();
        return "<meta name='csrf-token' content='$token'>";
    }
}