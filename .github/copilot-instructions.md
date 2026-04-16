# Copilot Agent Instructions

## Project Overview
This is a **Laravel 13** application using:
- **PHP 8.4** (via `php:8.4-fpm-alpine`)
- **MySQL 8.4** (separate Docker container)
- **Vite + Tailwind CSS v4** for frontend assets
- **Docker + Docker Compose** for containerised deployment
- **Supervisor** to manage nginx, php-fpm, and the queue worker inside a single app container

## Project Structure
- `app/` — Laravel application code (Controllers, Models, etc.)
- `resources/` — Blade views, CSS, JS
- `routes/` — `web.php` for HTTP routes, `console.php` for Artisan commands
- `database/migrations/` — Database migrations
- `docker/` — Docker config files (nginx, supervisord, php.ini, entrypoint.sh)
- `Dockerfile` — Multi-stage build (Node for Vite assets, PHP for the app)
- `docker-compose.yml` — Defines `app` and `mysql` services

## Development Guidelines

### PHP / Laravel
- Follow **PSR-12** coding standards
- Follow **SOLID principles**: single responsibility, open/closed, Liskov substitution, interface segregation, dependency inversion
- Use **Laravel conventions**: Eloquent ORM, route model binding, form requests for validation
- Place business logic in **Service classes** under `app/Services/`, not in controllers
- Controllers should be thin — delegate to services
- Use **Laravel's built-in features**: queues, events, notifications, policies
- Always write **migrations** for database changes; never modify existing migrations
- Use `DB_CONNECTION=mysql` — SQLite is not used in this project

### Frontend
- Use **Tailwind CSS v4** utility classes
- JavaScript goes in `resources/js/`, CSS in `resources/css/`
- Run `npm run build` to compile assets (handled automatically in Docker)

### Docker
- The app runs on port **8000** (`http://localhost:8000`)
- MySQL credentials: host=`mysql`, db=`doctechproject`, user=`laravel`, password=`secret`
- To rebuild and restart: `docker compose up --build`
- To run Artisan commands inside the container: `docker compose exec app php artisan <command>`
- Storage and MySQL data are persisted via named Docker volumes

### Testing
- Use **PHPUnit** (already configured in `phpunit.xml`)
- Run tests with: `docker compose exec app php artisan test`
- Feature tests go in `tests/Feature/`, unit tests in `tests/Unit/`
- **All new features must be accompanied by tests** — do not submit code without coverage
- Write **Feature tests** for HTTP endpoints: assert correct status codes, redirects, and response content
- Write **Unit tests** for Service classes and complex business logic in isolation
- Use Laravel's `RefreshDatabase` trait to reset state between tests
- Use **model factories** (`database/factories/`) to generate test data — never hardcode IDs or emails
- Assert **authorization boundaries**: test that forbidden roles receive 403s and guests are redirected to login
- Test **happy paths and failure paths** (e.g. validation errors, unauthorised access, not-found resources)
- All tests must pass before opening a pull request: `docker compose exec app php artisan test`

## Changelog
- The project maintains a `CHANGELOG.md` in the root, following [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) format
- **Every new feature, bug fix, or breaking change must be recorded in `CHANGELOG.md`** before opening a pull request
- Add entries under the `## [Unreleased]` section using the appropriate subsection:
  - `### Added` — new features
  - `### Changed` — changes to existing behaviour
  - `### Fixed` — bug fixes
  - `### Removed` — removed features
  - `### Security` — security fixes
- When releasing, replace `[Unreleased]` with the version number and date (e.g. `[1.0.0] – 2026-05-01`)

## Environment
- Copy `.env.example` to `.env` for local development outside Docker
- In Docker, environment variables are injected via `docker-compose.yml`
- Never commit `.env` files
