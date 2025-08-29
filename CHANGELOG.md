# Changelog

All notable changes to this project will be documented in this file.

## [Phase 1] - 2025-08-29

### Added
- **Core MVC Framework**
  - Single front controller (`public/index.php`) with routing
  - PSR-4 autoloading via Composer
  - Lightweight Router class with GET/POST route support
  - Base Controller class with rendering and JSON response helpers
  - View system with layout wrapper and partial support

- **Security Features**
  - CSRF token generation and validation for all forms and APIs
  - Session-based authentication with secure cookie parameters
  - Rate limiting for public API endpoints (60 requests/minute/IP)
  - Input validation helpers with common rules
  - Secure password hashing with bcrypt

- **Admin Authentication System**
  - Admin login/logout functionality with session management
  - Admin dashboard with application statistics
  - User model with last login tracking
  - Admin-only route protection

- **Database Architecture**
  - 5 migration files with complete schema
  - Users table with admin role support
  - Enum tables for membership types, genders, religions, marital statuses, blood groups
  - Membership and trainee application tables with draft support
  - Upload, payment, audit log, consent log, and share link tables
  - Migration runner script with progress logging

- **Application Draft System**
  - Draft save API endpoints for membership and trainee applications
  - JSON data storage in database with automatic timestamps
  - Client IP tracking for security
  - RESTful API design with proper error handling

- **Frontend UI**
  - Dark-themed landing page with Tailwind CSS
  - Responsive design with mobile-first approach
  - Gold accent color scheme (#D4AF37)
  - Alpine.js for interactive components
  - Flash message system with auto-dismiss

- **Development Tools**
  - Composer setup with required dependencies (phpdotenv, ramsey/uuid)
  - Database seeder with enum data and admin user
  - Storage directory structure with proper permissions
  - Environment configuration with .env.example
  - Debug mode with helpful developer information

### Technical Details
- **PHP 8.0** with strict typing enabled throughout
- **PDO** with prepared statements and exception handling
- **File-based rate limiting** stored in storage/logs
- **JSON draft storage** in database with proper validation
- **Apache .htaccess** configuration for clean URLs
- **Placeholder services** for future phase implementations

### Routes Implemented
```
GET  /                           # Landing page
GET  /admin/login               # Admin login form
POST /admin/login               # Admin login processing
POST /admin/logout              # Admin logout
GET  /admin/dashboard           # Admin dashboard (auth required)
POST /api/membership/draft/save # Membership draft save API
POST /api/trainee/draft/save    # Trainee draft save API
GET  /membership/apply          # Placeholder - coming soon
GET  /trainee/apply             # Placeholder - coming soon
```

### Files Structure
- **Core Classes**: 9 files in src/Core/
- **Models**: 9 files in src/Models/
- **Controllers**: 11 files across src/Controllers/ and src/Controllers/Admin/
- **Views**: 4 files in src/Views/ with components
- **Migrations**: 5 SQL files in database/migrations/
- **Scripts**: 2 utility scripts for migration and seeding
- **Configuration**: Composer, environment, and Apache config files

### Next Phase Preparation
- Placeholder controller methods for file uploads, sharing, and preview
- Service class stubs for admission ID generation and share links
- Complete enum seeding for form dropdowns
- Audit logging infrastructure ready for implementation
- Consent tracking system foundation in place

## [Project audit, fixes, and local test guide] - 2025-08-30

### Audit Performed
- Reviewed core architecture (front controller, router, controllers, models, views)
- Validated Composer config and autoloading
- Scanned migrations and seeding scripts for schema coverage and order
- Checked CSRF, session cookie params, rate limiter wiring, and route registration
- Verified namespaces, strict_types, and PSR-4 alignment
- Identified view path casing risks and missing asset reference

### Fixes Applied
- Config: Use Dotenv safeLoad to allow boot without .env during first run
- Views: Robust view path resolution for Admin/Components casing; flash partial loading fixed
- Layout: Removed broken logo image path to avoid 404s in dev
- Composer: Added license field (proprietary) to pass validation without warnings

### Remaining Gaps by Phase
- Phase 2: Membership form UI, uploads API with MIME/size validation, preview/submit routes, share link creation, consent logging on submit
- Phase 3: Trainee application UI/logic (Self vs Other; Junior vs Senior), BGF autofill service
- Phase 4: Admin listing/detail pages, payment capture flow and status transitions, Ad-2 confirmation, audit log writes
- Phase 5: PDF generation, exports, optimization/hardening, backups, docs

### Notes for Next Phases
- Implement upload controller with finfo-based MIME validation and size/count constraints
- Add share link endpoints and read-only preview pages
- Wire consent log creation on submit; add terms versioning
- Expand admin dashboard to include filters and details; add immutable audit logging on state changes

## [Phase 2] - Membership Form, Uploads, Preview, Submit, Share - 2025-08-30

### Added
- Dependencies: intervention/image ^2.x for image optimization to WebP
- Config: TERMS_VERSION, TERMS_URL, TERMS_TEXT
- DB: migration 0006_expand_membership_fields.sql adding all membership fields
- Services: ImageService (optimizeToWebp), UploadService (category constraints, finfo MIME, storage, counts)
- Models: MembershipApplication (find/createDraft/updateDraft/updateFieldsOnSubmit), Upload (findByOwner, countByOwnerCategory), ShareLink (createFor, findByToken), ConsentLog (createConsent helper)
- Controllers: Membership (applyForm, preview, submit with validation+consent, share), File (upload API with rate limit and optimized serving), Share (public read-only preview), Pages (terms)
- Views: membership/form.php (Alpine autosave + uploads widgets), membership/preview.php (read-only with actions), terms.php; layout links
- Routes: all endpoints for Phase 2 registered in public/index.php
- Web Server: ensured Apache rewrite via public/.htaccess present

### Validation
- Submit requires full_name, email (valid), gender (from enum), dob (YYYY-MM-DD), terms_confirmed
- Uploads enforce MIME/size/count and generate optimized WebP for images

### Notes
- If image optimization fails, original is copied; flow continues

## [Fix] Membership autosave payload, draft reuse, and Preview gating - 2025-08-30

### Fixed
- Autosave now serializes the form into a plain object (not an empty array), using FormData -> object conversion.
- Draft ID is persisted across reloads via URL param and localStorage (membershipDraftId) and reused for subsequent saves.
- Preview button now enables when a draft exists; also checks minimal required fields for better UX and updates href dynamically.
- CSRF header is read from the meta tag; autosave logs a console error if missing (APP_DEBUG only logs retained).

### Hardened
- Server `MembershipController::saveDraft` converts array-of-pairs payloads into associative objects and consistently returns `{ ok, draft_id, saved_at }`.

### Files Changed
- `src/Views/membership/form.php`: names on inputs, serializeForm, autosave body, draftId persistence, Preview gating, debug logs.
- `src/Controllers/MembershipController.php`: input coercion for `data`, helper `isAssoc`.

## [Phase 3] - Trainee Self/Other, Junior/Senior, BGF autofill - 2025-08-30

### Added
- DB: `0007_expand_trainee_fields.sql` adds all trainee fields (common + senior-only + admission_id) and indexes on name/email.
- Models:
  - `TraineeApplication`: draft helpers and `updateFieldsOnSubmit`.
  - `MembershipApplication::findConfirmedByBGF($bgfId)` for autofill source.
- Services:
  - `UploadService`: support `owner_type=trainee` and trainee categories (`junior_passport_photo`, `junior_birth_cert`, `senior_passport_photo`, `senior_nid`).
- Controllers:
  - `TraineeController`: `applyForm`, `saveDraft` (assoc JSON, rate-limited), `preview`, `submit` (validation + consent), `share` (admin), `lookupByBGF` (public, 60/min/IP).
  - `FileController`: accept uploads for `owner_type=trainee`.
  - `ShareController`: render trainee shared previews.
- Views:
  - `trainee/form.php`: single dynamic Alpine view for Self/Other and Junior/Senior with BGF lookup and autosave.
  - `trainee/preview.php`: read-only display with actions (submit, share); hides actions in share mode.

### Routes
```
GET  /trainee/apply
GET  /trainee/preview
POST /trainee/submit
POST /trainee/share               # admin-only
GET  /api/member/by-bgf/{bgf}     # public lookup, rate-limited
```

### Validation & Security
- Server-side rules for required fields, Senior-only constraints, and `training_for` logic (Self forces Senior and requires `bgf_id`).
- CSRF enforced on POSTs; rate limiting on draft save and BGF lookup.
- Consent logged on submit using TERMS_VERSION/TEXT.

### Uploads
- Enforced MIME, size, and count; optimized WebP generated for images; single-file rule for trainee categories.

### Notes
- BGF autofill prepopulates fields but remains editable.
- If image optimization fails, originals are kept; flow continues.

## [Fix] Trainee BGF autofill persists to draft and appears in preview - 2025-08-30

### Fixed
- `src/Views/trainee/form.php`:
  - `autosave(forcePayload = null)` now accepts explicit payloads and merges Alpine state into the save body, ensuring programmatic changes are persisted even if inputs don’t fire events.
  - After successful BGF lookup, the component immediately calls `autosave(this.form)` so a `draft_id` is created/updated and `previewHref` updates right away.
  - Uploads now use the component `csrfToken` header consistently.
  - Added debug logs (APP_DEBUG) for lookup and autosave payload/responses.

### Outcome
- Fetching BGF data and clicking Preview shows the autofilled values without requiring manual typing.