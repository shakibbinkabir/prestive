<?php
// Expected: $application, $uploads, $is_share_view, $data
use App\Core\Auth;
?>
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold text-gold-400 mb-6">Trainee Application Preview</h1>

    <div class="bg-gray-900 p-4 rounded-lg border border-gray-800 space-y-6">
    <?php $a = isset($data) ? $data : $application; ?>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-400">Applying For:</span> <?= htmlspecialchars($a['training_for'] ?? '') ?></div>
                <div><span class="text-gray-400">Trainee Type:</span> <?= htmlspecialchars($a['trainee_type'] ?? '') ?></div>
                <div><span class="text-gray-400">BGF ID:</span> <?= htmlspecialchars($a['bgf_id'] ?? '') ?></div>
            </div>
        </div>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Identity</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-400">Name:</span> <?= htmlspecialchars($a['name'] ?? '') ?></div>
                <div><span class="text-gray-400">DOB:</span> <?= htmlspecialchars($a['dob'] ?? '') ?></div>
                <div><span class="text-gray-400">Gender:</span> <?= htmlspecialchars($a['gender'] ?? '') ?></div>
                <div><span class="text-gray-400">Religion:</span> <?= htmlspecialchars($a['religion'] ?? '') ?></div>
                <div><span class="text-gray-400">Blood Group:</span> <?= htmlspecialchars($a['blood_group'] ?? '') ?></div>
                <div><span class="text-gray-400">Phone:</span> <?= htmlspecialchars($a['phone'] ?? '') ?></div>
                <div><span class="text-gray-400">Email:</span> <?= htmlspecialchars($a['email'] ?? '') ?></div>
            </div>
        </div>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Education</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-400">Last/Current:</span> <?= htmlspecialchars($a['last_or_current_education'] ?? '') ?></div>
                <div><span class="text-gray-400">Institution:</span> <?= htmlspecialchars($a['institution'] ?? '') ?></div>
            </div>
        </div>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Family</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-400">Father:</span> <?= htmlspecialchars($a['father_name'] ?? '') ?></div>
                <div><span class="text-gray-400">Father Profession:</span> <?= htmlspecialchars($a['father_profession'] ?? '') ?></div>
                <div><span class="text-gray-400">Mother:</span> <?= htmlspecialchars($a['mother_name'] ?? '') ?></div>
                <div><span class="text-gray-400">Mother Profession:</span> <?= htmlspecialchars($a['mother_profession'] ?? '') ?></div>
            </div>
        </div>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Address</h2>
            <div class="text-sm">
                <div><span class="text-gray-400">Present:</span> <?= nl2br(htmlspecialchars($a['address_present'] ?? '')) ?></div>
            </div>
        </div>
        <?php if (($a['trainee_type'] ?? '') === 'senior' || ($a['training_for'] ?? '') === 'self'): ?>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Senior</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-400">Marital Status:</span> <?= htmlspecialchars($a['marital_status'] ?? '') ?></div>
                <div><span class="text-gray-400">Occupation:</span> <?= htmlspecialchars($a['occupation'] ?? '') ?></div>
            </div>
        </div>
        <?php endif; ?>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Club</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-400">Club:</span> <?= htmlspecialchars($a['club_name'] ?? '') ?></div>
                <div><span class="text-gray-400">Membership No:</span> <?= htmlspecialchars($a['membership_no'] ?? '') ?></div>
            </div>
        </div>
        <?php if (!empty($a['hobby']) || !empty($a['specialty'])): ?>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Optional</h2>
            <div class="text-sm">
                <div><span class="text-gray-400">Hobby:</span> <?= nl2br(htmlspecialchars($a['hobby'] ?? '')) ?></div>
                <div class="mt-1"><span class="text-gray-400">Specialty:</span> <?= nl2br(htmlspecialchars($a['specialty'] ?? '')) ?></div>
            </div>
        </div>
        <?php endif; ?>
        <div>
            <h2 class="text-lg text-gold-400 mb-2">Uploads</h2>
            <?php foreach (($uploads ?? []) as $cat => $items): ?>
                <div class="mb-3">
                    <div class="text-sm text-gray-300 mb-1"><?= htmlspecialchars(str_replace('_',' ', $cat)) ?></div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <?php foreach ($items as $u): ?>
                            <div class="bg-gray-800 p-2 rounded text-xs">
                                <?php if ($u['path_optimized'] && str_starts_with($u['mime_type'], 'image/')): ?>
                                    <img src="<?= '/file/optimized/' . $u['id'] ?>" class="w-full h-24 object-cover rounded mb-1"/>
                                <?php endif; ?>
                                <a class="underline" href="<?= '/file/raw/' . (int)$u['id'] ?>" target="_blank" rel="noopener">View</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (!$is_share_view): ?>
    <div class="mt-6 flex items-center gap-3">
        <a href="/trainee/apply?id=<?= (int)$a['id'] ?>" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">Edit</a>
        <form action="/trainee/submit" method="POST" onsubmit="return confirm('Submit application?')">
            <?= App\Core\CSRF::field() ?>
            <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
            <label class="inline-flex items-center text-sm mr-3"><input type="checkbox" name="terms_confirmed" required class="mr-2"> I agree to the <a class="underline ml-1" href="/terms" target="_blank">terms</a></label>
            <button type="submit" class="px-4 py-2 bg-gold-500 hover:bg-gold-600 text-black rounded">Submit</button>
        </form>
        <?php if (Auth::checkAdmin()): ?>
            <form x-data @submit.prevent="share()" class="inline-block">
                <?= App\Core\CSRF::field() ?>
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded">Send to Client</button>
            </form>
            <button class="px-4 py-2 bg-gray-700 rounded opacity-60 cursor-not-allowed" title="Coming soon" disabled>Add Payment Details</button>
        <?php endif; ?>
    </div>
    <script>
        function share() {
            const fd = new FormData(document.currentScript.previousElementSibling);
            fetch('/trainee/share', { method: 'POST', headers: { 'X-CSRF-Token': window.csrfToken }, body: fd })
                .then(r => r.json()).then(res => {
                    if (res.ok) {
                        if (navigator.share) { navigator.share({ title: 'Prestive Trainee', url: res.url }).catch(()=>{}); }
                        prompt('Share this link:', res.url);
                    } else { alert(res.error || 'Failed'); }
                });
        }
    </script>
    <?php endif; ?>
</div>
