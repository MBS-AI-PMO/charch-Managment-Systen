# Church CMS — Phase 1 Design

**Date:** 2026-05-30
**Phase:** 1 of 3 (Public Website + Admin Dashboard + Admin Auth)
**Architecture:** Pure Laravel + Custom Admin (Approach A)

---

## 1. Project Context

A Laravel-based Church Management System being built from scratch in `d:/XAMPP/htdocs/Chruch Managment System`. The full system spans 8 modules (Admin Dashboard, Member Management, Financial/Tithe Records, Prayer Requests, Events Management, Attendance Tracking, Notifications, Church Website) plus a deferred Android app idea. The work is decomposed into three phases. **This document specifies Phase 1 only.**

**Roles (target end-state, established by reference screenshots):**
- Admin login surface — *Site Admin* (full access), *Event Organizer* (sub-admin), *Prayer Organizer* (sub-admin), extensible
- Member login surface — *Church Member*

Phase 1 ships the auth framework, the *Site Admin* role, and the public website. *Event Organizer*, *Prayer Organizer*, and member features arrive in Phases 2 and 3.

---

## 2. Phase 1 Scope

### In scope

**Public website (7 pages, all dynamically editable):**
- Home, About Us, Sermons (index + detail), Events (index + detail, read-only), Ministries (index + detail), Blog/News (index + detail), Contact Us

**Admin panel (`/admin`):**
- Separate login at `/admin/login`
- Dashboard with KPIs and recent activity
- CRUD for pages, blog posts (+ categories), sermons (+ series, speakers), events, ministries, menus, media, users, roles, site settings
- Contact-form messages inbox
- Auto-save drafts, revision history on pages, audit logging on CRUD models

**Auth & permissions:**
- Two separate login flows (admin + member) on one `users` table with `is_admin` flag and two guards
- Spatie Laravel-Permission with `Site Admin` role seeded and all permissions assigned
- Permission-gated sidebar (forward-proofed for sub-admin roles)
- Member login page + logout exist as placeholders; member dashboard/portal is Phase 2

**Site-wide settings (admin-editable):**
- Branding (logo, favicon, color tokens — hydrated into CSS variables)
- Contact info (address, phone, email, service times)
- Social links (FB, IG, YT, X)
- Footer text fields
- SEO defaults + analytics script slot
- Mail SMTP test

**Media library:** Folder tree, uploads with auto-resize to ≤2400px + WebP variants, alt-text editor.

**Forms:** Contact form (saves to DB + sends email to admin), honeypot + reCAPTCHA v3, rate-limited.

### Out of scope (deferred)

- Phase 2: member registration/login portal, member profile, prayer requests, knock-for-help, community feed, member directory (admin view)
- Phase 3: online giving, attendance tracking, event registration, reminders, communication (email blasts), reports/analytics
- Multi-language, dark mode, theme switcher
- Push notifications, SMS, Android app
- Sermon series detail pages, advanced gallery, leadership/staff dedicated page

---

## 3. Stack

| Layer | Choice |
|---|---|
| Language | PHP 8.3+ |
| Framework | Laravel 12 |
| Templating | Blade |
| Database | MySQL 8 (via XAMPP) |
| Frontend CSS | Tailwind CSS v3 |
| Frontend JS | Alpine.js + Vite |
| Auth | Hand-rolled on Laravel Auth (no Breeze/Jetstream — clean custom Blade views) |
| Roles/Permissions | Spatie Laravel-Permission |
| WYSIWYG | CKEditor 5 (Classic build, customized toolbar) |
| HTML sanitization | mews/purifier (HTMLPurifier wrapper) |
| Image processing | Intervention Image |
| Mail | Laravel Mail + Mailtrap (dev) / SMTP (prod) |
| Testing | Pest + (optional) Laravel Dusk |
| Local server | XAMPP (Apache + MySQL) |

**Why not Breeze/Jetstream/Filament:** The user wants design control over both public site AND admin, with explicit emphasis on "design UI/UX first." Scaffolding frameworks would have us reskin generated code instead of designing from scratch.

---

## 4. Authentication & Authorization

### Two login surfaces, one User model

```
/login          → Member login (web guard)        [Phase 1 stub — no dashboard yet]
/admin/login    → Admin login (admin guard)
```

Single `users` table with an `is_admin` boolean. Two guards share the `User` model but apply different middleware and post-login redirects. Each guard's session is isolated — admin logout does not member-logout.

**Why one table:** A Site Admin may also be a church member (they tithe, request prayer, attend events). Two tables would force account duplication and link tables. The login *experience* is fully separate; the *identity* is shared.

### Auth scaffolding

Custom controllers and Blade views in `app/Http/Controllers/Auth/` and `resources/views/auth/`. We use Laravel's `Illuminate\Auth` services directly — no Breeze, because Breeze ships routes and views we'd immediately overwrite.

### Roles & Permissions (Spatie)

**Seeded in Phase 1:**
- Role: `Site Admin` (assigned to the bootstrap admin)
- Roles defined but unassigned: `Event Organizer`, `Prayer Organizer`

**Permissions (granular, seeded once):**
`manage-pages`, `manage-blog`, `manage-sermons`, `manage-events`, `manage-ministries`, `manage-media`, `manage-menus`, `manage-settings`, `manage-users`, `manage-roles`, `manage-messages`, `view-reports`

Site Admin gets all of them via `Gate::before` rule.

**Sidebar is permission-driven** from day one — when Phase 2 enables Event Organizer with `manage-events` only, their sidebar populates automatically.

### Route protection

- `/admin/*` → `auth:admin` + `verified` + Spatie role middleware
- `/member/*` (Phase 1 stub) → `auth:web` + `verified`
- Per-resource policies enforce field-level checks (`Gate::denies('manage-pages')` etc.)

### Security on auth

- Bcrypt cost 12
- Password rules enforced via `Password::min(12)->mixedCase()->numbers()->symbols()`
- Rate limiters: `login` 5/min/IP, `password-reset` 3/hr/IP
- Failed-attempt lockout: 5 strikes → 15 min lock (listener on `Illuminate\Auth\Events\Failed`)
- "Remember me" disabled on admin guard
- Password reset via Laravel signed URLs
- Session config: `SESSION_SECURE_COOKIE=true` (prod), `HTTP_ONLY=true`, `SAME_SITE=lax`
- `auth.session` middleware invalidates other sessions on password change
- 2FA toggle stub in user schema (`two_factor_enabled` flag) — full TOTP impl deferred

---

## 5. Database Schema

### Auth & roles

- **users** — `id, name, email (unique), email_verified_at, password, is_admin (bool, default 0), avatar_path (nullable), two_factor_enabled (bool, default 0), remember_token, created_at, updated_at`
- **password_reset_tokens**, **sessions**, **personal_access_tokens** — Laravel defaults
- **roles**, **permissions**, **model_has_roles**, **model_has_permissions**, **role_has_permissions** — Spatie defaults

### Site config & navigation

- **site_settings** — `key (PK varchar), value (longtext), type (enum: text/image/json/color/html), group (varchar — e.g. brand/contact/social/footer/seo)`
- **menus** — `id, slug (unique — "main"/"footer"), name`
- **menu_items** — `id, menu_id (FK), parent_id (FK self, nullable), label, link_type (enum: page/url/route), link_value, target (enum: _self/_blank), sort_order`

### CMS singleton pages

- **pages** — `id, slug (unique), title, hero_heading, hero_subheading, hero_image_path, body (longtext, sanitized HTML), meta_title, meta_description, meta_og_image_path, is_published (bool), published_at (nullable), updated_by (FK users), created_at, updated_at`
- **page_sections** — `id, page_id (FK), type (enum: rich-text/image-grid/cta/stats/testimonials), payload (JSON), sort_order`
- **page_revisions** — `id, page_id (FK), snapshot (longtext JSON of full page state), user_id (FK), created_at`

### Dynamic content types

- **blog_categories** — `id, slug (unique), name`
- **blog_posts** — `id, slug (unique), title, excerpt, body (longtext), featured_image_path, category_id (FK), author_id (FK users), meta_title, meta_description, is_published, published_at, created_at, updated_at`
- **sermon_series** — `id, slug (unique), name, description, cover_image_path`
- **sermon_speakers** — `id, slug (unique), name, role, bio, photo_path`
- **sermons** — `id, slug (unique), title, summary, body, series_id (FK nullable), speaker_id (FK nullable), scripture_reference, preached_on (date), audio_url, video_url, thumbnail_path, downloads_enabled (bool), is_published, created_at, updated_at`
- **events** — `id, slug (unique), title, description (longtext), location, starts_at (datetime), ends_at (datetime nullable), cover_image_path, registration_url (varchar nullable), is_published, is_featured, created_at, updated_at`
- **ministries** — `id, slug (unique), name, summary, body (longtext), leader_name, contact_email, cover_image_path, sort_order, is_published, created_at, updated_at`

### Media

- **media_folders** — `id, parent_id (FK self, nullable), name, slug, path, created_at, updated_at`
- **media** — `id, folder_id (FK nullable), disk, path (unique), filename, mime_type, size, width, height, alt_text, uploaded_by (FK users), created_at, updated_at`

### Forms

- **contact_messages** — `id, name, email, phone (nullable), subject, message (text), ip, user_agent, read_at (nullable), created_at, updated_at`

### Audit

- **activity_log** — `id, user_id (FK), subject_type, subject_id, action (created/updated/deleted/published), changes (JSON), ip, created_at` (populated via `LogsModelActivity` trait)

### Stack defaults

`failed_jobs`, `jobs`, `cache`, `cache_locks` — Laravel defaults.

### Indices

- Unique: every `slug` field
- Composite: `(is_published, published_at)` on `pages`, `blog_posts`, `sermons`, `events`, `ministries`
- `events.starts_at` index
- `contact_messages.read_at` index
- `activity_log.subject_type, subject_id` composite

---

## 6. Public Site UI/UX

### Aesthetic direction

**Warm & Community.** Welcoming, photography-led, humanist typography.

### Design tokens

| Token | Value | Use |
|---|---|---|
| `--brand-primary` | `#7A1F2B` | CTAs, links |
| `--brand-secondary` | `#C9A961` | Accents, dividers |
| `--surface` | `#FAF7F2` | Page background |
| `--surface-elevated` | `#FFFFFF` | Cards |
| `--ink` | `#2C2825` | Body text |
| `--ink-muted` | `#6B645E` | Captions, meta |
| `--border` | `#E8E2D8` | Dividers, card borders |

All tokens are stored in `site_settings` (group=`brand`). A small middleware hydrates `:root { --brand-primary: ... }` into every public response by reading the cached settings, so the admin can rebrand without a deploy.

### Typography

- Headings: **Fraunces** (Google Fonts, weights 400/600)
- Body: **Inter** (Google Fonts, weights 400/500/600)
- Scale: H1 48/56 · H2 36/44 · H3 28/36 · body 17/28 · small 14/22

### Spacing & layout

- 12-col grid, max-width 1200px, 24px gutters desktop / 16px mobile
- Section padding: 96px desktop / 56px mobile vertical
- Radius: cards 12px, buttons 8px, inputs 4px
- Subtle warm shadow on cards

### Blade component library

Located in `resources/views/components/site/`:
- `header` — sticky logo + nav + mobile hamburger + "Plan your visit" CTA
- `footer` — 4-column (logo+blurb / quick links / service times+address / socials+newsletter stub)
- `hero` — full-bleed image + heading + sub + 2 CTAs
- `section-heading` — eyebrow + heading + lede
- `card` — used for sermon / event / blog / ministry cards
- `cta-band` — full-width gold band with H2 + button
- `breadcrumb`

### Page layouts (summary)

**Home** — Hero · service times strip · welcome 2-col · upcoming events (3 cards) · latest sermon (large + 2 small) · ministries grid (4–6) · latest blog (3 cards) · CTA band · footer

**About Us** — Hero · Our Story (WYSIWYG + side images) · Beliefs (3-col icon grid) · Leadership (photo grid + modal bios) · CTA band

**Sermons (index)** — Hero · filter (series/speaker/search) · sermon grid (12/page) · sidebar with latest series
**Sermons (detail)** — Title+meta · embedded audio/video · scripture ref · summary · "more from this series" carousel

**Events (index)** — Hero · tabs (Upcoming / Past) · cards with image, date badge, title, location
**Events (detail)** — Hero with date badge · description · map · `.ics` calendar download · external Register CTA

**Ministries (index)** — Hero · ministry tile grid
**Ministries (detail)** — Hero · body · leader card · contact CTA

**Blog (index)** — Hero · category chips · featured post (large) · grid · pagination
**Blog (detail)** — Title+meta · featured image · body · author block · related posts (3)

**Contact** — Hero · 2-col (form left / info card right) · embedded map

### Responsive

Mobile-first. Breakpoints sm 640 · md 768 · lg 1024 · xl 1280. Nav becomes off-canvas drawer below `md`. Hero text scales. Grids 1 → 2 → 3-4 col.

### UI-first deliverable

A `/preview` route group renders every public page with static seed data so the user can sign off on look & feel before any controller is wired to the DB. Once approved, controllers replace the static seed with real data.

---

## 7. Admin Panel UI/UX

### Design language

Functional and calm — *not* the warm photographic feel of the public site. Inter throughout. Same brand palette but cooler background and a deep-ink sidebar.

### Tokens (admin)

| Token | Value |
|---|---|
| Background | `#F7F5F1` |
| Sidebar bg | `#1F1A18` |
| Sidebar text | `#FAF7F2` |
| Active nav | `--brand-primary` left border + 8% tint |
| Card border | `#E8E2D8` |

### Layout

Top bar (logo · breadcrumb · user menu) + collapsible left sidebar + main content. Sidebar collapses to icons under `lg`; off-canvas drawer on mobile.

### Screens

**Login (`/admin/login`)** — split layout: brand panel (church name + verse) | clean form

**Dashboard (`/admin`)** — 4 KPI cards (Pages / Posts / Upcoming events / Unread messages) · recent activity feed (last 10 audit-log entries) · quick actions

**Pages** — Index (table). Edit (tabbed: Content / SEO / Settings). Sections builder (sortable, "Add section" dropdown). Auto-save every 30s. Save Draft vs Publish. Revision history drawer.

**Blog** — Index w/ filters. Categories sub-page (inline CRUD). Edit: title, slug, category, excerpt, CKEditor body, featured image (media-library modal), SEO tab, publish toggle, scheduled `published_at`.

**Sermons** — Series + Speakers sub-pages (CRUD). Sermons CRUD: title, series, speaker, scripture, date, audio URL, video URL (auto-parse YouTube/Vimeo), thumbnail, downloads_enabled.

**Events** — CRUD with date/time pickers (Flatpickr), location, cover, registration URL, "Featured on home" toggle.

**Ministries** — CRUD with leader, contact, WYSIWYG, drag-sortable list.

**Media Library** — Folder tree left, thumbnail grid main. Drag-drop upload with progress. Auto-resize >2400px + WebP variants. Alt-text editor. Modal-capable (CKEditor opens it).

**Menus** — Pick menu (Main/Footer) → drag-sortable nested list (1 level deep). "Add item" picks page / custom URL / named route.

**Settings** (tabs) — Branding · Contact · Social · Footer · SEO · Mail (with SMTP test)

**Messages** — Contact inbox: list + detail, mark read/unread, "Reply via email" mailto link

**Users & Roles** — Users CRUD (name, email, role multi-select, activate/deactivate, send-password-reset action). Roles CRUD with grouped permission checkboxes.

### CKEditor 5 build

**Toolbar:** heading (H2-H4), bold, italic, underline, link, bulleted list, numbered list, blockquote, font family (curated 5 choices: Inter, Fraunces, Lora, Georgia, system-ui), font size, font color, background color, alignment, insert image (media library modal), insert table, insert YouTube, source view.

**Sanitization:** Server-side HTMLPurifier with allowlist of tags/attributes runs on every save — never trust client sanitization.

### Admin UI-first deliverable

A `/admin/preview` route group renders the layout + each key screen with seed data for sign-off before wiring to controllers.

---

## 8. Security Plan

### Input & output
- All requests through `FormRequest` classes — no inline validation
- HTMLPurifier sanitizes CKEditor output on save with explicit allowlist
- Blade `{{ }}` default; `{!! !!}` only for purified HTML fields
- Filename sanitization on uploads (slug + random prefix, no traversal)
- MIME-type + extension allowlist for uploads; image dimensions verified via Intervention
- Max upload size 10MB (PHP, web server, Laravel all aligned)

### Auth & sessions
- bcrypt cost 12
- Password rule: min 12 chars, mixed case, numbers, symbols
- Rate limiters: `login` 5/min/IP, `password-reset` 3/hr/IP, `contact-form` 5/hr/IP
- Sessions: secure cookie (prod), http-only, same-site=lax
- `auth.session` middleware invalidates other sessions on password change
- CSRF on all state-changing routes
- Signed URLs for password reset and email verification

### Authorization
- Spatie role middleware on every admin route group
- Policy classes for every model (forward-proofs sub-admin tiers)
- `Gate::before` grants Site Admin everything

### HTTP hardening
- Security headers middleware: `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy` minimal, `Content-Security-Policy` (script-src self + CKEditor CDN + analytics domain)
- HSTS in production
- `URL::forceScheme('https')` in production

### Misc
- `.env` never committed; `.env.example` documents every var with safe defaults
- Telescope/Debugbar disabled in production via env check
- Honeypot + reCAPTCHA v3 on contact form
- Audit log via `LogsModelActivity` trait (created/updated/deleted/published + who + IP)

---

## 9. Routes Structure

```
routes/
├── web.php       # mounts public + admin + member + auth route files
├── admin.php     # /admin/* — auth:admin + Spatie role middleware
├── member.php    # /member/* — Phase 2 stub (login/logout only in Phase 1)
└── auth.php      # custom login/forgot/reset routes for both guards
```

---

## 10. Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Site/         # public-facing controllers
│   │   ├── Admin/        # one controller per admin resource
│   │   └── Auth/         # AdminLoginController, MemberLoginController, etc.
│   ├── Requests/         # FormRequest per action
│   ├── Middleware/       # SecurityHeaders, EnsureAdmin, HydrateBrandTokens
│   └── ViewComposers/    # NavComposer, SettingsComposer
├── Models/
├── Policies/
├── Services/
│   ├── PagePublisher.php
│   ├── MediaUploader.php
│   ├── SettingsRepository.php   # cached site-settings access
│   └── HtmlPurifierService.php
├── Support/
│   ├── Sluggable.php             # trait
│   └── LogsModelActivity.php     # trait
└── Providers/

database/
├── migrations/
├── seeders/
│   ├── RolePermissionSeeder.php
│   ├── SiteAdminSeeder.php       # bootstrap admin from .env
│   ├── SiteSettingsSeeder.php
│   └── DemoContentSeeder.php     # local-only realistic content
└── factories/

resources/
├── views/
│   ├── components/site/    # public Blade components
│   ├── components/admin/   # admin Blade components
│   ├── site/               # public page views
│   ├── admin/              # admin page views
│   ├── auth/               # login/forgot views (admin + member)
│   ├── emails/             # contact-form notification, password reset
│   └── layouts/            # site.blade.php, admin.blade.php
├── css/
└── js/

tests/
├── Feature/
│   ├── Site/
│   ├── Admin/
│   └── Auth/
└── Unit/

docs/
└── superpowers/specs/
    └── 2026-05-30-church-cms-phase-1-design.md   # this file
```

---

## 11. Testing Approach (Pest)

**Auth feature tests**
- Admin login success/failure
- Rate limit triggers at 6th login attempt
- Member cannot access `/admin/*`
- Admin without `verified` is redirected
- Password reset signed URL works and expires
- Logging out admin does not log out the same user from member surface (sessions isolated)

**CMS feature tests** (per resource: pages, blog, sermons, events, ministries)
- Create / update / publish / unpublish flows
- Slug auto-generation and uniqueness
- Revision created on page update
- Policy denies user without permission

**Public site tests**
- Each page returns 200
- Unpublished/draft items return 404 publicly
- Contact form: success, validation errors, honeypot triggered, rate-limited

**Security tests**
- CSRF rejects missing token
- XSS payload in CKEditor body is purified on save and rendered as escaped output
- File upload rejects `.php`, `.phtml`, SVG with embedded script
- Security headers present on every response
- HTTPS enforced when `APP_ENV=production`

**Browser smoke (Pest + Dusk, optional)**
- Admin completes "create blog post" happy path end-to-end

**Coverage targets:** ~70% line coverage on app code; 100% on auth, policies, security middleware.

---

## 12. Build Sequence (UI-first → dynamic)

Per the user's "design first, then convert to dynamic" requirement:

1. **Bootstrap:** Laravel install, Tailwind + Vite + Google Fonts, base layouts, auth scaffolding (Blade views only, no controllers wired)
2. **UI/UX deliverable — public site:** Build component library + every public page under `/preview/*` using static seed data. **Stop and get user sign-off here.**
3. **UI/UX deliverable — admin panel:** Build admin layout + each key screen under `/admin/preview/*` using static seed data. **Stop and get user sign-off here.**
4. **Database & seeders:** Migrations, Spatie roles/permissions seeder, Site Admin seeder (from `.env`), demo content seeder (local-only)
5. **Backend wiring:** Replace static seed in public pages with real controller + model queries; replace admin preview screens with real CRUD controllers + FormRequests + policies
6. **Hardening:** Security headers middleware, rate limiters, audit-log trait wired in, HTML purification on save
7. **Tests:** Auth, CMS CRUD, public site, security
8. **Polish:** Performance pass (settings cached, image lazy-loading, route caching for prod), final review, deployment notes

---

## 13. Acceptance Criteria for Phase 1

- All 7 public pages render with admin-edited content
- Admin can log in at `/admin/login`, edit any page via WYSIWYG, manage blog/sermons/events/ministries/menus/settings/media/users/roles
- Admin can rebrand the site (logo, colors) without code changes
- Member login page exists at `/login` (post-login redirect goes to a "Coming soon" stub)
- All security plan items implemented and verified by tests
- Site fully responsive across mobile/tablet/desktop
- Contact form: saves to DB, emails admin, rate-limited, honeypot active
- Tests green; line coverage ≥ 70%
- `README.md` documents local setup (XAMPP, `.env`, seed admin, `npm run dev`)

---

## 14. Open Questions Deferred to Implementation Plan

- Exact CKEditor 5 build URL / self-host strategy (CDN with SRI vs `npm` build)
- Whether to add Laravel Telescope locally for dev (recommend yes — env-gated)
- Default `Site Admin` email/password handling (seed from `.env`, force change on first login? Recommended yes.)
- Whether to ship a `make:phase1-stub` Artisan command for quick local resets
- Exact reCAPTCHA v3 vs hCaptcha choice (recommend reCAPTCHA v3 for invisible UX)

These do not block design approval and will be resolved in the writing-plans step.
