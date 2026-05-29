# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

---

## [Unreleased]

### Added
- **User search & filter** — admin user index now has a search bar (name/email) and a role dropdown filter; pagination preserves active filters via query string
- **User activity overview** — user index table shows job counts per user: "Jobs Created" (as doctor) and "Jobs Assigned" (as technician), loaded via `withCount`
- **License expiry notifications** — `App\Notifications\LicenseExpiringNotification` mail notification; `licenses:notify-expiring` Artisan command (configurable `--days` option, default 7) sends email to users whose license expires on the target date; scheduled daily at 08:00 in `routes/console.php`
- 12 new tests covering user search/filter and the expiry notification command (180 total, 348 assertions)

---

## [0.3.0] – 2026-04-16

### Added
- Romanian (`ro`) translation support for the entire application
- `lang/en/app.php` — comprehensive English translation file (nav, auth, dashboard, calendar, work jobs, users, licenses, common sections)
- `lang/ro/app.php` — full Romanian translation file matching all keys
- `lang/ro/auth.php` — Romanian auth error messages
- `app/Http/Middleware/SetLocale.php` — reads `session('locale')` and sets the application locale on every web request
- `app/Http/Controllers/LocaleController.php` — handles locale switching, validates against `['en', 'ro']`, persists choice in session
- `GET /locale/{locale}` route (`locale.set`) for switching language
- EN | RO language switcher in the main navigation bar
- All 14 Blade views updated to use `__('app.*')` translation helpers

---

## [0.2.0] – 2026-04-16

### Added
- **Licensing system** — per-user module licenses (`App\Models\License`, `App\Enums\Module`)
- `licenses` database table with `user_id` (unique FK), `expires_at`, `is_active`, `modules` (JSON, nullable)
- `App\Enums\Module` — `inventory`, `appointments`, `reviews` with `label()`
- `License::isValid()` and `License::hasModule(Module)` helper methods
- `User::license()` HasOne relation
- `App\Services\LicenseService` — `create()`, `update()`, `toggleActive()`
- `App\Http\Middleware\HasModule` — gate-checks license validity and module access; alias `module:`
- `App\Http\Controllers\Admin\LicenseController` — index, create, store, edit, update, toggleActive
- `App\Http\Requests\Admin\StoreLicenseRequest` and `UpdateLicenseRequest`
- Admin views: `resources/views/admin/licenses/index|create|edit.blade.php`
- License routes inside the `admin` prefix group
- "Licenses" link added to admin navigation
- `database/factories/LicenseFactory.php` with states: `expired()`, `inactive()`, `perpetual()`
- `tests/Feature/Admin/LicenseTest.php` (9 tests)
- `tests/Feature/Middleware/HasModuleTest.php` (6 tests)
- `tests/Unit/Services/LicenseServiceTest.php` (11 tests)

---

## [0.1.0] – 2026-04-02

### Added
- Initial Laravel 13 application scaffold with Docker Compose (PHP 8.4-fpm-alpine + MySQL 8.4)
- Supervisor managing nginx, php-fpm, and queue worker in a single container
- Authentication system (`LoginController`, `AuthService`) with email/password and "remember me"
- Role-based access control — `App\Enums\Role` (`admin`, `doctor`, `technician`), `RoleMiddleware`
- `UserPolicy` for admin-only user management
- Role-specific dashboards (`admin`, `doctor`, `technician`)
- `App\Models\User` with `is_active`, `role` columns; `UserService` for create/update/toggle-active
- Admin user management — `UserController` (index, create, store, edit, update, toggleActive)
- Admin views: `resources/views/admin/users/index|create|edit.blade.php`
- **Work Jobs** — `App\Models\WorkJob`, `App\Enums\WorkJobStatus` (`pending`, `in_progress`, `completed`, `cancelled`)
- `WorkJobController` — full CRUD; calendar view grouped by day; role-gated create/edit/delete
- Monthly calendar view (`work-jobs/calendar.blade.php`) with job pills and status legend
- Work job create, edit, show views
- **File attachments** — `Attachment` model, `WorkJobAttachmentController` (store, download, destroy)
- Drag-and-drop multi-file upload on create and show (upload modal) pages
- Download modal and delete confirmation modal for attachments (replacing browser popups)
- `PostTooLargeException` handler returning a friendly validation error
- `App\Services\WorkJobService` for business logic
- Vite + Tailwind CSS v4 frontend pipeline
- `database/factories/UserFactory.php` with role states
- `tests/Feature/` and `tests/Unit/` suites — 168 tests, 320 assertions
