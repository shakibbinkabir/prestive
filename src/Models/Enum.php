<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Enum
{
    public static function getByTable(string $table): array
    {
        return DB::fetchAll("SELECT * FROM $table WHERE active = 1 ORDER BY sort_order, label");
    }

    public static function getMembershipTypes(): array
    {
        return self::getByTable('membership_types');
    }

    public static function getGenders(): array
    {
        return self::getByTable('genders');
    }

    public static function getReligions(): array
    {
        return self::getByTable('religions');
    }

    public static function getMaritalStatuses(): array
    {
        return self::getByTable('marital_statuses');
    }

    public static function getBloodGroups(): array
    {
        return self::getByTable('blood_groups');
    }
}