#!/bin/sh
set -e

# ── Copy .env if it doesn't exist ──────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

cd /var/www/html

# ── Generate app key if not set ────────────────────────────────────────────
if grep -q "^APP_KEY=$" .env || grep -q "^APP_KEY=\"\"$" .env; then
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

# ── Run migrations ─────────────────────────────────────────────────────────
php artisan migrate --force

# ── Cache config/routes/views for performance ──────────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Fix permissions ────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── Create supervisor log dir ──────────────────────────────────────────────
mkdir -p /var/log/supervisor

# ── Hand off to supervisor ─────────────────────────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
