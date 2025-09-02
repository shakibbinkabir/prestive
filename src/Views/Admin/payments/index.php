<?php declare(strict_types=1); ?>
<div class="p-6 text-gray-100">
  <h1 class="text-2xl font-bold mb-4">Payment Records</h1>
  <form method="get" action="/admin/payments" class="mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
    <div>
      <label class="text-sm">Owner Type</label>
      <select name="owner_type" class="bg-gray-800 border border-gray-700 rounded p-1 w-full">
        <?php foreach (['all'=>'All','membership'=>'Membership','trainee'=>'Trainee'] as $k=>$v): ?>
          <option value="<?= $k ?>" <?= ($filters['owner_type']??'all')===$k?'selected':'' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="text-sm">From</label>
      <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1 w-full" />
    </div>
    <div>
      <label class="text-sm">To</label>
      <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1 w-full" />
    </div>
    <div>
      <label class="text-sm">Mode</label>
      <select name="mode" class="bg-gray-800 border border-gray-700 rounded p-1 w-full">
        <option value="">Any</option>
        <?php foreach (['cheque','bank_transfer'] as $m): ?>
          <option value="<?= $m ?>" <?= ($filters['mode']??'')===$m?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$m)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="text-sm">Currency</label>
      <input type="text" name="currency" maxlength="3" placeholder="BDT" value="<?= htmlspecialchars($filters['currency']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1 w-full" />
    </div>
    <div>
      <label class="text-sm">Search</label>
      <input type="text" name="q" placeholder="Owner ID or TRX" value="<?= htmlspecialchars($filters['q']??'') ?>" class="bg-gray-800 border border-gray-700 rounded p-1 w-full" />
    </div>
    <div class="md:col-span-6">
      <button class="bg-yellow-600 text-black px-3 py-1 rounded">Apply</button>
    </div>
  </form>

  <div class="overflow-x-auto">
    <table class="min-w-full table-auto text-sm">
      <thead class="bg-gray-800">
        <tr>
          <th class="p-2 text-left">ID</th>
          <th class="p-2 text-left">Owner</th>
          <th class="p-2 text-left">Date</th>
          <th class="p-2 text-left">Mode</th>
          <th class="p-2 text-left">Amount</th>
          <th class="p-2 text-left">TRX</th>
          <th class="p-2 text-left">Notes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr class="border-b border-gray-800">
            <td class="p-2">#<?= (int)$r['id'] ?></td>
            <td class="p-2">
              <span class="px-2 py-0.5 rounded bg-gray-700"><?= htmlspecialchars($r['owner_type']) ?></span>
              <a class="text-yellow-500 hover:underline ml-1" href="/admin/applications/<?= $r['owner_type'] ?>/<?= (int)$r['owner_id'] ?>">#<?= (int)$r['owner_id'] ?></a>
            </td>
            <td class="p-2 text-gray-300"><?= htmlspecialchars($r['payment_date']) ?></td>
            <td class="p-2"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$r['mode']))) ?></td>
            <td class="p-2"><?= htmlspecialchars(number_format((float)$r['amount'],2)) ?> <span class="text-gray-400"><?= htmlspecialchars($r['currency']) ?></span></td>
            <td class="p-2"><?= htmlspecialchars($r['trx_id'] ?? '') ?></td>
            <td class="p-2 text-gray-300"><?= htmlspecialchars($r['notes'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-4 flex items-center justify-between text-sm text-gray-300">
    <div>Total: <?= (int)$total ?> | Page <?= (int)$page ?> / <?= (int)$pages ?></div>
    <div class="flex gap-2">
      <?php for ($p=1; $p <= max(1,$pages); $p++): ?>
        <a class="px-2 py-1 rounded <?= $p===$page?'bg-yellow-600 text-black':'bg-gray-800' ?>" href="?<?= http_build_query(array_merge($filters,['page'=>$p])) ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  </div>
</div>
