<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use App\Models\Upload;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Services\AdmissionIdService;

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

    // Phase 4 additions
    public static function countWithFilters(array $filters): int
    {
        [$where, $params] = self::buildWhere($filters);
        $row = DB::fetchOne('SELECT COUNT(*) as cnt FROM trainee_applications ' . $where, $params);
        return (int)($row['cnt'] ?? 0);
    }

    public static function listWithFilters(array $filters, string $sort, string $dir, int $limit, int $offset): array
    {
        [$where, $params] = self::buildWhere($filters);
        $allowedSort = ['id','name','email','status','created_at','submitted_at','admission_id'];
        if (!in_array($sort, $allowedSort, true)) { $sort = 'id'; }
        $dir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);
        $sql = 'SELECT id, name, email, status, created_at, submitted_at, admission_id FROM trainee_applications '
             . $where . " ORDER BY $sort $dir LIMIT $limit OFFSET $offset";
        return DB::fetchAll($sql, $params);
    }

    private static function buildWhere(array $filters): array
    {
        $clauses = [];
        $params = [];
        if (!empty($filters['q'])) {
            $q = trim((string)$filters['q']);
            $clauses[] = '(name LIKE ? OR email LIKE ? OR admission_id = ?)';
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
            $params[] = strtoupper($q);
        }
        if (!empty($filters['status'])) { $clauses[] = 'status = ?'; $params[] = $filters['status']; }
        if (!empty($filters['date_from'])) { $clauses[] = 'created_at >= ?'; $params[] = $filters['date_from'] . ' 00:00:00'; }
        if (!empty($filters['date_to'])) { $clauses[] = 'created_at <= ?'; $params[] = $filters['date_to'] . ' 23:59:59'; }
        if (!empty($filters['email'])) { $clauses[] = 'email LIKE ?'; $params[] = '%' . $filters['email'] . '%'; }
        if (!empty($filters['name'])) { $clauses[] = 'name LIKE ?'; $params[] = '%' . $filters['name'] . '%'; }
        if (!empty($filters['admission_id'])) { $clauses[] = 'admission_id = ?'; $params[] = $filters['admission_id']; }
        if (!empty($filters['bgf_id'])) { $clauses[] = 'bgf_id = ?'; $params[] = $filters['bgf_id']; }
        if (!empty($filters['trainee_type'])) { $clauses[] = 'trainee_type = ?'; $params[] = $filters['trainee_type']; }
        $where = $clauses ? ('WHERE ' . implode(' AND ', $clauses)) : '';
        return [$where, $params];
    }

    public static function findWithUploads(int $id): ?array
    {
        $row = self::find($id);
        if (!$row) return null;
        $row['uploads'] = Upload::findByOwner('trainee', $id);
        $row['payments'] = Payment::listByOwner('trainee', $id);
        return $row;
    }

    public static function transition(int $id, string $toStatus, int $userId, array $meta = []): void
    {
        $current = self::find($id);
        if (!$current) { throw new \RuntimeException('Not found'); }
        $from = $current['status'];
        $allowed = [
            'submitted' => ['payment_received','paid'],
            'payment_received' => ['paid'],
            'paid' => ['confirmed'],
        ];
        $ok = in_array($toStatus, $allowed[$from] ?? [], true);
        $override = false;
        if (!$ok && $from === 'submitted' && $toStatus === 'paid') { $ok = true; $override = true; }
        if (!$ok) { throw new \RuntimeException('Invalid transition'); }

        $update = ['status' => $toStatus];
        if ($toStatus === 'paid') { $update['paid_at'] = date('Y-m-d H:i:s'); }
        DB::update('trainee_applications', $update, 'id = :id', ['id' => $id]);
        AuditLog::create(
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            'status.transition',
            'trainee',
            $id,
            [
                'from' => $from,
                'to' => $toStatus,
                'override' => $override,
                'meta' => $meta,
            ]
        );
    }

    public static function confirmAdmission(int $id, int $userId, string $notes = ''): string
    {
        $svc = new AdmissionIdService();
        return $svc->assign('trainee', $id, $userId, $notes);
    }
}