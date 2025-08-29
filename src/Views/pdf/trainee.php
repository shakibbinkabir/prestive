<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title><?= htmlspecialchars($title) ?><?= !empty($app['admission_id']) ? (' - ' . htmlspecialchars($app['admission_id'])) : '' ?></title>
  <style>
    @page { margin: 12mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    h1 { font-size: 18px; margin: 0 0 8px; }
    .muted { color: #666; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { text-align: left; padding: 6px 8px; vertical-align: top; }
    tr:nth-child(even) td { background: #f5f5f5; }
    .section { margin-top: 12px; }
    .badge { display: inline-block; padding: 2px 6px; background: #eee; border-radius: 3px; }
  </style>
  </head>
<body>
  <h1><?= htmlspecialchars($title) ?></h1>
  <div class="muted">
    ID #<?= (int)($app['id'] ?? 0) ?>
    <?php if (!empty($app['admission_id'])): ?> • Admission: <span class="badge"><?= htmlspecialchars($app['admission_id']) ?></span><?php endif; ?>
    • Status: <?= htmlspecialchars($app['status'] ?? '') ?>
    • Created: <?= htmlspecialchars($app['created_at'] ?? '') ?>
  </div>

  <div class="section">
    <table>
      <tbody>
        <tr><th>Name</th><td><?= htmlspecialchars($app['name'] ?? '') ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($app['email'] ?? '') ?></td></tr>
        <tr><th>Phone</th><td><?= htmlspecialchars($app['phone'] ?? '') ?></td></tr>
        <tr><th>Gender</th><td><?= htmlspecialchars($app['gender'] ?? '') ?></td></tr>
        <tr><th>Date of Birth</th><td><?= htmlspecialchars($app['dob'] ?? '') ?></td></tr>
        <tr><th>Training For</th><td><?= htmlspecialchars($app['training_for'] ?? '') ?></td></tr>
        <tr><th>Trainee Type</th><td><?= htmlspecialchars($app['trainee_type'] ?? '') ?></td></tr>
        <?php if (($app['trainee_type'] ?? '') === 'senior'): ?>
          <tr><th>BGF ID</th><td><?= htmlspecialchars($app['bgf_id'] ?? '') ?></td></tr>
        <?php endif; ?>
        <tr><th>Address (Present)</th><td><?= htmlspecialchars($app['address_present'] ?? '') ?></td></tr>
        <tr><th>Address (Permanent)</th><td><?= htmlspecialchars($app['address_permanent'] ?? '') ?></td></tr>
      </tbody>
    </table>
  </div>

  <?php if (!empty($uploads) && is_array($uploads)): ?>
  <div class="section">
    <h2 style="font-size:14px; margin:0 0 6px;">Uploads</h2>
    <table>
      <thead><tr><th style="width:30%">Category</th><th>Files</th></tr></thead>
      <tbody>
        <?php foreach ($uploads as $cat => $items): ?>
          <tr>
            <td><?= htmlspecialchars((string)$cat) ?></td>
            <td>
              <?php $names = array_map(fn($u) => (string)($u['original_name'] ?? ('#'.$u['id'])), (array)$items); echo htmlspecialchars(implode(', ', $names)); ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
  <div class="muted" style="margin-top:16px;">Generated on <?= date('Y-m-d H:i') ?></div>
</body>
</html>
