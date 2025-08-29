<?php
// Expected: $draftId, $prefill, $enums, $uploads
?>
<div class="max-w-5xl mx-auto p-6" x-data="traineeForm()" x-init="init()">
    <h1 class="text-2xl font-semibold text-gold-400 mb-6">Trainee Application</h1>

    <form x-ref="formEl" @input.debounce.1200ms="autosave()" @change.debounce.1200ms="autosave()" class="space-y-8">
        <input type="hidden" name="id" x-model="state.draft_id">

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Application Type</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1">Applying For *</label>
                    <select name="training_for" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.training_for" @change="onTrainingForChanged">
                        <option value="">Select…</option>
                        <option value="self">Self</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div x-show="form.training_for === 'other'">
                    <label class="block text-sm mb-1">Trainee Type *</label>
                    <select name="trainee_type" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.trainee_type">
                        <option value="">Select…</option>
                        <option value="junior">Junior</option>
                        <option value="senior">Senior</option>
                    </select>
                </div>
                <template x-if="form.training_for === 'self'">
                    <div>
                        <label class="block text-sm mb-1">BGF ID</label>
                        <div class="flex gap-2">
                            <input type="text" name="bgf_id" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.bgf_id" placeholder="e.g., BGF12345">
                            <button type="button" @click="lookupBGF" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded">Fetch</button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1" x-text="lookupMsg"></p>
                    </div>
                </template>
            </div>
            <p class="text-xs text-gray-400 mt-2" x-show="form.training_for === 'self'">Self-applications are treated as Senior. You can still edit any prefilled fields.</p>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Identity</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Name *</label><input name="name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.name"></div>
                <div><label class="block text-sm mb-1">DOB *</label><input type="date" name="dob" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.dob"></div>
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
                    <label class="block text-sm mb-1">Religion *</label>
                    <select name="religion" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.religion">
                        <option value="not_specified">Not specified</option>
                        <?php foreach (($enums['religions'] ?? []) as $r): ?>
                            <option value="<?= htmlspecialchars($r['slug']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Blood Group *</label>
                    <select name="blood_group" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.blood_group">
                        <option value="not_specified">Not specified</option>
                        <?php foreach (($enums['blood_groups'] ?? []) as $b): ?>
                            <option value="<?= htmlspecialchars($b['slug']) ?>"><?= htmlspecialchars($b['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><label class="block text-sm mb-1">Phone *</label><input name="phone" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.phone"></div>
                <div><label class="block text-sm mb-1">Email *</label><input type="email" name="email" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.email"></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Education</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Last or Current Education *</label><input name="last_or_current_education" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.last_or_current_education"></div>
                <div><label class="block text-sm mb-1">Institution *</label><input name="institution" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.institution"></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Family</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Father's Name *</label><input name="father_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.father_name"></div>
                <div><label class="block text-sm mb-1">Father's Profession *</label><input name="father_profession" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.father_profession"></div>
                <div><label class="block text-sm mb-1">Mother's Name *</label><input name="mother_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.mother_name"></div>
                <div><label class="block text-sm mb-1">Mother's Profession *</label><input name="mother_profession" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.mother_profession"></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Address</h2>
            <div class="grid grid-cols-1 gap-4">
                <div><label class="block text-sm mb-1">Present Address *</label><textarea name="address_present" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.address_present"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800" x-show="isSenior()">
            <h2 class="text-xl text-gold-400 mb-3">Senior Only</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Marital Status *</label>
                    <select name="marital_status" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.marital_status">
                        <option value="">Select…</option>
                        <option value="unmarried">Unmarried</option>
                        <option value="married">Married</option>
                        <?php foreach (($enums['marital_statuses'] ?? []) as $m): ?>
                            <option value="<?= htmlspecialchars($m['slug']) ?>"><?= htmlspecialchars($m['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><label class="block text-sm mb-1">Occupation *</label><input name="occupation" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.occupation"></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Club (optional)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Club Name</label><input name="club_name" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.club_name"></div>
                <div><label class="block text-sm mb-1">Membership No</label><input name="membership_no" class="w-full px-3 py-2 bg-gray-800 rounded" x-model="form.membership_no"></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Optional</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm mb-1">Hobby</label><textarea name="hobby" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.hobby"></textarea></div>
                <div><label class="block text-sm mb-1">Specialty</label><textarea name="specialty" class="w-full px-3 py-2 bg-gray-800 rounded" rows="2" x-model="form.specialty"></textarea></div>
            </div>
        </section>

        <section class="bg-gray-900 p-4 rounded-lg border border-gray-800">
            <h2 class="text-xl text-gold-400 mb-3">Uploads</h2>
            <template x-if="form.trainee_type === 'junior'">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Junior Passport Photo</label>
                        <input type="file" accept="image/*" @change="onUpload($event, 'junior_passport_photo')">
                        <div class="mt-2" x-html="renderUploads('junior_passport_photo')"></div>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Birth Certificate</label>
                        <input type="file" accept=".pdf,image/*" @change="onUpload($event, 'junior_birth_cert')">
                        <div class="mt-2" x-html="renderUploads('junior_birth_cert')"></div>
                    </div>
                </div>
            </template>
            <template x-if="isSenior()">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Senior Passport Photo</label>
                        <input type="file" accept="image/*" @change="onUpload($event, 'senior_passport_photo')">
                        <div class="mt-2" x-html="renderUploads('senior_passport_photo')"></div>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">National ID</label>
                        <input type="file" accept=".pdf,image/*" @change="onUpload($event, 'senior_nid')">
                        <div class="mt-2" x-html="renderUploads('senior_nid')"></div>
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
function traineeForm() {
    return {
        state: { draft_id: <?= json_encode($draftId ?? null) ?> },
        form: <?= json_encode((object)($prefill ?? [])) ?>,
        uploaded: <?= json_encode($uploads ?? []) ?>,
        lastSaved: null,
        csrfToken: null,
        lookupMsg: '',
        previewHref: '/trainee/preview',
        init() {
            // Normalize uploaded entries
            for (const cat in this.uploaded) {
                this.uploaded[cat] = (this.uploaded[cat] || []).map(f => ({
                    ...f,
                    optimized_url: f.optimized_url ?? (f.path_optimized ? (`/file/optimized/${f.id}`) : null)
                }));
            }
            const meta = document.querySelector('meta[name="csrf-token"]');
            this.csrfToken = meta ? meta.content : null;
            const urlId = new URLSearchParams(location.search).get('id');
            const lsId = localStorage.getItem('traineeDraftId');
            if (!this.state.draft_id && urlId) this.state.draft_id = parseInt(urlId, 10) || null;
            if (!this.state.draft_id && lsId) this.state.draft_id = parseInt(lsId, 10) || null;
            if (this.state.draft_id) this.previewHref = `/trainee/preview?id=${this.state.draft_id}`;
            // Defaults
            if (!this.form.training_for) this.form.training_for = '';
            if (this.form.training_for === 'self') this.form.trainee_type = 'senior';
        },
        onTrainingForChanged() {
            if (this.form.training_for === 'self') this.form.trainee_type = 'senior';
            else this.form.trainee_type = '';
            // Create draft early when user picks mode
            this.autosave(this.form);
        },
        isSenior() { return this.form.training_for === 'self' || this.form.trainee_type === 'senior'; },
        serializeForm(formEl) {
            const fd = new FormData(formEl);
            const obj = {};
            for (const [k, v] of fd.entries()) {
                if (obj.hasOwnProperty(k)) { if (Array.isArray(obj[k])) obj[k].push(v); else obj[k] = [obj[k], v]; }
                else obj[k] = v;
            }
            return obj;
        },
        autosave(forcePayload = null) {
            const formEl = this.$refs.formEl; if (!formEl) return Promise.resolve();
            // Use explicit payload when provided; else serialize DOM
            const dataObj = forcePayload ? { ...forcePayload } : this.serializeForm(formEl);
            // Merge into reactive state so DOM can catch up
            this.form = Object.assign({}, this.form, dataObj);
            if (!dataObj || Object.keys(dataObj).length === 0) return Promise.resolve();
            const payload = { draft_id: this.state.draft_id ?? null, data: dataObj };
            if (!this.csrfToken) { console.error('[autosave] Missing CSRF'); return Promise.resolve(); }
            if (<?= APP_DEBUG ? 'true' : 'false' ?>) {
                console.debug('[trainee.autosave] payload', payload);
            }
            return fetch('/api/trainee/draft/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': this.csrfToken },
                body: JSON.stringify(payload)
            }).then(r => r.json()).then(res => {
                if (<?= APP_DEBUG ? 'true' : 'false' ?>) console.debug('[trainee.autosave] response', res);
                if (res.ok) {
                    this.state.draft_id = res.draft_id;
                    localStorage.setItem('traineeDraftId', String(this.state.draft_id));
                    this.previewHref = `/trainee/preview?id=${this.state.draft_id}`;
                    this.lastSaved = new Date(res.saved_at).toLocaleTimeString();
                }
                return res;
            }).catch(e => { if (<?= APP_DEBUG ? 'true' : 'false' ?>) console.debug('[trainee.autosave] error', e); });
        },
        onUpload(ev, category) {
            if (!this.state.draft_id) { alert('Save draft first'); return; }
            const file = ev.target.files[0]; if (!file) return;
            const fd = new FormData();
            fd.append('owner_type', 'trainee');
            fd.append('owner_id', this.state.draft_id);
            fd.append('category', category);
            fd.append('file', file);
            fetch('/api/upload', { method: 'POST', headers: { 'X-CSRF-Token': this.csrfToken }, body: fd })
                .then(r => r.json()).then(res => {
                    if (res.ok) {
                        const arr = this.uploaded[category] || (this.uploaded[category] = []);
                        res.files.forEach(f => arr.push(f));
                    } else { alert(res.error || 'Upload failed'); }
                }).catch(() => alert('Upload failed'));
        },
        renderUploads(category) {
            const arr = this.uploaded[category] || [];
            if (!arr.length) return '<div class="text-xs text-gray-500">No files uploaded</div>';
            return '<div class="grid grid-cols-2 md:grid-cols-4 gap-2">' + arr.map(f => `
                <div class=\"bg-gray-800 p-2 rounded text-xs\">${f.optimized_url && f.mime_type.startsWith('image/') ? `<img src=\"${f.optimized_url}\" class=\"w-full h-24 object-cover rounded mb-1\"/>` : ''}<div class=\"truncate\">${f.original_name}</div></div>
            `).join('') + '</div>';
        },
        canPreview() {
            const hasDraft = !!this.state.draft_id;
            // simple min checks
            const name = (this.form.name||'').trim();
            const dob = (this.form.dob||'').trim();
            const phone = (this.form.phone||'').trim();
            const email = (this.form.email||'').trim();
            const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            return hasDraft && name && dob && phone && emailOk;
        },
        async lookupBGF() {
            if (!this.form.bgf_id) { this.lookupMsg = 'Enter a BGF ID first.'; return; }
            try {
                const r = await fetch(`/api/member/by-bgf/${encodeURIComponent(this.form.bgf_id)}`);
                const j = await r.json();
                if (<?= APP_DEBUG ? 'true' : 'false' ?>) console.debug('[bgf.lookup] result', { status: r.status, body: j });
                if (r.ok && j.ok) {
                    // Apply map and persist immediately
                    Object.assign(this.form, j.data || {});
                    this.lookupMsg = 'Autofill complete. Saving…';
                    const res = await this.autosave(this.form);
                    if (res && res.ok) {
                        this.lookupMsg = 'Autofill saved.';
                    } else {
                        this.lookupMsg = 'Autofill done, but save did not complete.';
                    }
                } else {
                    this.lookupMsg = (j && j.error) ? j.error : 'Not found';
                }
            } catch (e) {
                if (<?= APP_DEBUG ? 'true' : 'false' ?>) console.debug('[bgf.lookup] error', e);
                this.lookupMsg = 'Lookup failed';
            }
        }
    }
}
</script>
