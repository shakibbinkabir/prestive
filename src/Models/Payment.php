<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Payment
{
    public static function find(int $id): ?array
    {
        return DB::fetchOne('SELECT * FROM payments WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::insert('payments', $data);
    }

    public static function createFull(
        string $ownerType,
        int $ownerId,
        string $paymentDate,
        string $mode,
        float $amount,
        string $currency = 'BDT',
        ?string $trxId = null,
        ?int $proofUploadId = null,
        ?int $createdByUserId = null,
        ?string $notes = null
    ): int {
        return self::create([
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'payment_date' => $paymentDate,
            'mode' => $mode,
            'amount' => $amount,
            'currency' => $currency,
            'trx_id' => $trxId,
            'proof_upload_id' => $proofUploadId,
            'created_by_user_id' => $createdByUserId ?? 0,
            'notes' => $notes,
        ]);
    }

    /**
     * Spec-conform create() convenience wrapper.
     */
    public static function createSimple(
        string $ownerType,
        int $ownerId,
        string $paymentDate,
        string $mode,
        float $amount,
        string $currency = 'BDT',
        ?string $trxId = null,
        ?int $proofUploadId = null,
        ?int $createdByUserId = null,
        ?string $notes = null
    ): int {
        return self::createFull($ownerType, $ownerId, $paymentDate, $mode, $amount, $currency, $trxId, $proofUploadId, $createdByUserId, $notes);
    }

    public static function listByOwner(string $ownerType, int $ownerId): array
    {
        return DB::fetchAll('SELECT * FROM payments WHERE owner_type = ? AND owner_id = ? ORDER BY payment_date DESC, id DESC', [$ownerType, $ownerId]);
    }

    public static function listWithFilters(array $filters, int $limit, int $offset): array
    {
        $where = [];
        $params = [];
        if (!empty($filters['owner_type'])) { $where[] = 'owner_type = ?'; $params[] = $filters['owner_type']; }
        if (!empty($filters['q'])) {
            $where[] = '(CAST(owner_id AS CHAR) LIKE ? OR trx_id LIKE ?)';
            $params[] = '%' . $filters['q'] . '%';
            $params[] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['date_from'])) { $where[] = 'payment_date >= ?'; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to'])) { $where[] = 'payment_date <= ?'; $params[] = $filters['date_to']; }
        if (!empty($filters['mode'])) { $where[] = 'mode = ?'; $params[] = $filters['mode']; }
        if (!empty($filters['currency'])) { $where[] = 'currency = ?'; $params[] = $filters['currency']; }
        $sql = 'SELECT * FROM payments';
        if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
        $sql .= ' ORDER BY payment_date DESC, id DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
        return DB::fetchAll($sql, $params);
    }

    public static function countWithFilters(array $filters): int
    {
        $where = [];
        $params = [];
        if (!empty($filters['owner_type'])) { $where[] = 'owner_type = ?'; $params[] = $filters['owner_type']; }
        if (!empty($filters['q'])) {
            $where[] = '(CAST(owner_id AS CHAR) LIKE ? OR trx_id LIKE ?)';
            $params[] = '%' . $filters['q'] . '%';
            $params[] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['date_from'])) { $where[] = 'payment_date >= ?'; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to'])) { $where[] = 'payment_date <= ?'; $params[] = $filters['date_to']; }
        if (!empty($filters['mode'])) { $where[] = 'mode = ?'; $params[] = $filters['mode']; }
        if (!empty($filters['currency'])) { $where[] = 'currency = ?'; $params[] = $filters['currency']; }
        $sql = 'SELECT COUNT(*) AS c FROM payments';
        if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
        $row = DB::fetchOne($sql, $params) ?: ['c' => 0];
        return (int)$row['c'];
    }
}