<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class MembershipApplication
{
    public static function find(int $id): ?array
    {
        $row = DB::fetchOne('SELECT * FROM membership_applications WHERE id = ?', [$id]);
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::insert('membership_applications', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
    DB::update('membership_applications', $data, 'id = :id', ['id' => $id]);
    }

    public static function createDraft(array $data, string $ip): int
    {
        $data = array_merge($data, [
            'status' => 'draft',
            'created_ip' => $ip
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
            $result = DB::fetchOne('SELECT COUNT(*) as count FROM membership_applications WHERE status = ?', [$status]);
        } else {
            $result = DB::fetchOne('SELECT COUNT(*) as count FROM membership_applications');
        }
        
        return (int) $result['count'];
    }

    // Phase 3: BGF lookup for trainee autofill
    public static function findConfirmedByBGF(string $bgfId): ?array
    {
        $row = DB::fetchOne(
            'SELECT * FROM membership_applications WHERE status = "confirmed" AND confirmed_bgf_id = ? ORDER BY confirmed_at DESC, id DESC LIMIT 1',
            [$bgfId]
        );
        if (!$row) return null;
        // Map minimal fields for autofill
        return [
            'full_name' => $row['full_name'] ?? null,
            'dob' => $row['dob'] ?? null,
            'gender' => $row['gender'] ?? null,
            'religion' => $row['religion'] ?? null,
            'blood_group' => $row['blood_group'] ?? null,
            'email' => $row['email'] ?? null,
            'mobile' => $row['mobile'] ?? null,
            'address_present' => $row['address_present'] ?? null,
            'club_name' => $row['organization'] ?? null,
            'membership_no' => $row['confirmed_bgf_id'] ?? null,
        ];
    }
}