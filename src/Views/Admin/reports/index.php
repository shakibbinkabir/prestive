<?php declare(strict_types=1); ?>
<div class="p-6 text-gray-100">
  <h1 class="text-2xl font-bold mb-4">Reports</h1>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-gray-800 p-4 rounded border border-gray-700">
      <div class="text-gray-400 text-sm">Total Membership Applications</div>
      <div class="text-2xl font-semibold text-white"><?= (int)$summary['membership_total'] ?></div>
    </div>
    <div class="bg-gray-800 p-4 rounded border border-gray-700">
      <div class="text-gray-400 text-sm">Total Trainee Applications</div>
      <div class="text-2xl font-semibold text-white"><?= (int)$summary['trainee_total'] ?></div>
    </div>
    <div class="bg-gray-800 p-4 rounded border border-gray-700">
      <div class="text-gray-400 text-sm">Total Payments</div>
      <div class="text-2xl font-semibold text-white"><?= (int)$summary['payments_total'] ?></div>
    </div>
  </div>
  <p class="text-sm text-gray-400 mt-4">More detailed reports will be added in a future phase.</p>
</div>
