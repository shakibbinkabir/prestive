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