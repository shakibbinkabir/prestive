<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class ShareLink
{
    public static function find(int $id): ?array
    {
        return DB::fetchOne('SELECT * FROM share_links WHERE id = ?', [$id]);
    }

    public static function findByToken(string $token): ?array
    {
        return DB::fetchOne('SELECT * FROM share_links WHERE token = ?', [$token]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::insert('share_links', $data);
    }

    public static function createFor(string $type, int $id, ?int $userId): string
    {
        $token = \Ramsey\Uuid\Uuid::uuid4()->toString();
        self::create([
            'token' => $token,
            'target_type' => $type,
            'target_id' => $id,
            'created_by_user_id' => $userId
        ]);
        return $token;
    }
}