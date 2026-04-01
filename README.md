# DocTech Project

A Laravel web application that connects **doctors** and **technicians** through a shared work-job calendar. Doctors request technical jobs, technicians manage and update their status — all within a role-based access system administered by admins.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.4 · Laravel 13 |
| Database | MySQL 8.4 |
| Frontend | Blade · Tailwind CSS v4 · Vite |
| Infrastructure | Docker · Docker Compose · Nginx · Supervisor |

---

## Features

### Role-Based Access Control

Three user roles with separate dashboards and permissions:

| Role | Capabilities |
|---|---|
| **Admin** | Manage users, view all work jobs on the calendar |
| **Doctor** | Create & manage their own work job requests, assign to technicians |
| **Technician** | View assigned work jobs, update job status |

### Work Jobs Calendar

- Monthly calendar view, navigable by month
- Colour-coded jobs by status: **Pending** (yellow), **In Progress** (blue), **Done** (green), **Cancelled** (red)
- Click any day to pre-fill the date when creating a new job
- Doctors can create, edit and delete their own jobs
- Technicians can update the status of jobs assigned to them

### User Management (Admin)

- Paginated user list
- Create new users with name, email, password and role assignment

---

## Getting Started

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/)

### Run the App

```bash
# 1. Clone the repository
git clone https://github.com/andybab44/DocTechProject.git
cd DocTechProject

# 2. Build and start the containers
docker compose up --build
```

The app will be available at **http://localhost:8000**

> On first run the entrypoint script automatically runs `php artisan migrate` and seeds the database.

---

## Useful Commands

All commands run inside the app container:

```bash
# Run database migrations
docker compose exec app php artisan migrate

# Run tests
docker compose exec app php artisan test

# Open a Tinker shell
docker compose exec app php artisan tinker

# Rebuild after changing PHP dependencies or Docker config
docker compose up --build
```

---

## Project Structure

```
app/
├── Enums/
│   ├── Role.php              # Admin | Doctor | Technician
│   └── WorkJobStatus.php     # Pending | InProgress | Done | Cancelled
├── Http/
│   ├── Controllers/
│   │   ├── Admin/UserController.php
│   │   ├── Auth/LoginController.php
│   │   ├── DashboardController.php
│   │   └── WorkJobController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/
│       ├── Admin/StoreUserRequest.php
│       ├── StoreWorkJobRequest.php
│       └── UpdateWorkJobRequest.php
├── Models/
│   ├── User.php
│   └── WorkJob.php
├── Policies/
│   └── UserPolicy.php
└── Services/
    ├── AuthService.php
    ├── UserService.php
    └── WorkJobService.php
```

---

## Environment

The Docker Compose stack injects all required environment variables automatically. For local development outside Docker, copy `.env.example`:

```bash
cp .env.example .env
php artisan key:generate
```

---

## Branching Strategy

| Branch | Purpose |
|---|---|
| `main` | Stable production-ready code |
| `develop` | Integration branch — PRs merge here |
| `feature/*` | Individual feature branches |
