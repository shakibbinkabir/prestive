<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class TraineeApplication
{
    public static function find(int $id): ?array
    {
        return DB::fetchOne('SELECT * FROM trainee_applications WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::insert('trainee_applications', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
    DB::update('trainee_applications', $data, 'id = :id', ['id' => $id]);
    }

    // Phase 3 helpers
    public static function createDraft(array $data, string $ip): int
    {
        $data = array_merge($data, [
            'status' => 'draft',
            'created_ip' => $ip,
        ]);
        return self::create($data);
    }

    public static function updateDraft(int $id, array $data): void
    {
        self::update($id, $data);
    }

    public static function updateFieldsOnSubmit(int $id, array $fields): void
    {
        self::update($id, $fields);
    }

    public static function countByStatus(string $status = null): int
    {
        if ($status) {
            $result = DB::fetchOne('SELECT COUNT(*) as count FROM trainee_applications WHERE status = ?', [$status]);
        } else {
            $result = DB::fetchOne('SELECT COUNT(*) as count FROM trainee_applications');
        }
        
        return (int) $result['count'];
    }
}