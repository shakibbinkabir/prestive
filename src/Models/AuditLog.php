<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class AuditLog
{
    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::insert('audit_logs', $data);
    }

    public static function log(string $action, string $targetType, ?int $targetId = null, ?int $actorUserId = null, ?array $changes = null, ?string $actorIp = null): void
    {
        self::create([
            'actor_user_id' => $actorUserId,
            'actor_ip' => $actorIp,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'changes_json' => $changes ? json_encode($changes) : null
        ]);
    }
}