# DocTech Roadmap

## Phase 1 — Core Enhancements
*Strengthen the existing foundation before building new features.*

### 1.1 Work Job File Attachments
Allow files (images, PDFs, reports) to be uploaded to a work job and downloaded by authorised users.

- File upload on job creation and edit forms (doctors/admins)
- Files stored via Laravel's storage system (local or S3-compatible)
- Download endpoint with ownership/role authorisation
- File type and size validation
- Display attached files on the job detail view

### 1.2 Admin User Control Page
Expand the existing user list into a full management interface.

- Edit user details (name, email, role)
- Reset/change user password
- Deactivate / reactivate accounts (soft disable without deletion)
- Filter and search users by role or name
- Paginated user activity overview (jobs created / assigned)

---

## Phase 2 — Licensing System
*Introduce a multi-tenant licensing layer to gate features per organisation.*

### 2.1 License Management
- `License` model: plan tier, expiry date, seat count, active status
- Licenses linked to an organisation/tenant
- Admin panel for managing licenses (create, renew, deactivate)
- License expiry notifications (email/in-app)

### 2.2 Module System
- Feature flags tied to license tier (e.g. `module:inventory`, `module:appointments`, `module:reviews`)
- `HasModule` middleware to gate routes by licensed module
- Module activation/deactivation per license from admin panel
- Graceful UI degradation when a module is not licensed (hidden nav items, access denied pages)

---

## Phase 3 — Technician Module: Inventory Management
*Licensed module — available to organisations with the `module:inventory` flag.*

- `InventoryItem` model: name, description, quantity, unit, category
- Technicians can view current stock levels
- Technicians can log item usage against a work job
- Admins can create, edit, and restock inventory items
- Low-stock alerts (threshold configurable per item)
- Inventory usage report per job / per time period

---

## Phase 4 — Doctor Module: Patient Appointments
*Licensed module — available to organisations with the `module:appointments` flag.*

- `Patient` model: name, date of birth, contact info, notes
- `Appointment` model: patient, doctor, scheduled time, status, notes
- Appointment calendar view (integrated with existing work job calendar)
- Doctors can create, reschedule, and cancel appointments
- Patients linked optionally to a work job
- Appointment reminder notifications (email)
- Appointment history per patient

---

## Phase 5 — Review System
*Licensed module — available to organisations with the `module:reviews` flag.*

- `Review` model: reviewer (doctor or technician), reviewee (doctor or technician), rating (1–5), comment, work job reference
- Doctors can review technicians on completed work jobs
- Technicians can review doctors on completed work jobs
- Reviews visible on user profile pages
- Aggregate rating displayed on user cards in admin panel
- Admin can moderate (hide/delete) reviews
- Reviews locked to `Done` or `Cancelled` job status only

---

## Cross-cutting Concerns
*Applied across all phases.*

| Concern | Approach |
|---|---|
| **Testing** | Feature + Unit tests required for every new module |
| **Authorisation** | All new routes gated by role middleware + policies |
| **Module gating** | `HasModule` middleware on all phase 3–5 routes |
| **Migrations** | New migrations per feature; never modify existing ones |
| **Service layer** | Business logic in `app/Services/`, not in controllers |
| **API readiness** | Design models and services to support a future REST API layer |
