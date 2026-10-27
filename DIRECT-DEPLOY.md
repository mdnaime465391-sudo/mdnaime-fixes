# Direct deploy (production package)

The production zip ships `vendor/` — **no Composer and no Node are needed on
the server.**

- Deploying to **Plesk**? Follow `docs/PLESK-DEPLOY.md` instead (recommended).
- The generic steps below apply to any LAMP/LEMP host where you control the
  web server. A reference nginx config (not for Plesk) is kept in
  `deploy/examples/nginx.conf`.

## 1. Upload

Extract the zip and put the `mdnaime/` folder contents at your web root, e.g.
`/var/www/mdnaime`, then point the web server's document root at
`/var/www/mdnaime/public`.

```bash
unzip mdnaime-portfolio-direct-deploy.zip -d /var/www/
cd /var/www/mdnaime
```

## 2. Configure

```bash
cp .env.example .env
# edit: APP_ENV, APP_URL, DB_*, MAIL_*, ADMIN_EMAIL, ADMIN_PASSWORD
# SESSION_SECURE_COOKIE=true requires working HTTPS
php artisan key:generate
```

## 3. Install (no Composer needed)

```bash
php artisan migrate --force
php artisan db:seed          # creates the admin + default content; demo
                             # projects are skipped in production unless
                             # SEED_DEMO=true
# remove ADMIN_PASSWORD from .env after the first seed

php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
# or: bash deploy/deploy.sh   (migrate + link + caches; never seeds)
```

## 4. Permissions

Make `storage/` and `bootstrap/cache/` writable by the PHP process user:

```bash
chown -R www-data:www-data storage bootstrap/cache   # Ubuntu/nginx+FPM
chmod -R ug+rwX storage bootstrap/cache
```

(On Plesk, skip the chown — the subscription user already owns the files.)

## 5. Admin access

- URL: `https://your-domain.com/admin/login`
- Credentials: `ADMIN_EMAIL` + `ADMIN_PASSWORD` from `.env`.
- **Change the password immediately** via Admin → Change password.
- Lost access later? Recover with `php artisan portfolio:admin`
  (prompts for email + password; never deletes content).

## 6. Verify

- Public pages return 200 and content comes from the admin panel.
- Contact form stores an inquiry (Admin → Inquiries) and email failures are
  logged, never fatal.
- Uploaded images resolve under `/storage/...`.
