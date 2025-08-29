<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Upload
{
    public static function find(int $id): ?array
    {
        return DB::fetchOne('SELECT * FROM uploads WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::insert('uploads', $data);
    }

    public static function findByOwner(string $ownerType, int $ownerId): array
    {
        $rows = DB::fetchAll('SELECT * FROM uploads WHERE owner_type = ? AND owner_id = ? ORDER BY id DESC', [$ownerType, $ownerId]);
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['category']][] = $row;
        }
        return $grouped;
    }

    public static function countByOwnerCategory(string $ownerType, int $ownerId, string $category): int
    {
        $row = DB::fetchOne('SELECT COUNT(*) as cnt FROM uploads WHERE owner_type = ? AND owner_id = ? AND category = ?', [$ownerType, $ownerId, $category]);
        return (int)($row['cnt'] ?? 0);
    }
}