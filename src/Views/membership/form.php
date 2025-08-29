<?php
// Expected: $draftId, $prefill, $enums, $uploads
?>
<div class="max-w-5xl mx-auto p-6" x-data="membershipForm()" x-init="init()">
    <h1 class="text-2xl font-semibold text-gold-400 mb-6">Membership Application</h1>

    <form @input.debounce.1500ms="autosave" @change.debounce.1500ms="autosave" class="space-y-8">
        <input type="hidden" name="id" x-model="state.draft_id">

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Personal</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Full Name *</label>
                    <input type="text" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.full_name">
                </div>
                <div>
                    <label class="block text-sm mb-1">Email *</label>
                    <input type="email" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.email">
                </div>
                <div>
                    <label class="block text-sm mb-1">Gender *</label>
                    <select class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.gender">
                        <option value="">Select…</option>
                        <?php foreach (($enums['genders'] ?? []) as $g): ?>
                            <option value="<?= htmlspecialchars($g['slug']) ?>"><?= htmlspecialchars($g['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Date of Birth *</label>
                    <input type="date" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.dob">
                </div>
                <div>
                    <label class="block text-sm mb-1">Membership Type</label>
                    <select class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.membership_type">
                        <option value="">Select…</option>
                        <?php foreach (($enums['membership_types'] ?? []) as $t): ?>
                            <option value="<?= htmlspecialchars($t['slug']) ?>"><?= htmlspecialchars($t['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Nationality</label>
                    <input type="text" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.nationality">
                </div>
                <div>
                    <label class="block text-sm mb-1">Religion</label>
                    <select class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.religion">
                        <option value="not_specified">Not specified</option>
                        <?php foreach (($enums['religions'] ?? []) as $r): ?>
                            <option value="<?= htmlspecialchars($r['slug']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Marital Status</label>
                    <select class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.marital_status">
                        <option value="not_specified">Not specified</option>
                        <?php foreach (($enums['marital_statuses'] ?? []) as $m): ?>
                            <option value="<?= htmlspecialchars($m['slug']) ?>"><?= htmlspecialchars($m['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Blood Group</label>
                    <select class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.blood_group">
                        <option value="not_specified">Not specified</option>
                        <?php foreach (($enums['blood_groups'] ?? []) as $b): ?>
                            <option value="<?= htmlspecialchars($b['slug']) ?>"><?= htmlspecialchars($b['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">NID No</label>
                    <input type="text" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.nid_no">
                </div>
                <div>
                    <label class="block text-sm mb-1">Passport No</label>
                    <input type="text" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.passport_no">
                </div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Family</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Father's Name</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.father_name"></div>
                <div><label class="block text-sm mb-1">Mother's Name</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.mother_name"></div>
                <div><label class="block text-sm mb-1">Spouse Name</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.spouse_name"></div>
                <div><label class="block text-sm mb-1">Number of Children</label><input type="number" min="0" class="w-full px-3 py-2 bg-gray-800 rounded" x-model.number="form.num_children"></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Children Names</label><textarea class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.children_names"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Professional</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Organization</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.organization"></div>
                <div><label class="block text-sm mb-1">Designation</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.designation"></div>
                <div><label class="block text-sm mb-1">Profession</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.profession"></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Education Qualifications</label><textarea class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.education_qualifications"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Addresses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2"><label class="block text-sm mb-1">Present Address</label><textarea class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.address_present"></textarea></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Permanent Address</label><textarea class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.address_permanent"></textarea></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Office Address</label><textarea class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.address_office"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Contact & Emergency</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Mobile</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.mobile"></div>
                <div><label class="block text-sm mb-1">Emergency Name</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.emergency_name"></div>
                <div><label class="block text-sm mb-1">Emergency Relationship</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.emergency_relationship"></div>
                <div><label class="block text-sm mb-1">Emergency Phone</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.emergency_phone"></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Emergency Address</label><textarea class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.emergency_address"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Other</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2"><label class="block text-sm mb-1">Hobbies & Interests</label><textarea class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.hobbies_interests"></textarea></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Previous Club Memberships</label><textarea class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.previous_club_memberships"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Proposers</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Proposer Name</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.proposer_name"></div>
                <div><label class="block text-sm mb-1">Proposer Membership No</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.proposer_membership_no"></div>
                <div><label class="block text-sm mb-1">Seconder Name</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.seconder_name"></div>
                <div><label class="block text-sm mb-1">Seconder Membership No</label><input class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.seconder_membership_no"></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Uploads</h2>
            <template x-for="cat in categories" :key="cat.key">
                <div class="mb-4">
                    <label class="block text-sm mb-1" x-text="cat.label"></label>
                    <input type="file" :accept="cat.accept" @change="onUpload($event, cat.key)">
                    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
                        <template x-for="file in (uploaded[cat.key] || [])" :key="file.id">
                            <div class="bg-gray-800 p-2 rounded text-xs">
                                <template x-if="file.optimized_url && file.mime_type.startsWith('image/')">
                                    <img :src="file.optimized_url" class="w-full h-24 object-cover rounded mb-1"/>
                                </template>
                                <div class="truncate" x-text="file.original_name"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Consent</h2>
            <p class="text-sm text-gray-300">By proceeding you agree to our <a href="/terms" class="underline text-gold-400" target="_blank">Terms & Conditions</a>.</p>
        </section>
    </form>

    <div class="fixed bottom-0 left-0 right-0 bg-gray-900 border-t border-gray-800 p-4">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="text-sm text-gray-400">Last saved: <span x-text="lastSaved || '—'"></span></div>
            <a :href="'/membership/preview?id=' + (state.draft_id || 0)" class="px-4 py-2 bg-gold-500 hover:bg-gold-600 text-black rounded disabled:opacity-50" :class="{ 'pointer-events-none opacity-50': !state.draft_id }">Preview</a>
        </div>
    </div>
</div>

<script>
function membershipForm() {
    return {
        state: {
            draft_id: <?= json_encode($draftId ?? null) ?>,
        },
        form: <?= json_encode($prefill ?? []) ?>,
    uploaded: <?= json_encode($uploads ?? []) ?>,
        lastSaved: null,
    _lastPayloadJson: null,
        categories: [
            { key: 'passport_photos', label: 'Passport Photos', accept: 'image/*' },
            { key: 'biodata_with_photo', label: 'Biodata with Photo', accept: '.pdf,image/*' },
            { key: 'nid_copy', label: 'NID Copy', accept: '.pdf,image/*' },
            { key: 'passport_copies', label: 'Passport Copies', accept: '.pdf,image/*' },
            { key: 'tin_cert', label: 'TIN Certificate', accept: '.pdf,image/*' },
            { key: 'ack_receipts', label: 'Acknowledgement Receipts', accept: '.pdf,image/*' },
            { key: 'trade_license', label: 'Trade License', accept: '.pdf,image/*' },
            { key: 'work_permit', label: 'Work Permit', accept: '.pdf,image/*' },
            { key: 'visa', label: 'Visa', accept: '.pdf,image/*' },
        ],
        init() {
            // Normalize uploaded entries coming from server DB rows
            for (const cat in this.uploaded) {
                this.uploaded[cat] = (this.uploaded[cat] || []).map(f => ({
                    ...f,
                    optimized_url: f.optimized_url ?? (f.path_optimized ? (`/file/optimized/${f.id}`) : null)
                }));
            }
        },
        autosave() {
            // Skip if form is empty (no keys) to avoid server 400/no-op
            if (!this.form || Object.keys(this.form).length === 0) return;
            const payload = { draft_id: this.state.draft_id, data: this.form };
            const pj = JSON.stringify(payload);
            if (this._lastPayloadJson === pj) return; // no changes
            fetch('/api/membership/draft/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.csrfToken },
                body: pj
            }).then(r => r.json()).then(res => {
                if (res.ok) {
                    this.state.draft_id = res.draft_id;
                    this.lastSaved = new Date(res.saved_at).toLocaleString();
                    this._lastPayloadJson = pj;
                }
            }).catch(() => { /* ignore network hiccups */ });
        },
        onUpload(ev, category) {
            if (!this.state.draft_id) { alert('Save draft first'); return; }
            const file = ev.target.files[0];
            if (!file) return;
            const fd = new FormData();
            fd.append('owner_type', 'membership');
            fd.append('owner_id', this.state.draft_id);
            fd.append('category', category);
            fd.append('file', file);
            fetch('/api/upload', {
                method: 'POST',
                headers: { 'X-CSRF-Token': window.csrfToken },
                body: fd
            }).then(r => r.json()).then(res => {
                if (res.ok) {
                    const arr = this.uploaded[category] || (this.uploaded[category] = []);
                    res.files.forEach(f => arr.push(f));
                } else {
                    alert(res.error || 'Upload failed');
                }
            }).catch(() => alert('Upload failed'));
        }
    }
}
</script>
