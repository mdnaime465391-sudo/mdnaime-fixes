# Plesk deployment guide (VPS: Apache + nginx proxy, PHP-FPM, MySQL)

This app deploys cleanly to a Plesk VPS. The package ships production
`vendor/`, so **Composer is not required on the server**. No Node build is
needed — CSS/JS are plain files in `public/assets`.

The path names below assume a Plesk subscription whose document root you set
to `httpdocs/public`. If your subscription home differs, substitute accordingly.

---

## 1. Domain, certificate, PHP

1. **Add the domain** in Plesk (Websites & Domains → Add Domain).
2. **Issue a Let's Encrypt certificate**: Websites & Domains → SSL/TLS
   Certificates → Install a free certificate (Let's Encrypt). Tick
   "Redirect from http to https".
3. **PHP settings**: Websites & Domains → PHP Settings:
   - PHP version **8.2 or 8.3** (FPM served by nginx/Apache).
   - Extensions required: `gd`, `fileinfo`, `mbstring`, `pdo_mysql`
     (they are enabled by default on most Plesk builds — verify under
     "php.ini" / extension list; `zend.assertions` can stay default).
   - Common settings:
     - `upload_max_filesize = 8M` (or more)
     - `post_max_size = 8M` (or more — must be ≥ `upload_max_filesize`)
     - `max_execution_time = 60` (image processing needs headroom)
4. **SSH access**: enable "SSH Access" for the subscription (Websites &
   Domains → SSH Access) — set it to `/bin/bash`, not "nologin", so the
   artisan commands below can run.

## 2. Create the database

1. Databases → Add Database:
   - Database name: e.g. `mdnaime`
   - Related site: your domain
   - Create a database user + strong password. Note all three values.
2. Keep the values for the `.env` step: `DB_DATABASE`, `DB_USERNAME`,
   `DB_PASSWORD`. `DB_HOST` is `localhost` on Plesk.

## 3. Upload the application

Upload the **contents** of the `mdnaime/` folder inside the deploy zip
directly into `httpdocs/` (via FTP/SFTP, or rsync over SSH):

```bash
rsync -av --delete mdnaime/ user@server:/var/www/vhosts/<domain>/httpdocs/
```

Resulting layout:

```
httpdocs/
  app/  bootstrap/  config/  database/  deploy/  docs/  public/  resources/
  routes/  storage/  vendor/  artisan  composer.json  .env.example  ...
```

> Upload tools should copy hidden files too (`.env.example`, `.htaccess`).
> File Manager in Plesk can upload a zip and extract it in place.

## 4. Document root

Websites & Domains → **Hosting Settings** → Document root:
`httpdocs/public` — the **`/public` subfolder, not `httpdocs` itself**.

If you cannot reach the site after this, give Plesk a minute to regenerate
the nginx/Apache config.

## 5. Configure `.env`

```bash
cd /var/www/vhosts/<domain>/httpdocs
cp .env.example .env
nano .env   # or edit via Plesk File Manager
```

Fill in:

| Key | Value |
|---|---|
| `APP_ENV` | `production` |
| `APP_URL` | `https://your-domain.com` (no trailing slash) |
| `APP_DEBUG` | `false` |
| `DB_*` | values from step 2 |
| `ADMIN_EMAIL` | your admin login email |
| `ADMIN_PASSWORD` | a strong password, min 12 chars (used by the first seed only — **remove it after seeding**) |
| `MAIL_*` | your SMTP account (`MAIL_SCHEME=smtp` for port 587, `smtps` for 465) |
| `SESSION_SECURE_COOKIE` | `true` — **requires working HTTPS**; only set `false` while testing plain http |

Then generate the key:

```bash
/opt/plesk/php/8.3/bin/php artisan key:generate
```

(Use `php` if the Plesk PHP binary is already on your PATH; adjust the
version in the path to what you selected in step 1.)

## 6. Run the artisan commands

From `httpdocs/`:

```bash
PHP_BIN=/opt/plesk/php/8.3/bin/php

# Migrate + first seed (seeder fails loudly in production if ADMIN_PASSWORD is missing/short)
$PHP_BIN artisan migrate --force
$PHP_BIN artisan db:seed

# After seeding, remove ADMIN_PASSWORD from .env

# Storage link + caches
$PHP_BIN artisan storage:link
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache

# Or all of the above (migrate + link + caches, never seeds):
bash deploy/deploy.sh
```

**Subsequent releases**: upload the new files, then
`PHP_BIN=/opt/plesk/php/8.3/bin/php bash deploy/deploy.sh`.

## 7. Permissions

Plesk runs PHP-FPM as the subscription's system user, so the default file
ownership already works. Ensure these directories are **writable by that
user** (755 is normally enough; if not, 775 — never 777):

```
storage/
storage/app/public/
storage/framework/cache/
storage/framework/sessions/
storage/framework/views/
storage/logs/
bootstrap/cache/
```

Via SSH, from `httpdocs/`:

```bash
find storage bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
find storage bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
```

Do **not** use `chown www-data` or `chmod -R 777` on Plesk — the subscription
user must own the files, and world-writable files are a security risk.

## 8. Storage link

`php artisan storage:link` creates `public/storage` → `storage/app/public`.
Uploaded images (project covers, gallery, avatars, settings images) are served
through it.

If it reports success but images 404, verify the link:

```bash
ls -la public/storage
```

**Fallback when symlinks are restricted** (rare on Plesk, but possible when
`symlink()` is in `disable_functions`):

1. SSH option — create the link yourself:
   ```bash
   ln -s "$(pwd)/storage/app/public" "$(pwd)/public/storage"
   ```
2. If `symlink()` is disabled in the PHP settings, remove it from
   `disable_functions` (PHP Settings → php.ini → disable_functions) and retry,
   or serve uploaded files by copying: add a cron/one-off
   `rsync -a --delete storage/app/public/ public/storage/` after uploads.
   The copy approach delays new uploads until the next sync — the symlink is
   strongly preferred.

## 9. Scheduler / queue

- No cron is required: nothing in the app is scheduled by default.
- Mail uses `QUEUE_CONNECTION=sync`. A slow SMTP server delays the contact
  form response slightly but never breaks it (mail failures are logged and the
  inquiry is still saved). Move to `database` + a queue worker later if needed.

## 10. After first login

1. Log in at `https://your-domain.com/admin/login` with `ADMIN_EMAIL` +
   `ADMIN_PASSWORD`.
2. Change the password in **Admin → Change password** (or run
   `php artisan portfolio:admin`).
3. Replace the sample projects / demo credentials / seeded settings with real
   content in the admin panel.

---

## Troubleshooting

### 500 — Internal Server Error

1. Check the Laravel log: `storage/logs/laravel.log`.
2. `APP_DEBUG=false` hides details — inspect the log, don't enable debug on
   production.
3. Common causes:
   - `APP_KEY` missing → run `php artisan key:generate`.
   - Config cached before `.env` was complete → run `php artisan config:cache`
     again after fixing `.env`.
   - `storage/` or `bootstrap/cache/` not writable → section 7.
   - Missing PHP extension (`gd`/`pdo_mysql`/…) → PHP Settings.

### 404 on every page (or on `public/` paths)

- Document root must be `httpdocs/public`, not `httpdocs`. Check Hosting
  Settings.
- If you see Laravel's 404 page (not Plesk's), routing works — the URL/slug
  simply doesn't exist.

### 404 only for uploaded images (`/storage/...`)

- `public/storage` symlink missing → section 8.
- Images uploaded before migration/seed were re-run → re-upload from the
  admin panel.

### Login loop / redirected back to the login form

- `SESSION_SECURE_COOKIE=true` but the site is served over plain http →
  finish the HTTPS setup (certificate + redirect) rather than disabling the
  cookie flag.
- `APP_URL` mismatched with the real domain (trailing slash, wrong scheme) →
  fix `.env`, then `php artisan config:cache`.
- Stale sessions after an `APP_KEY` change → users simply need to log in
  again; not an error.
- `sessions` table missing → run `php artisan migrate --force`
  (`SESSION_DRIVER=database`).

### Contact form submits but no email arrives

- Check `MAIL_*` values and the provider's SMTP credentials; port 587 wants
  `MAIL_SCHEME=smtp`, port 465 wants `smtps`.
- The admin notification goes to the email set in **Admin → Settings**,
  falling back to `MAIL_FROM_ADDRESS`.
- Failures are logged in `storage/logs/laravel.log` — the visitor still sees
  success, and the inquiry is saved in the admin inbox.
