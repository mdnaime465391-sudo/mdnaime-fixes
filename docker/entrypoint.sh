#!/usr/bin/env bash
set -e

cd /var/www/html

echo "==> MD Naime Portfolio - Initializing container environment..."

# 1. Setup .env file if missing
if [ ! -f .env ]; then
    echo "==> .env not found. Creating from .env.example..."
    cp .env.example .env
fi

# 2. Ensure storage and bootstrap directories have correct structure and permissions
echo "==> Setting up storage and bootstrap/cache permissions..."
mkdir -p storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache/data \
         storage/framework/testing \
         storage/logs \
         storage/app/public \
         bootstrap/cache

chmod -R 777 storage bootstrap/cache

# 3. Generate APP_KEY if missing
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "==> Generating application key..."
    php artisan key:generate --force
fi

# 4. Handle Database connectivity
DB_CONN=$(grep -E "^DB_CONNECTION=" .env | cut -d '=' -f2 | tr -d ' "\r' || echo "mysql")

if [ "$DB_CONN" = "mysql" ]; then
    DB_HOST_VAL=$(grep -E "^DB_HOST=" .env | cut -d '=' -f2 | tr -d ' "\r' || echo "db")
    DB_PORT_VAL=$(grep -E "^DB_PORT=" .env | cut -d '=' -f2 | tr -d ' "\r' || echo "3306")
    DB_USER_VAL=$(grep -E "^DB_USERNAME=" .env | cut -d '=' -f2 | tr -d ' "\r' || echo "portfolio_user")
    DB_PASS_VAL=$(grep -E "^DB_PASSWORD=" .env | cut -d '=' -f2 | tr -d ' "\r' || echo "portfolio_secret")

    echo "==> Waiting for MySQL at ${DB_HOST_VAL}:${DB_PORT_VAL}..."
    MAX_TRIES=30
    COUNT=0
    until php -r "try { new PDO('mysql:host=${DB_HOST_VAL};port=${DB_PORT_VAL}', '${DB_USER_VAL}', '${DB_PASS_VAL}'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
        COUNT=$((COUNT + 1))
        if [ $COUNT -ge $MAX_TRIES ]; then
            echo "==> WARNING: Database connection timed out after $MAX_TRIES seconds. Proceeding anyway..."
            break
        fi
        sleep 1
    done
    echo "==> Database connection verified."
elif [ "$DB_CONN" = "sqlite" ]; then
    echo "==> Setting up SQLite database..."
    mkdir -p database
    touch database/database.sqlite
    chmod 777 database/database.sqlite
fi

# 5. Storage symlink
if [ ! -L public/storage ]; then
    echo "==> Creating storage symlink..."
    php artisan storage:link || true
fi

# 6. Run database migrations & seeders
echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Running database seeders..."
php artisan db:seed --force || true

# 7. Refresh portfolio public cache
echo "==> Refreshing portfolio cache..."
php artisan portfolio:refresh-cache || true

echo "==> Startup completed successfully. Launching web server..."
exec "$@"
