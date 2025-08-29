<?php
// Expected: $draftId, $prefill, $enums, $uploads
?>
<div class="max-w-5xl mx-auto p-6" x-data="membershipForm()" x-init="init()">
    <h1 class="text-2xl font-semibold text-gold-400 mb-6">Membership Application</h1>

    <form x-ref="formEl" @input.debounce.1500ms="autosave" @change.debounce.1500ms="autosave" class="space-y-8">
        <input type="hidden" name="id" x-model="state.draft_id">

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Personal</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Full Name *</label>
                    <input type="text" name="full_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.full_name">
                </div>
                <div>
                    <label class="block text-sm mb-1">Email *</label>
                    <input type="email" name="email" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.email">
                </div>
                <div>
                    <label class="block text-sm mb-1">Gender *</label>
                    <select name="gender" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.gender">
                        <option value="">Select…</option>
                        <?php foreach (($enums['genders'] ?? []) as $g): ?>
                            <option value="<?= htmlspecialchars($g['slug']) ?>"><?= htmlspecialchars($g['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Date of Birth *</label>
                    <input type="date" name="dob" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.dob">
                </div>
                <div>
                    <label class="block text-sm mb-1">Membership Type</label>
                    <select name="membership_type" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.membership_type">
                        <option value="">Select…</option>
                        <?php foreach (($enums['membership_types'] ?? []) as $t): ?>
                            <option value="<?= htmlspecialchars($t['slug']) ?>"><?= htmlspecialchars($t['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Nationality</label>
                    <input type="text" name="nationality" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.nationality">
                </div>
                <div>
                    <label class="block text-sm mb-1">Religion</label>
                    <select name="religion" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.religion">
                        <option value="not_specified">Not specified</option>
                        <?php foreach (($enums['religions'] ?? []) as $r): ?>
                            <option value="<?= htmlspecialchars($r['slug']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Marital Status</label>
                    <select name="marital_status" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.marital_status">
                        <option value="not_specified">Not specified</option>
                        <?php foreach (($enums['marital_statuses'] ?? []) as $m): ?>
                            <option value="<?= htmlspecialchars($m['slug']) ?>"><?= htmlspecialchars($m['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Blood Group</label>
                    <select name="blood_group" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.blood_group">
                        <option value="not_specified">Not specified</option>
                        <?php foreach (($enums['blood_groups'] ?? []) as $b): ?>
                            <option value="<?= htmlspecialchars($b['slug']) ?>"><?= htmlspecialchars($b['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">NID No</label>
                    <input type="text" name="nid_no" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.nid_no">
                </div>
                <div>
                    <label class="block text-sm mb-1">Passport No</label>
                    <input type="text" name="passport_no" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.passport_no">
                </div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Family</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Father's Name</label><input name="father_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.father_name"></div>
                <div><label class="block text-sm mb-1">Mother's Name</label><input name="mother_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.mother_name"></div>
                <div><label class="block text-sm mb-1">Spouse Name</label><input name="spouse_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.spouse_name"></div>
                <div><label class="block text-sm mb-1">Number of Children</label><input type="number" min="0" name="num_children" class="w-full px-3 py-2 bg-gray-800 rounded" x-model.number="form.num_children"></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Children Names</label><textarea name="children_names" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.children_names"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Professional</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Organization</label><input name="organization" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.organization"></div>
                <div><label class="block text-sm mb-1">Designation</label><input name="designation" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.designation"></div>
                <div><label class="block text-sm mb-1">Profession</label><input name="profession" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.profession"></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Education Qualifications</label><textarea name="education_qualifications" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.education_qualifications"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Addresses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2"><label class="block text-sm mb-1">Present Address</label><textarea name="address_present" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.address_present"></textarea></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Permanent Address</label><textarea name="address_permanent" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.address_permanent"></textarea></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Office Address</label><textarea name="address_office" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.address_office"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Contact & Emergency</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Mobile</label><input name="mobile" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.mobile"></div>
                <div><label class="block text-sm mb-1">Emergency Name</label><input name="emergency_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.emergency_name"></div>
                <div><label class="block text-sm mb-1">Emergency Relationship</label><input name="emergency_relationship" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.emergency_relationship"></div>
                <div><label class="block text-sm mb-1">Emergency Phone</label><input name="emergency_phone" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.emergency_phone"></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Emergency Address</label><textarea name="emergency_address" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.emergency_address"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Other</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2"><label class="block text-sm mb-1">Hobbies & Interests</label><textarea name="hobbies_interests" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.hobbies_interests"></textarea></div>
                <div class="md:col-span-2"><label class="block text-sm mb-1">Previous Club Memberships</label><textarea name="previous_club_memberships" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.previous_club_memberships"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Proposers</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Proposer Name</label><input name="proposer_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.proposer_name"></div>
                <div><label class="block text-sm mb-1">Proposer Membership No</label><input name="proposer_membership_no" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.proposer_membership_no"></div>
                <div><label class="block text-sm mb-1">Seconder Name</label><input name="seconder_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.seconder_name"></div>
                <div><label class="block text-sm mb-1">Seconder Membership No</label><input name="seconder_membership_no" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.seconder_membership_no"></div>
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
            <a :href="previewHref" class="px-4 py-2 bg-gold-500 hover:bg-gold-600 text-black rounded disabled:opacity-50" :class="{ 'pointer-events-none opacity-50': !canPreview() }">Preview</a>
        </div>
    </div>
</div>

<script>
function membershipForm() {
    return {
        state: {
            draft_id: <?= json_encode($draftId ?? null) ?>,
        },
        // Force object for form state to avoid JSON [] issue when empty
        form: <?= json_encode((object)($prefill ?? [])) ?>,
        uploaded: <?= json_encode($uploads ?? []) ?>,
        lastSaved: null,
        _lastPayloadJson: null,
        csrfToken: null,
        genderOptions: <?= json_encode(array_map(fn($g) => $g['slug'], $enums['genders'] ?? [])) ?>,
        previewHref: '/membership/preview',
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

            // Set CSRF token
            const meta = document.querySelector('meta[name="csrf-token"]');
            this.csrfToken = meta ? meta.content : null;
            if (!this.csrfToken) {
                console.error('[autosave] Missing CSRF token meta tag');
            }

            // Draft ID: prefer URL ?id, else server-provided, else localStorage
            const urlId = new URLSearchParams(location.search).get('id');
            const lsId = localStorage.getItem('membershipDraftId');
            if (!this.state.draft_id && urlId) this.state.draft_id = parseInt(urlId, 10) || null;
            if (!this.state.draft_id && lsId) this.state.draft_id = parseInt(lsId, 10) || null;

            // Initialize preview href
            if (this.state.draft_id) {
                this.previewHref = `/membership/preview?id=${this.state.draft_id}`;
            }
        },
        serializeForm(formEl) {
            // Convert FormData to a plain object, handle duplicates as arrays
            const fd = new FormData(formEl);
            const obj = {};
            for (const [k, v] of fd.entries()) {
                if (obj.hasOwnProperty(k)) {
                    if (Array.isArray(obj[k])) obj[k].push(v);
                    else obj[k] = [obj[k], v];
                } else {
                    obj[k] = v;
                }
            }
            return obj;
        },
        autosave() {
            const formEl = this.$refs.formEl;
            if (!formEl) return;
            const dataObj = this.serializeForm(formEl);
            // Maintain reactive form object
            this.form = Object.assign({}, this.form, dataObj);
            // Skip if no meaningful keys
            if (!dataObj || Object.keys(dataObj).length === 0) return;

            const payload = { draft_id: this.state.draft_id ?? null, data: dataObj };
            const pj = JSON.stringify(payload);
            if (this._lastPayloadJson === pj) return; // no changes

            if (!this.csrfToken) {
                console.error('[autosave] Missing CSRF token, request not sent');
                return;
            }

            if (<?= APP_DEBUG ? 'true' : 'false' ?>) {
                console.debug('[autosave] payload', payload);
            }

            fetch('/api/membership/draft/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': this.csrfToken },
                body: pj
            }).then(r => r.json()).then(res => {
                if (res.ok) {
                    this.state.draft_id = res.draft_id;
                    localStorage.setItem('membershipDraftId', String(this.state.draft_id));
                    this.previewHref = `/membership/preview?id=${this.state.draft_id}`;
                    this.lastSaved = new Date(res.saved_at).toLocaleTimeString();
                    this._lastPayloadJson = pj;
                    if (<?= APP_DEBUG ? 'true' : 'false' ?>) {
                        console.debug('[autosave] response', res);
                    }
                }
            }).catch((e) => { if (<?= APP_DEBUG ? 'true' : 'false' ?>) console.debug('[autosave] error', e); });
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
        },
        hasMinRequired() {
            const fn = (this.form.full_name || '').trim();
            const email = (this.form.email || '').trim();
            const dob = (this.form.dob || '').trim();
            const gender = (this.form.gender || '').trim();
            const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            const genderOk = this.genderOptions.includes(gender);
            const ok = fn.length > 0 && emailOk && dob.length > 0 && genderOk;
            if (<?= APP_DEBUG ? 'true' : 'false' ?>) {
                console.debug('[preview-check] hasMinRequired', { ok, fn, email, dob, gender });
            }
            return ok;
        },
        canPreview() {
            // Enable if both min-required and draft exist; fallback: allow when draft exists
            const hasDraft = !!this.state.draft_id;
            const allow = (this.hasMinRequired() && hasDraft) || hasDraft;
            return allow;
        }
    }
}
</script>
