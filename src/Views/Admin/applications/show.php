<?php declare(strict_types=1); ?>
<div class="p-6 text-gray-100" x-data="{tab: 'overview', csrf: '<?= htmlspecialchars($csrf) ?>', ownerType: '<?= htmlspecialchars($type) ?>', ownerId: <?= (int)$app['id'] ?>}">
  <div class="flex items-start justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold mb-1">#<?= (int)$app['id'] ?> • <?= htmlspecialchars($type === 'membership' ? ($app['full_name'] ?? '') : ($app['name'] ?? '')) ?></h1>
      <div class="flex items-center gap-2 text-sm">
        <span class="px-2 py-0.5 rounded bg-gray-700">Status: <?= htmlspecialchars($app['status']) ?></span>
        <?php if (!empty($app['admission_id'])): ?>
          <span class="px-2 py-0.5 rounded bg-yellow-600 text-black">Admission: <?= htmlspecialchars($app['admission_id']) ?></span>
        <?php endif; ?>
      </div>
    </div>
  <div class="flex gap-2">
      <?php if (($app['status'] ?? '') === 'submitted'): ?>
        <form onsubmit="return false;" class="inline" id="btnToPaymentReceived">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
          <button class="bg-gray-800 border border-gray-700 px-3 py-1 rounded" onclick="adminTransition('payment_received')">Mark Payment Received</button>
        </form>
        <form onsubmit="return false;" class="inline ml-2" id="btnToPaidOverride">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
          <button class="bg-gray-800 border border-gray-700 px-3 py-1 rounded" onclick="adminTransitionPaidOverride()">Mark Paid (override)</button>
        </form>
      <?php endif; ?>
      <?php if (($app['status'] ?? '') === 'payment_received'): ?>
        <form onsubmit="return false;" class="inline" id="btnToPaid">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
          <button class="bg-gray-800 border border-gray-700 px-3 py-1 rounded" onclick="adminTransition('paid')">Mark Paid</button>
        </form>
      <?php endif; ?>
  <?php if (($app['status'] ?? '') === 'paid'): ?>
        <button class="bg-yellow-600 text-black px-3 py-1 rounded" onclick="openAd2()">Ad-2 Confirm</button>
      <?php endif; ?>
  <button class="bg-gray-800 border border-gray-700 px-3 py-1 rounded" onclick="copyShareLink()">Copy Share Link</button>
  <a class="bg-gray-800 border border-gray-700 px-3 py-1 rounded" target="_blank" href="/admin/applications/<?= $type ?>/<?= (int)$app['id'] ?>/pdf">Download PDF</a>
    </div>
  </div>

  <div class="flex gap-3 mb-4">
    <button class="px-3 py-1 rounded" :class="tab==='overview'?'bg-gray-700':''" @click="tab='overview'">Overview</button>
    <button class="px-3 py-1 rounded" :class="tab==='uploads'?'bg-gray-700':''" @click="tab='uploads'">Uploads</button>
    <button class="px-3 py-1 rounded" :class="tab==='payments'?'bg-gray-700':''" @click="tab='payments'">Payments</button>
    <button class="px-3 py-1 rounded" :class="tab==='audit'?'bg-gray-700':''" @click="tab='audit'">Audit Log</button>
  </div>

  <div x-show="tab==='overview'">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php foreach ($app as $k => $v): if (in_array($k, ['draft_data','uploads','payments'])) continue; ?>
        <div class="bg-gray-900 rounded p-3">
          <div class="text-xs text-gray-400"><?= htmlspecialchars($k) ?></div>
          <div class="text-sm"><?= htmlspecialchars((string)$v) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div x-show="tab==='uploads'">
    <?php foreach (($app['uploads'] ?? []) as $cat => $items): ?>
      <div class="mb-3">
        <div class="font-semibold mb-2"><?= htmlspecialchars($cat) ?></div>
        <div class="flex flex-wrap gap-3">
          <?php foreach ($items as $u): ?>
            <a class="border border-gray-800 rounded p-2 hover:bg-gray-800" href="/file/optimized/<?= (int)$u['id'] ?>" target="_blank">
              <div class="text-xs text-gray-400">#<?= (int)$u['id'] ?> <?= htmlspecialchars($u['original_name']) ?></div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div x-show="tab==='payments'">
    <div class="mb-3">
      <h3 class="font-semibold mb-2">Existing Payments</h3>
      <ul class="text-sm list-disc ml-5">
        <?php foreach (($app['payments'] ?? []) as $p): ?>
          <li>
            #<?= (int)$p['id'] ?> — <?= htmlspecialchars($p['payment_date']) ?> — <?= htmlspecialchars($p['mode']) ?> —
            <span class="text-green-400 font-semibold"><?= number_format((float)($p['amount'] ?? 0),2) ?> <?= htmlspecialchars($p['currency'] ?? 'BDT') ?></span>
            <?= $p['trx_id']?('— TX: '.htmlspecialchars($p['trx_id'])):'' ?>
            <form method="post" action="/admin/payments/<?= (int)$p['id'] ?>/delete" class="inline ml-2" onsubmit="return confirm('Delete this payment?')">
              <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>" />
              <button class="text-red-400 hover:underline">Delete</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="bg-gray-900 rounded p-3">
      <h3 class="font-semibold mb-2">Add Payment</h3>
      <?php \App\Core\View::partial('admin/partials/payment_form', ['ownerType' => $type, 'ownerId' => (int)$app['id'], 'csrf' => $csrf]); ?>
    </div>
  </div>

  <div x-show="tab==='audit'">
    <?php \App\Core\View::partial('admin/audit/index', ['logs' => $logs, 'embedded' => true]); ?>
  </div>

  <div id="ad2Modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center">
    <div class="bg-gray-900 p-4 rounded w-full max-w-md">
      <h3 class="font-semibold mb-2">Ad-2 Confirmation</h3>
      <textarea id="ad2_notes" class="w-full bg-gray-800 border border-gray-700 rounded p-2" rows="4" placeholder="Notes (optional)"></textarea>
      <div class="mt-3 flex justify-end gap-2">
        <button class="px-3 py-1 bg-gray-800 rounded" onclick="closeAd2()">Cancel</button>
        <button class="px-3 py-1 bg-yellow-600 text-black rounded" onclick="confirmAd2()">Confirm</button>
      </div>
    </div>
  </div>

  <script>
    const OWNER_TYPE = '<?= htmlspecialchars($type) ?>';
    const OWNER_ID = <?= (int)$app['id'] ?>;
    const CSRF = '<?= htmlspecialchars($csrf) ?>';

    async function adminTransition(to, note = '') {
      const body = new URLSearchParams();
      body.set('_token', CSRF);
      body.set('to_status', to);
      if (note) body.set('note', note);
  const res = await fetch(`/admin/applications/${OWNER_TYPE}/${OWNER_ID}/transition`, { method: 'POST', headers: { 'Content-Type':'application/x-www-form-urlencoded', 'Accept': 'application/json' }, body });
      const j = await res.json();
      if (j.ok) location.reload(); else alert(j.error||'Failed');
    }
    async function adminTransitionPaidOverride(){
      const note = prompt('Optional note for override (will be logged):','');
      await adminTransition('paid', note||'');
    }
    function openAd2(){ document.getElementById('ad2Modal').classList.remove('hidden'); document.getElementById('ad2Modal').classList.add('flex'); }
    function closeAd2(){ document.getElementById('ad2Modal').classList.add('hidden'); document.getElementById('ad2Modal').classList.remove('flex'); }
    async function confirmAd2(){
      const notes = document.getElementById('ad2_notes').value;
      const body = new URLSearchParams();
      body.set('_token', CSRF);
      body.set('owner_type', OWNER_TYPE);
      body.set('owner_id', OWNER_ID);
      body.set('notes', notes);
  const res = await fetch('/admin/ad2/confirm', { method: 'POST', headers: { 'Content-Type':'application/x-www-form-urlencoded', 'Accept':'application/json' }, body });
      const j = await res.json();
      if (j.ok) { alert('Confirmed: ' + j.admission_id); location.reload(); } else { alert(j.error||'Failed'); }
    }

    async function copyShareLink(){
      const body = new URLSearchParams();
      body.set('_token', CSRF);
      body.set('id', OWNER_ID);
      const url = OWNER_TYPE === 'membership' ? '/membership/share' : '/trainee/share';
  const res = await fetch(url, { method:'POST', headers:{ 'Content-Type':'application/x-www-form-urlencoded', 'Accept':'application/json' }, body });
      const j = await res.json();
      if (j.ok && j.url) {
        await navigator.clipboard.writeText(j.url);
        alert('Copied: ' + j.url);
      } else {
        alert(j.error||'Failed to create share link');
      }
    }
  </script>
</div>