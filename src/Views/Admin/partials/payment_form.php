<?php declare(strict_types=1); ?>
<form method="post" action="/admin/payments" class="grid grid-cols-1 md:grid-cols-3 gap-3">
  <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>" />
  <input type="hidden" name="owner_type" value="<?= htmlspecialchars($ownerType) ?>" />
  <input type="hidden" name="owner_id" value="<?= (int)$ownerId ?>" />
  <div>
    <label class="block text-sm mb-1">Payment Date</label>
    <input type="date" name="payment_date" class="w-full bg-gray-800 border border-gray-700 rounded p-2" required />
  </div>
  <div>
    <label class="block text-sm mb-1">Mode</label>
    <select name="mode" class="w-full bg-gray-800 border border-gray-700 rounded p-2" required>
      <option value="cheque">Cheque</option>
      <option value="bank_transfer">Bank Transfer</option>
    </select>
  </div>
  <div>
    <label class="block text-sm mb-1">Amount</label>
    <input type="number" step="0.01" min="0.01" name="amount" class="w-full bg-gray-800 border border-gray-700 rounded p-2" required />
  </div>
  <div>
    <label class="block text-sm mb-1">Currency</label>
    <input type="text" name="currency" value="BDT" maxlength="3" class="w-full bg-gray-800 border border-gray-700 rounded p-2" required />
  </div>
  <div class="md:col-span-2">
    <label class="block text-sm mb-1">Transaction ID</label>
    <input type="text" name="trx_id" class="w-full bg-gray-800 border border-gray-700 rounded p-2" />
  </div>
  <div class="md:col-span-3">
    <label class="block text-sm mb-1">Notes</label>
    <textarea name="notes" rows="3" class="w-full bg-gray-800 border border-gray-700 rounded p-2"></textarea>
  </div>
  <div class="md:col-span-3 text-right">
    <button class="bg-yellow-600 text-black px-4 py-2 rounded">Add Payment</button>
  </div>
</form>