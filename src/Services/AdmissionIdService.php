<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use PDO;

class AdmissionIdService
{
    /**
     * Generate a unique 6-char alphanumeric admission ID (uppercase) for a target type.
     */
    public function generate(string $targetType): string
    {
        $targetType = strtolower($targetType);
        if (!in_array($targetType, ['membership','trainee'], true)) {
            throw new \InvalidArgumentException('Invalid targetType');
        }

        $tries = 0;
        while ($tries < 10) {
            $tries++;
            $id = strtoupper(self::randomId(6));
            if ($this->isUnique($targetType, $id)) {
                return $id;
            }
        }
        throw new \RuntimeException('Failed to generate unique admission ID after multiple attempts');
    }

    /**
     * Assign admission id and confirm the application atomically.
     * Returns the assigned admission_id.
     */
    public function assign(string $targetType, int $targetId, int $userId, string $notes = ''): string
    {
        $targetType = strtolower($targetType);
        if (!in_array($targetType, ['membership','trainee'], true)) {
            throw new \InvalidArgumentException('Invalid targetType');
        }

        $pdo = DB::getInstance();
        $pdo->beginTransaction();
        try {
            // Lock row FOR UPDATE to avoid race
            $table = $targetType === 'membership' ? 'membership_applications' : 'trainee_applications';
            $row = DB::fetchOne("SELECT id, status, admission_id FROM {$table} WHERE id = ? FOR UPDATE", [$targetId]);
            if (!$row) {
                throw new \RuntimeException('Target not found');
            }

            $admissionId = $row['admission_id'] ?? null;
            if (!$admissionId) {
                $admissionId = $this->generate($targetType);
            }

            $now = date('Y-m-d H:i:s');
            // Set confirmed status and timestamps
            $update = [
                'admission_id' => $admissionId,
                'status' => 'confirmed',
                'confirmed_at' => $now,
                'ad2_confirmed_at' => $now,
                'ad2_confirmed_by_user_id' => $userId,
                'ad2_notes' => $notes,
                'updated_at' => $now,
            ];

            DB::update($table, $update, 'id = :id', ['id' => $targetId]);

            $pdo->commit();
            return $admissionId;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $e;
        }
    }

    private static function randomId(int $length): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // avoid confusing chars
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $out;
    }

    private function isUnique(string $targetType, string $admissionId): bool
    {
        $table = $targetType === 'membership' ? 'membership_applications' : 'trainee_applications';
        $row = DB::fetchOne("SELECT id FROM {$table} WHERE admission_id = ? LIMIT 1", [$admissionId]);
        return $row === null;
    }
}