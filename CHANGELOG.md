# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

---

## [Unreleased]

### Added
- **Phase 4 — Patient Appointments module** (`module:appointments`, gated by `HasModule` middleware)
  - `Patient` model: name, date of birth, email, phone, notes; with appointment history relation
  - `Appointment` model: patient, doctor, optional work job link, scheduled datetime, status, notes; `AppointmentStatus` enum (Scheduled / Completed / Cancelled) with `label()` and `colour()` helpers
  - `patients` and `appointments` migrations (with cascade delete on patient removal)
  - `AppointmentService` — CRUD for patients; create, reschedule, cancel, complete appointments; calendar scoping (admin sees all, doctor sees own); `getDueForReminder()` for scheduled notifications
  - `AppointmentReminderNotification` — mail notification sent to the appointment's doctor ahead of the scheduled time
  - `SendAppointmentReminders` artisan command (`appointments:send-reminders --hours=24`) — scheduled hourly; finds scheduled appointments in the reminder window and dispatches notifications
  - `PatientController` — index, show (with appointment history), create, store, edit, update, destroy; create/edit/destroy restricted to admins
  - `AppointmentController` — monthly calendar, show, create, store, edit, update, cancel, complete; technicians are read-only; doctors can only manage their own appointments
  - Four form request classes: `StorePatientRequest`, `UpdatePatientRequest`, `StoreAppointmentRequest`, `UpdateAppointmentRequest`
  - Routes: `/appointments/*` and `/patients/*` gated by `module:appointments`
  - Views: `appointments/calendar`, `appointments/show`, `appointments/create`, `appointments/edit`, `patients/index`, `patients/show` (with appointment history table), `patients/create`, `patients/edit`
  - Nav link shown to users with a valid appointments license
  - EN + RO translations for all appointments and patients UI strings
  - 48 new tests (267 total, 497 assertions) covering module gating, CRUD, authorization boundaries, calendar scoping, status transitions, and reminder command

- **Phase 3 — Inventory Management module** (`module:inventory`, gated by `HasModule` middleware)
  - `InventoryItem` model: name, description, quantity, unit, category, low-stock threshold; `isLowStock()` helper
  - `InventoryUsage` model: tracks quantity used, who used it, linked work job, and notes
  - `inventory_items` and `inventory_usages` migrations
  - `InventoryService` — create, update, restock, delete items; log usage with automatic stock deduction; triggers `LowStockNotification` when stock ≤ threshold after usage
  - `LowStockNotification` — mail notification sent to all admins when an item hits low-stock threshold
  - `InventoryItemController` (general) — index, show, log usage (all users with inventory license)
  - `Admin\InventoryItemController` — create, store, edit, update, restock, destroy (admin + inventory license)
  - Four form request classes: `StoreInventoryItemRequest`, `UpdateInventoryItemRequest`, `RestockInventoryItemRequest`, `StoreInventoryUsageRequest`
  - Routes: `/inventory/*` and `/admin/inventory/*` both gated by `module:inventory`
  - Views: `inventory/index`, `inventory/show` (with usage history, log-usage form, restock form), `admin/inventory/create`, `admin/inventory/edit`
  - Nav link shown to users with a valid inventory license
  - EN + RO translations for all inventory UI strings
  - 39 new tests covering module gating, CRUD, usage logging, low-stock notifications, and authorization boundaries

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
