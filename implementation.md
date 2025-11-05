# Devgenfour Company Profile – Implementation Plan (Revised: Single `web.php`, No Prefix, No Blade Components)

## 1. Scope Recap & Approach

* Build a **company profile website** (Home, About, Services, Portfolio, Contact; Blog = Phase 2) plus a **custom admin panel** for content management.
* Stack: **Laravel 12 (PHP 8.3+)**, **MySQL 8+ / PostgreSQL 18+**, **Bootstrap 5**, **Vanilla JS**, **jQuery AJAX**, **Yajra DataTables**, **Spatie Permission**, **Laravel Breeze**.
* **No Vite/Node** — all assets are loaded via **CDN** to avoid build errors.
* Focus: responsive design, high performance, strong security, and clear separation of public vs. admin functionality (via middleware and route names).

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
* Admin actions are guarded by `auth`, `verified`, and `role/permission` middleware; endpoints return **AJAX JSON** for CRUD/DataTables.
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

## 5. Routing & Controllers (Single `routes/web.php`, No Prefix)

All routes live in **`routes/web.php`**. Use **middleware groups** (no URL prefix) and **clear route names** to separate public vs. admin.

**Public routes**

```php
// Public pages
Route::get('/', [Site\HomeController::class, 'index'])->name('home');
Route::get('/about', [Site\AboutController::class, 'index'])->name('about');
Route::get('/services', [Site\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [Site\ServiceController::class, 'show'])->name('services.show');
Route::get('/portfolio', [Site\PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [Site\PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/contact', [Site\ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [Site\ContactController::class, 'submit'])->name('contact.submit');
```

**Admin routes (no prefix, protected by middleware)**

```php
Route::middleware(['auth','verified','role:Admin|Editor'])->group(function () {
    // We keep namespaced controllers under App\Http\Controllers\Admin
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Services
    Route::get('/services/manage', [Admin\ServiceController::class, 'index'])->name('admin.services.index');
    Route::post('/services', [Admin\ServiceController::class, 'store'])->name('admin.services.store');
    Route::put('/services/{id}', [Admin\ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/services/{id}', [Admin\ServiceController::class, 'destroy'])->name('admin.services.destroy');

    // Projects
    Route::get('/projects/manage', [Admin\ProjectController::class, 'index'])->name('admin.projects.index');
    Route::post('/projects', [Admin\ProjectController::class, 'store'])->name('admin.projects.store');
    Route::put('/projects/{id}', [Admin\ProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/projects/{id}', [Admin\ProjectController::class, 'destroy'])->name('admin.projects.destroy');

    // Project Images (nested behaviour without URL prefix)
    Route::post('/projects/{id}/images', [Admin\ProjectImageController::class, 'store'])->name('admin.projects.images.store');
    Route::delete('/projects/images/{imageId}', [Admin\ProjectImageController::class, 'destroy'])->name('admin.projects.images.destroy');
    Route::put('/projects/{id}/reorder', [Admin\ProjectController::class, 'reorder'])->name('admin.projects.reorder');

    // Teams
    Route::get('/teams/manage', [Admin\TeamController::class, 'index'])->name('admin.teams.index');
    Route::post('/teams', [Admin\TeamController::class, 'store'])->name('admin.teams.store');
    Route::put('/teams/{id}', [Admin\TeamController::class, 'update'])->name('admin.teams.update');
    Route::delete('/teams/{id}', [Admin\TeamController::class, 'destroy'])->name('admin.teams.destroy');

    // Contacts
    Route::get('/contacts', [Admin\ContactMessageController::class, 'index'])->name('admin.contacts.index');
    Route::get('/contacts/{id}', [Admin\ContactMessageController::class, 'show'])->name('admin.contacts.show');
    Route::patch('/contacts/{id}/read', [Admin\ContactMessageController::class, 'markAsRead'])->name('admin.contacts.read');
    Route::delete('/contacts/{id}', [Admin\ContactMessageController::class, 'destroy'])->name('admin.contacts.destroy');

    // Profile
    Route::get('/profile', [Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile', [Admin\ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/change-password', [Admin\ProfileController::class, 'changePassword'])->name('admin.profile.password');
});
```

> Notes:
>
> * No **URL prefix** is used. Separation is enforced by **middleware** and **route names** (`admin.*`).
> * Keep admin controllers under `App\Http\Controllers\Admin\*` for clarity, even though URLs have no prefix.

---

## 6. Frontend Implementation (Plain Blade + Partials, No Components)

**Layouts**

* `resources/views/layouts/app.blade.php` — public layout
* `resources/views/layouts/admin.blade.php` — admin layout
* Use `@yield('title')`, `@yield('content')`, plus `@stack('styles')` / `@stack('scripts')`.

**Partials**

* `resources/views/partials/navbar.blade.php`
* `resources/views/partials/footer.blade.php`
* `resources/views/partials/flash.blade.php`

**Per-page JS**

* Push page-scoped scripts via `@push('scripts')` and render with `@stack('scripts')` in layouts.
* Global helpers in `public/js/app.js` (CSRF setup, small utilities).

**DataTables + AJAX**

* Initialize DataTables on admin pages, server-side JSON endpoints as defined in routes.
* AJAX forms return standard JSON and update tables/DOM without page reloads.

---

## 7. Admin Panel UX Details

* Fixed sidebar and breadcrumb content area.
* DataTables with action buttons (edit/delete/publish).
* CRUD forms via AJAX modals with FormRequest validation.
* **Spatie Permission** controls visibility — `Editor` cannot delete/publish; `Admin` can.
* Image uploads with preview (FileReader) and drag-and-drop reordering.
* Flash notifications via Blade partial + Vanilla JS (`aria-live` for accessibility).

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
| Development           | 4 weeks  | AJAX CRUD + DataTables + SEO + Analytics |
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

### ✅ Build Contract (CDN-Only, Single `web.php`, No Components, No Prefix)

* **All routes must be in `routes/web.php`**. **Do not** create additional route files.
* **Do not use URL prefixes**. Separate admin/public via middleware and route names only.
* **Do not use Blade Components** — use plain Blade layouts and `@include` partials.
* **No Node/Vite**. Use the CDN assets listed above.
* Do not include Tailwind, Alpine, Webpack, Mix, or npm scripts.
* All UI interactions must be implemented with Bootstrap 5 + jQuery AJAX + Vanilla JS.
* Load page-specific JS using `@push('scripts')` and `@stack('scripts')`.
* Follow the standard JSON response contract and server-side DataTables structure from controllers.

---
