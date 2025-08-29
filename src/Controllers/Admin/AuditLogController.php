<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function list(string $type, int $id): void
    {
        $this->requireAdmin();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        $logs = AuditLog::listForTarget($type, $id, $perPage, $offset);
        $this->render('admin/audit/index', [
            'title' => 'Audit Logs',
            'type' => $type,
            'targetId' => $id,
            'logs' => $logs,
            'page' => $page,
        ]);
    }
}