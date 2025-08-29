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
}