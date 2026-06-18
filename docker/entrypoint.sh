#!/bin/sh
set -e

# ── Copy .env if it doesn't exist ──────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

cd /var/www/html

# ── Wipe stale bootstrap cache BEFORE any artisan call ────────────────────
# The bind-mounted ./bootstrap/cache may contain dev-only package references
# (e.g. laravel/pail) that aren't installed in the production image.
# Removing them here lets the first artisan command rebuild them cleanly.
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php

# ── Generate app key if not set ────────────────────────────────────────────
if [ -z "${APP_KEY}" ]; then
    php artisan key:generate --force
fi

# ── Wait for MySQL to be ready ─────────────────────────────────────────────
echo "Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
until php -r "
    \$dsn = 'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE');
    new PDO(\$dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
" 2>/dev/null; do
    echo "  MySQL not ready yet - retrying in 2s..."
    sleep 2
done
echo "MySQL is ready."

# ── Clear any stale bootstrap cache (safe on every boot) ──────────────────
php artisan package:discover --ansi 2>/dev/null || true

# ── Run migrations ─────────────────────────────────────────────────────────
php artisan migrate --force

# ── Seed database (skips existing records via firstOrCreate) ───────────────
php artisan db:seed --force

# ── Cache config/routes/views for performance (production only) ────────────
if [ "${APP_ENV}" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
fi

# ── Fix permissions ────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── Create supervisor log dir ──────────────────────────────────────────────
mkdir -p /var/log/supervisor

# ── Hand off to supervisor ─────────────────────────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
