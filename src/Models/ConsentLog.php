<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class ConsentLog
{
    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::insert('consent_logs', $data);
    }

    public static function log(string $targetType, int $targetId, string $termsVersion, string $consentText, string $ip, string $userAgent): void
    {
        self::create([
            'target_type' => $targetType,
            'target_id' => $targetId,
            'terms_version' => $termsVersion,
            'consent_text_snapshot' => $consentText,
            'ip' => $ip,
            'user_agent' => $userAgent
        ]);
    }
}