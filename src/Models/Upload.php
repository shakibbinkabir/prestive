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
}