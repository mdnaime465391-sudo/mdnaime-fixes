# Database schema — mdnaime portfolio

MySQL 8, utf8mb4. Fresh Laravel installs ship `users`, `password_reset_tokens`
and `sessions` migrations — keep those; everything below is additive.

## Entity relationship

```
users (admin, 1 row initially)
settings (singleton row, id = 1)
projects 1──n project_media   (gallery | figma | elementor)
services
testimonials
inquiries
```

## Tables

### users (base Laravel + one addition)
| column | type | notes |
|---|---|---|
| *(base Laravel columns)* | | name, email, password, timestamps |
| `is_admin` | boolean, default 0 | only `is_admin = 1` may pass the `admin` middleware |

### projects
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| title | varchar(191) | |
| slug | varchar(191) | unique, route key for `/projects/{slug}` |
| categories | json | e.g. `["Med Spa Websites","Figma UI/UX"]`; feeds the filter chips |
| summary | varchar(255) | one-line hero lead on the case study |
| cover_image | varchar(255) nullable | path on `public` disk (WebP) |
| thumbnail | varchar(255) nullable | auto-generated small WebP |
| accent_color | varchar(32) nullable | card accent for the mock-UI tiles |
| background | longtext nullable | rich text |
| challenge | longtext nullable | rich text |
| design_approach | longtext nullable | rich text |
| features | json | repeater array of strings |
| tech_stack | json | repeater array of strings |
| solution | longtext nullable | rich text |
| result | longtext nullable | rich text |
| live_preview_url | varchar(255) nullable | renders the LIVE PREVIEW block when set |
| demo_credentials | varchar(255) nullable | shown in the preview card |
| demo_notes | text nullable | shown in the preview card |
| sort_order | int, default 0 | admin drag order |
| is_published | boolean, default 0 | only published rows render publicly |
| published_at | timestamp nullable | set on first publish |
| created_at / updated_at | timestamps | |

Indexes: `slug` (unique), `is_published`, `sort_order`.

### project_media
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| project_id | FK → projects, cascade on delete | |
| type | enum `gallery`,`figma`,`elementor` | drives the three case-study galleries |
| path | varchar(255) | full-size WebP |
| caption | varchar(255) nullable | |
| sort_order | int, default 0 | drag to reorder |
| created_at / updated_at | timestamps | |

Indexes: `project_id, type, sort_order` composite.

### services
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| title | varchar(191) | |
| slug | varchar(191) unique | |
| short_description | varchar(255) | card copy |
| long_description | longtext | rich text |
| icon | varchar(64) nullable | icon key rendered by the frontend |
| features | json | repeater array |
| sort_order | int, default 0 | |
| is_published | boolean, default 0 | |
| timestamps | | |

### testimonials
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | varchar(191) | |
| role | varchar(191) nullable | |
| company | varchar(191) nullable | |
| quote | text | |
| avatar | varchar(255) nullable | WebP on `public` disk |
| sort_order | int, default 0 | |
| is_published | boolean, default 0 | |
| timestamps | | |

### inquiries
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | varchar(191) | |
| email | varchar(191) | |
| business_type | varchar(191) nullable | select from the form |
| service | varchar(191) nullable | select from the form |
| budget | varchar(64) nullable | select from the form |
| message | text | |
| status | enum `new`,`read`,`replied`, default `new` | admin workflow |
| ip_address | varchar(45) nullable | rate-limit audit |
| user_agent | varchar(255) nullable | |
| created_at | timestamp | no updated_at (immutable) |

Indexes: `status`, `created_at`.

### settings (singleton)
| column | type | notes |
|---|---|---|
| id | bigint PK, seeded as 1 | |
| site_title | varchar(191) | |
| tagline | varchar(255) | |
| seo_title | varchar(255) nullable | meta/OG default |
| seo_description | varchar(255) nullable | |
| email | varchar(191) | |
| phone | varchar(32) nullable | |
| social_links | json | `{label, url}` array |
| call_booking_url | varchar(255) nullable | scheduler link (may stay null — inquiry-first) |
| logo_path | varchar(255) nullable | upload |
| favicon_path | varchar(255) nullable | upload |
| timestamps | | |

## Conventions

- All image columns store **paths on the `public` disk**, converted to WebP by
  `App\Support\ImageService` at upload time; originals never touch the DB.
- Rich-text fields are stored as sanitized HTML (purified on save in the admin
  controllers — phase 2).
- Public queries filter `is_published = 1` via the `scopePublished` on each
  model and eager-load `media` to avoid N+1.

## Additions (admin + about page)

### about_pages (singleton)
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| eyebrow | varchar(80) | e.g. `ABOUT` |
| headline | varchar(191) | hero headline |
| intro | varchar(500) nullable | hero lead |
| portrait_path | varchar(255) nullable | WebP on `public` disk |
| story_heading | varchar(191) nullable | |
| story | longtext nullable | sanitised HTML |
| principles | json | `[{title, body}]` |
| toolkit | json | `[{name, role}]` |
| availability_label | varchar(80) nullable | |
| show_story | boolean | section switch |
| show_principles | boolean | section switch |
| show_toolkit | boolean | section switch |
| show_testimonials | boolean | section switch |
| show_cta | boolean | section switch |
| seo_title | varchar(255) nullable | |
| seo_description | varchar(500) nullable | |
| timestamps | | |

Cached as `about.page`; invalidated on save via `ContentCache`.

### settings (added columns)
| column | type | notes |
|---|---|---|
| availability_label | varchar(80) nullable | e.g. `Open for Q2 2026 projects` |
| location | varchar(120) nullable | e.g. `Dhaka, Bangladesh` |
| og_image_path | varchar(255) nullable | original PNG/JPEG/WebP for social sharing (1200×630 recommended) |

### Public cache keys

| Key | Written by | Invalidated by |
|---|---|---|
| `settings.current` | `Setting::current()` | Setting save |
| `about.page` | `AboutPage::current()` | About save |
| `public.home` | HomeController | Project/service/testimonial/about changes |
| `public.work` | WorkController | Project changes |
| `public.services` | ServiceController | Service changes |
| `public.about` | AboutController | About/testimonial changes |
| `public.footer.services` | Public layout composer | Service changes |

### Seeding note

`ContentSeeder` encodes JSON columns explicitly because it uses batch/query
builder inserts (`Project::insert`) instead of Eloquent model events. The
seeded admin flag is set with `forceFill` semantics via direct assignment, not
mass assignment.
