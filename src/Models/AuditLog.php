<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class AuditLog
{
    public static function create(?int $actorUserId, ?string $actorIp, string $action, string $targetType, ?int $targetId, array $changes = []): void
    {
        DB::insert('audit_logs', [
            'actor_user_id' => $actorUserId,
            'actor_ip' => $actorIp,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'changes_json' => !empty($changes) ? json_encode($changes, JSON_UNESCAPED_UNICODE) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function listForTarget(string $targetType, int $targetId, int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, min(200, $limit));
        $offset = max(0, $offset);
        return DB::fetchAll(
            'SELECT * FROM audit_logs WHERE target_type = ? AND target_id = ? ORDER BY id DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset,
            [$targetType, $targetId]
        );
    }
}