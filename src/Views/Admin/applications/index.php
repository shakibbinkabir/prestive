<?php declare(strict_types=1); ?>
<div class="p-6 text-gray-100">
  <h1 class="text-2xl font-bold mb-4">Admin Applications</h1>
  <div class="mb-4 flex items-center gap-2">
    <a href="/admin/applications?type=membership" class="px-3 py-1 rounded <?= $type==='membership'?'bg-yellow-600 text-black':'bg-gray-700' ?>">Membership</a>
    <a href="/admin/applications?type=trainee" class="px-3 py-1 rounded <?= $type==='trainee'?'bg-yellow-600 text-black':'bg-gray-700' ?>">Trainee</a>
  <form method="get" action="/admin/applications" class="ml-auto flex gap-2 items-end">
      <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>" />
      <div>
        <label class="text-sm">Status</label>
        <select name="status" class="bg-gray-800 border border-gray-700 rounded p-1">
          <option value="">Any</option>
          <?php foreach (['draft','submitted','payment_received','paid','confirmed'] as $st): ?>
            <option value="<?= $st ?>" <?= ($filters['status']??'')===$st?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="text-sm">From</label>
        <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1" />
      </div>
      <div>
        <label class="text-sm">To</label>
        <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1" />
      </div>
      <div>
        <label class="text-sm">Search</label>
        <input type="text" name="q" placeholder="name/email/admission" value="<?= htmlspecialchars($filters['q']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1" />
      </div>
      <?php if ($type==='membership'): ?>
      <div>
        <label class="text-sm">Membership Type</label>
        <input type="text" name="membership_type" value="<?= htmlspecialchars($filters['membership_type']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1" />
      </div>
      <?php else: ?>
      <div>
        <label class="text-sm">BGF ID</label>
        <input type="text" name="bgf_id" value="<?= htmlspecialchars($filters['bgf_id']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1" />
      </div>
      <?php endif; ?>
      <button class="bg-yellow-600 text-black px-3 py-1 rounded">Apply</button>
    </form>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full table-auto text-sm">
      <thead class="bg-gray-800">
        <tr>
          <th class="p-2 text-left"><a href="?type=<?= $type ?>&sort=id&dir=<?= $dir==='asc'?'desc':'asc' ?>">ID</a></th>
          <th class="p-2 text-left"><a href="?type=<?= $type ?>&sort=<?= $type==='membership'?'full_name':'name' ?>&dir=<?= $dir==='asc'?'desc':'asc' ?>">Name</a></th>
          <th class="p-2 text-left"><a href="?type=<?= $type ?>&sort=email&dir=<?= $dir==='asc'?'desc':'asc' ?>">Email</a></th>
          <th class="p-2 text-left"><a href="?type=<?= $type ?>&sort=status&dir=<?= $dir==='asc'?'desc':'asc' ?>">Status</a></th>
          <th class="p-2 text-left"><a href="?type=<?= $type ?>&sort=created_at&dir=<?= $dir==='asc'?'desc':'asc' ?>">Created</a></th>
          <th class="p-2 text-left"><a href="?type=<?= $type ?>&sort=submitted_at&dir=<?= $dir==='asc'?'desc':'asc' ?>">Submitted</a></th>
          <th class="p-2 text-left"><a href="?type=<?= $type ?>&sort=admission_id&dir=<?= $dir==='asc'?'desc':'asc' ?>">Admission ID</a></th>
          <th class="p-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr class="border-b border-gray-800">
            <td class="p-2">#<?= (int)$r['id'] ?></td>
            <td class="p-2"><?= htmlspecialchars($r['full_name'] ?? $r['name'] ?? '') ?></td>
            <td class="p-2"><?= htmlspecialchars($r['email'] ?? '') ?></td>
            <td class="p-2"><span class="px-2 py-0.5 rounded bg-gray-700"><?= htmlspecialchars($r['status']) ?></span></td>
            <td class="p-2 text-gray-400"><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
            <td class="p-2 text-gray-400"><?= htmlspecialchars($r['submitted_at'] ?? '') ?></td>
            <td class="p-2"><?= htmlspecialchars($r['admission_id'] ?? '') ?></td>
            <td class="p-2">
              <a class="text-yellow-500 hover:underline" href="/admin/applications/<?= $type ?>/<?= (int)$r['id'] ?>">View</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-4 flex items-center justify-between text-sm text-gray-300">
    <div>Total: <?= (int)$total ?> | Page <?= (int)$page ?> / <?= (int)$pages ?></div>
    <div class="flex gap-2">
      <?php for ($p=1; $p <= max(1,$pages); $p++): ?>
        <a class="px-2 py-1 rounded <?= $p===$page?'bg-yellow-600 text-black':'bg-gray-800' ?>" href="?type=<?= $type ?>&page=<?= $p ?>&status=<?= urlencode($filters['status']??'') ?>&q=<?= urlencode($filters['q']??'') ?>&date_from=<?= urlencode($filters['date_from']??'') ?>&date_to=<?= urlencode($filters['date_to']??'') ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  </div>
</div>