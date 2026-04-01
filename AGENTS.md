# Agent Guidelines

## Setup
Before making changes, ensure the environment is working:
```bash
docker compose up --build -d
```

## Making Changes
1. Create a new branch for each task
2. Follow the coding standards in `.github/copilot-instructions.md`
3. Run migrations after schema changes: `docker compose exec app php artisan migrate`
4. Compile assets after frontend changes: `docker compose exec app npm run build` (or rebuild the image)

## Running Tests
```bash
docker compose exec app php artisan test
```
All tests must pass before opening a pull request.

## Artisan Commands
```bash
# Run a command inside the container
docker compose exec app php artisan <command>

# Examples
docker compose exec app php artisan make:controller UserController
docker compose exec app php artisan make:model Post -m
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
```
