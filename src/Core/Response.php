<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    public static function json(mixed $data, int $status = 200): void
    {
        self::setStatus($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    public static function setStatus(int $code): void
    {
        http_response_code($code);
    }

    public static function setHeader(string $name, string $value): void
    {
        header("$name: $value");
    }
}