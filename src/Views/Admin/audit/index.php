<?php declare(strict_types=1); ?>
<?php $embedded = $embedded ?? false; ?>
<?php if (!$embedded): ?>
<div class="p-6 text-gray-100">
  <h1 class="text-2xl font-bold mb-4">Audit Logs</h1>
<?php endif; ?>
  <div class="space-y-2">
    <?php foreach ($logs as $log): ?>
      <div class="bg-gray-900 rounded p-3 text-sm">
        <div class="flex justify-between">
          <div>
            <span class="text-gray-400">#<?= (int)$log['id'] ?></span>
            <span class="ml-2 px-2 py-0.5 rounded bg-gray-800"><?= htmlspecialchars($log['action']) ?></span>
            <span class="ml-2 text-gray-400">Actor: <?= htmlspecialchars((string)($log['actor_user_id'] ?? 'system')) ?> (<?= htmlspecialchars($log['actor_ip'] ?? '-') ?>)</span>
          </div>
          <div class="text-gray-400"><?= htmlspecialchars($log['created_at']) ?></div>
        </div>
        <?php if (!empty($log['changes_json'])): ?>
          <pre class="mt-2 bg-black/40 p-2 rounded overflow-auto"><?= htmlspecialchars(json_encode(json_decode($log['changes_json'], true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) ?></pre>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php if (!$embedded): ?>
</div>
<?php endif; ?>