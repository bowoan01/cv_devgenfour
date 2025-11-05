# Devgenfour Company Profile – Implementation Plan

## 1. Scope Recap & Approach

* Build a **company profile website** (Home, About, Services, Portfolio, Contact; Blog = Phase 2) along with a **custom admin panel** for content management.
* Stack: **Laravel 12 (PHP 8.3+)**, **MySQL 8+ / PostgreSQL 18+**, **Bootstrap 5**, **Vanilla JS**, **jQuery AJAX**, **Yajra DataTables**, **Spatie Permission**, **Laravel Breeze**.
* No Vite / Node — all assets are loaded via **CDN** to prevent build errors.
* Focus: responsive design, high performance, strong security, and strict separation between public and admin areas.

---

## 2. High-Level Architecture

```
Browser (Public + Admin)
        │
        ▼
Laravel 12 ── Controllers ── Policies ── FormRequests ── Services
        │             │
        │             ├── Redis (cache, queue, rate limit)
        │             └── Mail (Mailgun / Postmark / SES)
        ▼
MySQL 8+ / PostgreSQL 18+ (Eloquent ORM + Spatie Roles/Permissions)
```

* Public controllers render Blade views using Bootstrap and cached data.
* Admin routes (`/admin`) run through `auth`, `verified`, and `role/permission` middleware, exposing **AJAX JSON** endpoints.
* Queues handle email and media jobs; a scheduler manages sitemap generation and cleanup tasks.
* Storage uses the `public` disk or AWS S3 (via `storage:link`).

---

## 3. Environment & Tooling

| Environment    | Purpose            | Configuration                                                |
| -------------- | ------------------ | ------------------------------------------------------------ |
| **Local**      | Development & QA   | Laravel Sail / Valet + Telescope enabled                     |
| **Staging**    | Stakeholder review | Forge droplet (HTTPS + seeded demo data)                     |
| **Production** | Live traffic       | Forge + Envoyer (zero-downtime) + Supervisor (queue workers) |

**Tooling & Packages**

* `spatie/laravel-permission` – role & permission management
* `yajra/laravel-datatables-oracle` – server-side table handling
* `spatie/laravel-seo` + `spatie/laravel-sitemap` – SEO utilities
* `laravel/breeze` – authentication scaffolding
* `laravel/telescope` (dev) + `sentry/sentry-laravel` (prod)

**Frontend (CDN-only)**

```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js" defer></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js" defer></script>
<script src="/js/app.js" defer></script>
```

---

## 4. Database Design

| Table               | Key Columns                                                                                   | Relationships                    | Purpose                       |
| ------------------- | --------------------------------------------------------------------------------------------- | -------------------------------- | ----------------------------- |
| users               | name, email, password                                                                         | Spatie pivot                     | Admin / Editor authentication |
| roles / permissions | name, guard_name                                                                              | Many-to-many with users          | Access control                |
| services            | title, slug, short_description, description, icon_path, order, is_published                   | —                                | Service data                  |
| projects            | title, slug, client, category, tech_stack (json), summary, results, cover_image, is_published | hasMany project_images           | Portfolio projects            |
| project_images      | project_id, path, caption, order                                                              | belongsTo projects               | Image gallery                 |
| teams               | name, role_title, bio, photo_path, social_links (json), order_index, is_visible               | —                                | Team profiles                 |
| contact_messages    | name, email, company, phone, message, status, handled_by, handled_at                          | belongsTo users                  | Contact messages              |
| posts (Phase 2)     | title, slug, excerpt, body, cover_image, status, published_at, author_id                      | belongsTo users                  | Blog posts                    |
| tags (Phase 2)      | name, slug                                                                                    | Many-to-many with posts/projects | Dynamic categories            |
| settings            | key, value, type, group                                                                       | —                                | Global metadata               |

---

## 5. Routing & Controllers

**Public (`routes/web.php`)**

```
GET /                → HomeController@index
GET /about           → AboutController@index
GET /services        → ServiceController@index
GET /services/{slug} → ServiceController@show
GET /portfolio       → PortfolioController@index
GET /portfolio/{slug}→ PortfolioController@show
GET /contact         → ContactController@show
POST /contact        → ContactController@submit
```

**Admin (`routes/admin.php`, prefix `/admin`)**

```
GET /dashboard       → DashboardController@index
resources: services, projects, teams
contacts: index, show, markAsRead, delete
profile: update, change-password
```

Middleware: `web`, `auth`, `verified`, `role:Admin|Editor`.
API routes (`routes/api.php`) only if needed; protect with `auth:sanctum`.

---

## 6. Frontend Implementation

* Main layouts: `layouts/app.blade.php` and `layouts/admin.blade.php`.
* Use **Bootstrap 5 utilities** and Blade components (`x-card`, `x-form`, `x-alert`).
* **Vanilla JS** for mobile navigation, form validation, and flash toasts.
* **jQuery AJAX** for CRUD without page reloads, returning standard JSON responses:

  ```json
  { "success": true, "message": "Data saved", "data": { ... } }
  ```
* **Yajra DataTables** for server-side processing (pagination, filtering, sorting).
* Optimizations: lazy-loaded images, font prefetching, and asynchronous analytics.

---

## 7. Admin Panel UX Details

* Fixed sidebar and breadcrumb-based content area.
* DataTables with action buttons (edit/delete/publish).
* CRUD forms via AJAX modals with FormRequest validation.
* **Spatie Permission** controls visibility — `Editor` cannot delete or publish.
* Image uploads with preview (FileReader) and drag-and-drop reordering (`PUT /reorder`).
* Flash notifications via Vanilla JS, with `aria-live` accessibility support.

---

## 8. Security & Compliance

* **HTTPS + HSTS** in production; trusted proxies (Forge).
* CSRF tokens for all forms; XSS sanitization via HTML Purifier.
* **Rate-limiting** on login and contact forms; **hCaptcha / reCAPTCHA v3**.
* Passwords hashed with Argon2id; optional 2FA (Breeze).
* Security headers (CSP, X-Frame-Options, Referrer-Policy).
* Scheduled deletion of old contact messages for privacy.
* Audit logging via Telescope / Sentry.

---

## 9. Content Management Flow

1. **Services** → DataTables → create/edit modal → AJAX save → drag-reorder.
2. **Portfolio** → multi-step form → upload cover/gallery via AJAX → publish toggle.
3. **Team** → CRUD + JSON social links + visibility toggle.
4. **Blog (Phase 2)** → Trix/TinyMCE editor, auto-slug, tagging support.
5. **Contacts** → Unread DataTable → detail panel → mark handled / archive / CSV export.

---

## 10. Testing Strategy

* **Unit Tests** → Model relations, scopes, permission policies.
* **Feature Tests** → Public page rendering, contact form validation, admin CRUD JSON.
* **Integration Tests** → AJAX CRUD flows (`actingAs` roles), DataTables response, file uploads, queued mail.
* **Browser Tests (Dusk)** → Smoke tests: login, admin CRUD, contact submission.
* CI pipeline: GitHub Actions running `php artisan test`, `php artisan pint`, `larastan`.

---

## 11. Deployment Checklist

```
composer install
cp .env.example .env
php artisan key:generate
# set DB, MAIL, CAPTCHA, SENTRY
php artisan migrate --seed
php artisan storage:link
php artisan optimize
php artisan queue:work
# cron: * * * * * php artisan schedule:run
```

**Server (Forge + Envoyer)**

* PHP 8.3, Redis, Supervisor.
* Envoyer hooks: `composer install --no-dev`, `migrate --force`, `db:seed`, `storage:link`, `optimize`.
* Health check: HTTP 200 root, admin login works, queue active.

---

## 12. Timeline Alignment

| Phase                 | Duration | Focus                                    |
| --------------------- | -------- | ---------------------------------------- |
| Research & Sitemap    | 1 week   | Sitemap & initial content                |
| Wireframe & UI Design | 2 weeks  | Bootstrap mockups & admin UX             |
| Development           | 4 weeks  | CRUD AJAX + DataTables + SEO + Analytics |
| Testing & Launch      | 1 week   | QA, performance, final deployment        |

---

## 13. Open Questions / Decisions Needed

1. Choice of analytics (GA4 or Plausible) and cookie consent requirement.
2. Email provider (Mailgun / Postmark / SES) and sender domain.
3. Image guidelines (resolution & file size).
4. Blog Phase 2 schedule and editorial workflow.
5. Single or multi-language (i18n) support.
6. Final approval of copy and brand assets.

---

### ✅ Build Contract (CDN-Only)

**No Node / Vite. Use the CDN assets listed above. Do not include Tailwind, Alpine, Webpack, Mix, or npm scripts.**
All UI interactions must be implemented with Bootstrap 5 + jQuery AJAX + Vanilla JS.
Follow the standard JSON response contract and server-side DataTables structure from controllers.

---
