<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class User
{
    public static function find(int $id): ?array
    {
        return DB::fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return DB::fetchOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::insert('users', $data);
    }

    public static function updateLastLogin(int $id): void
    {
    DB::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $id]);
    }
}