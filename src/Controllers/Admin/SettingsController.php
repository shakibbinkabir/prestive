<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $this->render('admin/settings/index', [
            'title' => 'Settings',
        ]);
    }
}
