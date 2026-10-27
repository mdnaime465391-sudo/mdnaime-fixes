# mdnaime.design — Laravel app, admin panel & public site

A complete Laravel 12 application behind the portfolio frontend: a custom
admin panel for projects, services, testimonials, inquiries, site settings and
the About page, plus the Blade-rendered public site it feeds.

No CSS framework, no admin starter kit. The admin shares the public site's
design language — warm near-black surfaces, one amber signal, Instrument
Serif display type, Instrument Sans body, JetBrains Mono metadata — with a
proper light theme.

## What is inside

```
app/
  Console/Commands/           portfolio:admin — create or reset the admin user
  Http/Controllers/Admin/     Dashboard, projects + media, services,
                              testimonials, inquiries, settings, about,
                              account (change password)
  Http/Controllers/Auth/      Login (throttled), forgot/reset password
  Http/Controllers/Site/      Home, projects, case study, services, about,
                              contact, sitemap
  Http/Controllers/Api/       Read-only JSON for published content + inquiry POST
  Http/Requests/              Validation (admin + public)
  Http/Middleware/            EnsureAdmin, SecurityHeaders
  Http/Resources/             API resources
  Models/                     Project, ProjectMedia, Service, Testimonial,
                              Inquiry, Setting, AboutPage, User
  Policies/                   Single-admin gates per content model
  Support/                    ImageService (WebP), ContentCache, HtmlSanitizer
  Notifications/NewInquiry.php
database/
  migrations/                 Laravel defaults + content schema + about page
  seeders/                    AdminSeeder (safe upsert), ContentSeeder
                              (only-if-empty defaults), DemoProjectsSeeder
                              (9 sample projects; skipped in production
                              unless SEED_DEMO=true)
  factories/                  Project, Service, Testimonial, User
resources/views/
  admin/                      Custom panel (dark/light)
  site/                       Public Blade pages
  emails/new-inquiry.blade.php
  errors/404, 403
public/assets/
  css/portfolio.css           Public design system (shared with the prototype)
  css/site.css                Integration additions
  css/admin.css               Admin design system
  js/site.js                  Public motion + filters + form
  js/admin.js                 Theme, editor, media ordering, repeaters
deploy/
  deploy.sh                   Migrations, storage link, caches (Plesk-friendly,
                              never seeds; no chown; composer only with RUN_COMPOSER=1)
  examples/nginx.conf         Reference nginx config (NOT for Plesk)
docs/
  PLESK-DEPLOY.md             Step-by-step Plesk VPS deployment
  schema.md                   Schema reference
tests/Feature/                Feature tests
```

## Requirements

- PHP 8.2+ with `gd`, `fileinfo`, `mbstring`, `pdo_mysql`
- MySQL 8 (SQLite is fine for local testing)
- Composer 2
- No Node build step — the CSS and JS are plain files in `public/assets`

## Quick start

```bash
cp .env.example .env
# set APP_ENV=local, APP_DEBUG=true, APP_URL=http://localhost:8000
# set DB_DATABASE / DB_USERNAME / DB_PASSWORD
# set ADMIN_EMAIL + ADMIN_PASSWORD (min 12 chars) for the first seed
# SESSION_SECURE_COOKIE=false for local http

composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Public site: `http://localhost:8000`  
Admin: `http://localhost:8000/admin/login`

**Seeded admin** — created from `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env`.
In non-production only, a development default password is applied when
`ADMIN_PASSWORD` is unset; **production seeding fails loudly** without a
strong password.

After the first seed, remove `ADMIN_PASSWORD` from `.env`. To reset a lost
admin password at any time (works without tinker):

```bash
php artisan portfolio:admin            # prompts for email + password
php artisan portfolio:admin --email=you@example.com --password='LongEnough123'
```

Or use **Admin → Change password** in the panel (signs out other sessions).

**Seeder safety** — `php artisan db:seed` is idempotent and safe to re-run:

- `AdminSeeder` creates the admin only (never deletes/renames a user; resets
  the password only when `ADMIN_PASSWORD` is set).
- `ContentSeeder` inserts default settings, about page, services and
  testimonials **only when the table is empty**; it never deletes or
  truncates, and never touches inquiries.
- `DemoProjectsSeeder` inserts the 9 sample projects with `firstOrCreate`
  (your edits to them are preserved). It is **skipped in production** unless
  `SEED_DEMO=true` is set in `.env`.

For a clean local database use SQLite: set `DB_CONNECTION=sqlite`, touch
`database/database.sqlite`, and leave `DB_DATABASE` blank or point it at the
file.

## Admin modules

| Module | Capabilities |
|---|---|
| Dashboard | Project / inquiry / service / testimonial counts, unread inquiries, recently edited projects, quick actions |
| Projects | Create, edit, delete, publish/unpublish switch, sort order, summary, categories, accent, cover + thumbnail, rich-text story fields, feature and tech-stack repeaters |
| Project media | Three groups (key screens, Figma, Elementor), multi-file upload, drag-to-reorder, caption autosave, delete |
| Services | Create, edit, delete, publish switch, rich description, feature repeater |
| Testimonials | Create, edit, delete, publish switch, avatar upload |
| Inquiries | Search, status filters, detail view (auto-marks read), mark read/replied/unread, delete, CSV export for the current filter |
| Settings | Identity, availability, location, contact, social links, booking URL, SEO defaults, logo, favicon, social image (stored in its original format for crawler compatibility) |
| About page | Hero, portrait, story, principles, toolkit, SEO, and per-section visibility switches (story, principles, toolkit, testimonials, CTA) |

Only published projects, services and testimonials reach the public site.
Drafts 404 on their case-study URL.

## Public routes

| Route | Page |
|---|---|
| `/` | Home: hero, marquee, selected projects, services, process, toolkit, testimonials, CTA |
| `/projects` | Filterable project index with counts, progressive show-more/show-less |
| `/projects/{slug}` | Case study, galleries, features, live-preview block with demo access |
| `/services` | Both services rendered from the model |
| `/about` | About page with admin-controlled section visibility |
| `/contact` | Validated inquiry form (honeypot + rate limit) |
| `/sitemap.xml` | Published pages and projects |
| `/up` | Laravel health check |

## API (read-only + inquiry)

```
GET  /api/projects
GET  /api/projects/{slug}
GET  /api/services
GET  /api/testimonials
GET  /api/settings
POST /api/inquiries
```

All reads are published-only. The inquiry endpoint is throttled to five
requests per minute per IP and accepts the same honeypot field (`website`) as
the HTML form.

## Content pipeline

- **Images** are validated (mime + 4 MB), resized and converted to WebP at
  upload time (`App\Support\ImageService`). Full image + `-thumb` variant on
  the `public` disk; the DB stores paths only. Favicons and social share images are stored in their original format
  (PNG/ICO/JPEG/WebP) so crawlers and browsers handle them reliably; SVG is
  rejected for security.
- **Rich text** is sanitised through an allow-list (`HtmlSanitizer`), not
  trusted HTML.
- **Public reads** use `scopePublished` and eager-load media to avoid N+1.
- **Caching** — home, work, services and about payloads are cached for ten
  minutes (`ContentCache`). Project, media, service, testimonial, setting and
  about mutations bust the relevant keys. Use `php artisan portfolio:refresh-cache`
  to clear all public payloads.
- **Email** — a new inquiry triggers `NewInquiry` to the address in Settings
  (falling back to `MAIL_FROM_ADDRESS`). Mail failures are reported but never
  block the inquiry from being stored. Queue is `sync` by default; switch to a
  queue worker if you want the request to return before SMTP finishes.

## Deployment

- **Plesk VPS (Apache + nginx proxy, PHP-FPM)** — follow
  `docs/PLESK-DEPLOY.md`. Plesk generates its own web-server config; do not
  install a custom nginx file there.
- **Generic VPS** — see `DIRECT-DEPLOY.md`; a reference nginx config (not for
  Plesk) lives in `deploy/examples/nginx.conf`.

`deploy/deploy.sh` is Plesk-friendly by default: no `chown`, no Composer
(set `RUN_COMPOSER=1` to opt in), `PHP_BIN` override for
`/opt/plesk/php/8.3/bin/php`, migrations without seeding, a tolerant
`storage:link`, and the full cache build. The deploy script runs
`config:cache`, `route:cache`, `view:cache`, `event:cache` and clears public
content caches — all verified against this codebase.

## Security

- Single admin flag (`users.is_admin`) behind `auth` + `admin` middleware.
- Login throttled at 5 failed attempts per email+IP per minute, plus route
  throttle; session regenerated on success and invalidated on logout.
- CSRF on every state-changing route; password reset uses Laravel's signed
  token broker; reset tokens are single-use.
- Public inquiry form: honeypot + `throttle:inquiries` (5/min/IP) + server-side
  validation; IP and user agent are retained for audit.
- Security headers middleware (`nosniff`, `SAMEORIGIN`, referrer policy,
  permissions policy, HSTS when HTTPS).
- Admin content is sanitised; uploads are re-encoded and never executed.
- `/admin` is disallowed in `robots.txt` and marked `noindex, nofollow`.

## Tests

```bash
php artisan test
```

Covers admin access control, publish toggles, every admin screen rendering,
project creation with cover + gallery uploads and sanitisation, service /
testimonial / settings / about writes, published-only public reads, case-study
live preview, sitemap content, inquiry validation (including mail-failure
resilience), storage + notification, the honeypot path, seeder idempotency
and production guards, the `portfolio:admin` command, and the change-password
flow.

## Notes before launch

- The seeded project names, outcomes and testimonials are realistic sample
  content — replace with real client work.
- Seeded live-preview URLs (`bookflow.example.com`, `clientvault.example.com`,
  `pulse.example.com`) and demo credentials are placeholders. Swap them in the
  project editor.
- Set the real domain in `APP_URL`; canonical/OG URLs are generated from it.
- The admin account comes from `ADMIN_EMAIL` / `ADMIN_PASSWORD`; change the
  password via Admin → Change password after first login (or
  `php artisan portfolio:admin`).
- Proxy trust is enabled for all addresses (`trustProxies(at: '*')`) so HTTPS
  and client IPs are detected behind Plesk's nginx. If you front the app with
  an untrusted network, narrow it.
- Google Fonts are loaded from the network. If you need a fully self-hosted
  install, download Instrument Serif/Sans and JetBrains Mono into
  `public/assets/fonts` and update the two font links.
