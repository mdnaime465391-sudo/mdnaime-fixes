#!/usr/bin/env bash
# Plesk-friendly deploy for a single-domain Laravel install.
#
# Defaults are tuned for Plesk VPS hosting (Apache + nginx proxy, PHP-FPM):
#   - No chown (Plesk runs PHP-FPM as the subscription's system user).
#   - No composer install by default (the package ships vendor/). Set RUN_COMPOSER=1 to run it.
#   - Never runs seeders (use `php artisan db:seed` deliberately, or portfolio:admin).
#
# Usage (via SSH):
#   cd /var/www/vhosts/<domain>/httpdocs
#   bash deploy/deploy.sh
#
# Overrides:
#   PHP_BIN=/opt/plesk/php/8.3/bin/php bash deploy/deploy.sh
#   RUN_COMPOSER=1 bash deploy/deploy.sh
#   SKIP_MAINTENANCE=1 bash deploy/deploy.sh   # do not toggle maintenance mode
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
RUN_COMPOSER="${RUN_COMPOSER:-0}"
SKIP_MAINTENANCE="${SKIP_MAINTENANCE:-0}"

cd "$APP_DIR"

echo "→ Using PHP: $("$PHP_BIN" -r 'echo PHP_VERSION;')"

if [ "$SKIP_MAINTENANCE" != "1" ]; then
    echo "→ Maintenance mode"
    "$PHP_BIN" artisan down --retry=30 || true
    # If any later step fails, never leave the site stuck in maintenance mode.
    trap '"$PHP_BIN" artisan up >/dev/null 2>&1 || true' EXIT
fi

if [ "$RUN_COMPOSER" = "1" ]; then
    echo "→ Dependencies (composer)"
    "$COMPOSER_BIN" install --no-dev --no-interaction --prefer-dist --optimize-autoloader
else
    echo "→ Dependencies skipped (vendor/ shipped with the package; set RUN_COMPOSER=1 to install)"
fi

echo "→ Migrations (no seed)"
"$PHP_BIN" artisan migrate --force

echo "→ Storage link"
# Does not fail the deploy when the link already exists or symlinks are
# restricted — see docs/PLESK-DEPLOY.md (Storage link section) for the fallback.
if ! "$PHP_BIN" artisan storage:link >/dev/null 2>&1; then
    echo "  storage:link could not run (already linked or symlink() disabled) — check public/storage and docs/PLESK-DEPLOY.md"
fi

echo "→ Caches"
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan event:cache
"$PHP_BIN" artisan portfolio:refresh-cache

echo "→ Permissions (group-writable, no ownership changes)"
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

if [ "$SKIP_MAINTENANCE" != "1" ]; then
    echo "→ Back online"
    "$PHP_BIN" artisan up
fi

echo "Deploy complete."
