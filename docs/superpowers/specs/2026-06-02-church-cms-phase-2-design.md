# Church CMS — Phase 2 Design

**Date:** 2026-06-02
**Phase:** 2 of 3 (Member Portal + Sub-admin Role Wiring + Donation Page)
**Builds on:** [Phase 1 design](2026-05-30-church-cms-phase-1-design.md)

---

## 1. Project Context

Phase 1 shipped: public website, admin panel, two auth guards (`admin`/`web`), Spatie roles & permissions framework with `Site Admin` seeded, media library + CKEditor, contact form, security hardening, 74 Pest tests.

The `web` guard's member-side is currently a stub — login page exists at `/login`, post-login lands on a "Coming soon" view. Phase 2 turns that into a real member portal and wires up the **Event Organizer** and **Prayer Organizer** sub-admin roles that have existed in the database since Phase 1 but have no assigned UI.

---

## 2. Phase 2 Scope

### In scope

**Member auth & onboarding**
- Open self-signup at `/register` (email + password + name + honeypot)
- Email verification required before accessing any member feature (`MustVerifyEmail` + `verified` middleware)
- Resend verification email endpoint
- Password reset (already exists from Phase 1) reused

**Member portal at `/member/*` (`auth:web` + `verified`):**
- Dashboard — welcome, latest feed posts (3), own prayer requests with statuses, upcoming events teaser, quick-action buttons
- Profile — edit name, email (re-verifies if changed), avatar (reuses media library), phone, short bio; separate password change card
- Prayer Requests — own list + public community board; submit (with public/anonymous toggles); edit/delete own; toggle "I'm praying" on others' public requests
- Knock for Help (pastoral care alerts) — submit category (illness/grief/financial/food/other) + message + share-with-team toggle; view own list with status
- Community Feed — read-only timeline of admin/sub-admin posts; react with one of ♥/🙏/👏 (one reaction per post per member, replaceable)
- Giving — `/member/giving` redirects to public `/donate`

**Public donation page**
- New singleton page `slug=donate` editable via existing Pages CRUD
- Public route `/donate` (no auth required)
- Donate buttons added to public site header + member portal top nav + public footer

**Admin features for the new modules (Site Admin + Prayer Organizer)**
- Admin → Prayer Requests: list with status/pray-count filters, detail view, assign, status change, private notes, delete
- Admin → Knock for Help: list with status/category filters, detail view, status change, private response notes, audit-log of changes
- Admin → Community Feed: full CRUD; "Send to members" optional broadcast email on create

**Sub-admin role wiring**
- Prayer Organizer sees: Dashboard + Prayer Requests + Knock for Help + Community Feed
- Event Organizer sees: Dashboard + Events + Ministries (Phase 1 features) + Attendance + Reminders (both Phase 3 stubs)
- Sidebar is permission-driven (Phase 1 architecture) — adding the 4 new permissions is sufficient to populate sidebars automatically

**Admin Member Directory**
- Existing `Admin → Users` page from Phase 1 extends to include filters by role, last-login range, verification status, and a "Send invite" action that uses Laravel's password-reset broker to issue a setup link

### Out of scope (deferred to Phase 3 or dropped)

- Attendance tracking
- Event registration / RSVPs
- Reminders engine (scheduled mailers)
- Reports / analytics
- Stripe payment integration — **dropped from the project entirely** by user decision 2026-06-02; donation is now a static CMS page with bank details
- Communication module (admin → all members email blasts) — partial scope present via "Send to members" feed broadcast; full module is Phase 3
- Member-authored feed posts (only admins post in Phase 2)
- Feed comments
- In-app notification center
- Android app — deferred indefinitely

---

## 3. Database Schema

### New tables

**`prayer_requests`**
```
id (bigint pk)
user_id (FK users, nullable for anonymous — see note)
name (string 120)              snapshot of user name or "Anonymous" at submission time
title (string 200)
body (longtext)                 plain text on member submission (rendered with nl2br + {{ }} escape);
                                if admin edits via CKEditor, HTMLPurifier `cms` profile sanitizes
is_public (bool, default false) false = only Prayer Organizer + Site Admin see it
is_anonymous (bool, default false)
status (enum: pending|praying|answered|closed, default pending)
pray_count (int, default 0)     denormalized counter for community board sort
assigned_to (FK users, nullable)
admin_notes (longtext, nullable)  admin-only
timestamps
deleted_at (soft delete)
INDEX (status, created_at)
INDEX (user_id)
```
Note on anonymity: `user_id` is *always* stored for audit/moderation. `is_anonymous=true` renders "Anonymous" everywhere user-facing; only Site Admin sees the real `user_id` in admin detail view.

**`prayer_request_prays`** — "I'm praying for this" toggle
```
id, prayer_request_id (FK), user_id (FK), created_at
UNIQUE (prayer_request_id, user_id)
```
Counter trigger: model observer increments/decrements `prayer_requests.pray_count` on insert/delete.

**`care_requests`** (Knock for Help)
```
id (bigint pk)
user_id (FK users)              required — care is never anonymous
category (enum: illness|grief|financial|food|other)
message (text)
share_with_team (bool, default false)  if true, all Prayer Organizers see; else only assigned + Site Admin
status (enum: open|responding|closed, default open)
responder_id (FK users, nullable)
response_notes (longtext, nullable)  admin-only, multi-paragraph
closed_at (timestamp, nullable)
timestamps
INDEX (status, created_at)
INDEX (user_id)
```

**`feed_posts`**
```
id (bigint pk)
author_id (FK users)            must hold manage-community-feed permission at submission
title (string 200, nullable)
body (longtext)                 HTMLPurifier-sanitized
image_path (string, nullable)
pinned (bool, default false)
published_at (timestamp, default now)  enables future scheduling
timestamps
INDEX (pinned desc, published_at desc)
```

**`feed_reactions`**
```
id (bigint pk)
feed_post_id (FK)
user_id (FK)
kind (enum: heart|pray|amen)
created_at
UNIQUE (feed_post_id, user_id)  one reaction per user per post; change = replace
```

### Existing-table extensions

No schema changes to `users` (Phase 1 already added `is_admin`, `email_verified_at`, `last_login_at`, `password_change_required`, `two_factor_enabled`, `avatar_path`).

Optional new columns on `users`:
- `phone` (string 32, nullable) — added to Profile form
- `bio` (text, nullable) — short member bio shown on prayer requests/feed reactions

These columns added via one small migration.

### Seeders

**`SiteSettingsSeeder` update** (idempotent additions):
- `member.registration_enabled` (bool, default true)
- `member.welcome_message` (text, default "Welcome to the Grace Community family.")
- `member.broadcast_on_feed_default` (bool, default false) — checked-state of admin-feed "Send to members" toggle
- `donate.button_label` (text, default "Give")
- `donate.show_in_member_nav` (bool, default true)

**`DemoContentSeeder` update**:
- New `pages` row with slug `donate`:
  - title: "Give"
  - hero_heading: "Support our mission"
  - hero_subheading: "Your generosity keeps the lights on, the doors open, and the ministry going."
  - body: example markdown with placeholder bank details (Account: XXXX, Sort code: XX-XX-XX, Reference: "Your name", plus a "Visit in person" note)
  - is_published: true, published_at: now()

**`RolePermissionSeeder` update**:
Add 4 new permissions on `admin` guard:
- `manage-prayer-requests`
- `manage-knock-help`
- `manage-community-feed`
- `manage-members` (renames existing `manage-users` is NOT necessary — `manage-users` remains for user CRUD; `manage-members` is a separate concept covering the directory view & invite flow)

Permission assignments:
- **Site Admin** receives all 4 new permissions in addition to Phase 1's 12 (total 16).
- **Prayer Organizer** receives exactly: `manage-prayer-requests`, `manage-knock-help`, `manage-community-feed`.
- **Event Organizer** keeps Phase 1: `manage-events`, `manage-ministries`. No new perms in Phase 2.

`Gate::before` rule from Phase 1 (`Site Admin → always allowed`) remains unchanged — no plumbing additions.

---

## 4. Member Portal UI

### Layout (`resources/views/layouts/member.blade.php`)

Third layout alongside Phase 1's `site.blade.php` (public) and `admin.blade.php` (admin). Same Warm & Community palette as public site, but:

- **Fixed top nav** with logo + horizontal menu (Dashboard / Prayer / Knock / Feed / Give) + user avatar dropdown (My Profile / Settings / Sign out)
- **No sidebar** — top nav only, keeps the focus on content for everyday member use
- Mobile: nav collapses to off-canvas drawer (Alpine, matches site header pattern)
- Background: `surface` color (warm ivory); content blocks use `surface-elevated` cards
- Typography: Fraunces for h1/h2 (welcoming), Inter body

### Pages

**Dashboard (`/member`)** — Welcome banner with member name + service times pulled from `site_settings` + the seeded `member.welcome_message`. Three card grid:
1. Latest feed (3 most recent feed posts with reaction counts)
2. Your prayer requests (5 most recent with status pills)
3. Upcoming events teaser (3 next events, pulled from Phase 1 `events` table)

Quick-action button row: "Submit a prayer request" / "Knock for help" / "Update profile" / "Give".

**Profile (`/member/profile`)** — Two stacked cards in a max-w-2xl container:
1. *Personal info* card: name, email (if changed, requires re-verification; sets `email_verified_at = null` + resends), phone (optional), bio (textarea, 280 chars), avatar (media-library modal picker reused from Phase 1).
2. *Change password* card: current_password, new password (Password::defaults from Phase 1 still applies), confirmation.

Two distinct forms posting to two distinct routes.

**Prayer Requests (`/member/prayer-requests`)** — Alpine tabs:
- *My requests* tab: table of user's own (title, status pill, pray_count, created, edit/delete buttons).
- *Community board* tab: card grid of public requests from other users (anonymous-aware), each card has body excerpt + status pill + a single "🙏 I'm praying (N)" toggle button.

"+ New request" button opens an inline form (Alpine collapse) on the same page with:
- Title (required, max 200)
- Body (required, plain textarea — *not* CKEditor; intentional, prayer requests should be simple)
- "Make this public so others can pray with me" toggle (default off)
- "Submit anonymously" toggle (default off; only shown when public toggle is on)
- Submit button

Detail view (`/member/prayer-requests/{p}`) — full body, status pill, prayer count, "I'm praying" button (for others' requests). Owner sees Edit/Delete buttons. Members can't view another member's private request — 404 to anyone but the owner + Prayer Organizer + Site Admin.

**Knock for Help (`/member/care`)** — Member's own list (no community view — these are private to the member + pastor team).

"+ New request" button — modal-style page (`/member/care/create`) with calm copy: "Tell us what's going on — a pastor will reach out within 24 hours." Form fields:
- Category radios (illness / grief / financial / food / other)
- Message textarea (required, max 5000)
- "Share with the prayer team" toggle (off by default — only shares with the assigned pastor unless toggled)
- Submit

After submit: confirmation page "Thank you. A pastor will be in touch soon." + an email fires to Prayer Organizer + Site Admin immediately.

**Community Feed (`/member/feed`)** — Vertical timeline, infinite-scroll or paginated (paginated is simpler for Phase 2 — 10 per page). Each post:
- Author avatar + name
- Relative time (`diffForHumans`)
- Title (h3, optional)
- Body (purified HTML rendered with `prose` Tailwind plugin)
- Optional image (max-h-96, lazy-loaded)
- Reaction bar — three buttons with counts: ♥ Heart, 🙏 Pray, 👏 Amen (rate-limited by `feed-react` limiter — also covers the prayer-request "I'm praying" toggle since both are low-cost taps)
- Clicking a reaction toggles it (if none active → set; if other active → replace; if same active → remove)

Pinned posts pinned to top with a small "Pinned" badge.

**Giving (`/member/giving`)** — A controller method that does `return redirect()->route('site.donate');`. No view.

### Public Donate page (`/donate`)

Just another singleton page in the existing `pages` table — slug=`donate`. Public, no auth. Rendered by Phase 1's `PageController` (extend to handle this slug or add a small dedicated method). View reuses existing `site` Blade components (hero, prose body). Admin edits via existing `Admin → Pages → Donate` (no new admin code).

The public site header gets a "Give" CTA button next to "Plan your visit" (or replaces it depending on existing nav layout). The site footer gets a quick-link "Give" → `/donate`. Member portal top nav has "Give" → `/donate`.

---

## 5. Admin UI Additions

All under existing admin layout/sidebar/topbar from Phase 1. Sidebar items are permission-gated; adding the new permissions makes them appear automatically for permissioned users.

**Admin → Prayer Requests (`/admin/prayer-requests`)**
- Index: paginated table — title, requester (or "Anonymous"), status pill, pray_count, created. Filters: status select + search.
- Detail (`/admin/prayer-requests/{p}`): full body + member info (Site Admin only — Prayer Organizer sees "Anonymous" if `is_anonymous=true`) + status dropdown (changing it logs to `activity_log`) + "Assign to me" button + admin_notes textarea (CKEditor) + Delete button.

**Admin → Knock for Help (`/admin/care`)**
- Index: paginated table — member name, category badge, status pill, created. Filters: status + category + search.
- Detail (`/admin/care/{c}`): member contact card (name + email + phone) + full message + status dropdown (open/responding/closed) + response_notes textarea (CKEditor) + "Mark Closed" button (sets closed_at). Status changes log to `activity_log` with the user_id of the responder. The responder_id is set on first transition out of "open".

**Admin → Community Feed (`/admin/feed`)**
- Index: paginated table of posts (pinned ones grouped top) — title, author, published_at, reaction counts, edit/delete.
- Create/Edit form: title (optional), body (CKEditor), image (media library picker), pinned toggle, published_at datetime picker (defaults to now), "Send to members" checkbox. When checked on create, a `FeedPostBroadcastJob` is dispatched to the database queue — it batch-emails all `email_verified_at IS NOT NULL` users with the post excerpt and a link to `/member/feed`. Queue avoids slow admin response when membership grows.

**Admin → Users (extends Phase 1) → repositioned as "Member Directory"**
- Rename sidebar entry from "Users" to "Members" (since "Site Admin / Users" is a confusing label for site admins managing church members)
- Add filters: role select, last_login_at range, email_verified_at (verified/unverified)
- Add "Send invite" action: opens a modal with email field; submitting calls `Password::broker('users')->sendResetLink([...])` with a custom Mailable that says "Set your password and join Grace Community" instead of the standard "Reset password" copy. This effectively becomes the invite flow without a separate `invitations` table.

### Sub-admin sidebar wiring

The `<x-admin.sidebar />` component from Phase 1 already checks `request()->user()->can($permission)` per menu item. Update its `$links` array to include 3 new items, each gated on its permission:

```php
['route' => 'admin.prayer-requests.index', 'permission' => 'manage-prayer-requests', ...],
['route' => 'admin.care.index',            'permission' => 'manage-knock-help',     ...],
['route' => 'admin.feed.index',            'permission' => 'manage-community-feed', ...],
```

Plus two Phase 3 stub items, both `disabled` styled and pointing to a "Coming in Phase 3" route — only shown to Event Organizer:
```php
['route' => 'admin.attendance.stub', 'permission' => 'manage-events',  'badge' => 'Phase 3', ...],
['route' => 'admin.reminders.stub',  'permission' => 'manage-events',  'badge' => 'Phase 3', ...],
```

The "active" matcher used in the sidebar already supports these.

---

## 6. Routes Structure

```
routes/
├── web.php                  # public site routes (Phase 1) + new /donate route
├── admin.php                # /admin/* (Phase 1) + new resource routes for prayer-requests/care/feed
├── member.php               # /member/* — Phase 1 stub, now becomes full set
├── auth.php                 # /admin/login etc (Phase 1) + new /register and /email/verify flows
└── preview.php              # local-env-only design previews — extend to include /preview/member/*
```

New routes (full list at the route level — see Section 3 in earlier doc for member route names):

```php
// In routes/member.php
Route::middleware('guest:web')->group(function () {
    Route::get('/register',  [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])
        ->middleware('throttle:member-register');
});

Route::middleware('auth:web')->group(function () {
    Route::get('/email/verify',                  [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}',      [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth:web', 'verified'])->prefix('member')->name('member.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile',         [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',         [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',[ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::resource('prayer-requests', PrayerRequestController::class)
        ->only(['index','create','store','show','destroy'])
        ->parameters(['prayer-requests' => 'prayer'])
        ->names('prayer');
    Route::post('/prayer-requests/{prayer}/pray', [PrayerRequestController::class, 'togglePray'])
        ->name('prayer.pray')
        ->middleware('throttle:feed-react');

    Route::resource('care', CareRequestController::class)
        ->only(['index','create','store','show'])
        ->middleware(['throttle:care-submit:create']);

    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::post('/feed/{post}/react', [FeedController::class, 'react'])
        ->name('feed.react')
        ->middleware('throttle:feed-react');

    Route::get('/giving', fn() => redirect()->route('site.donate'))->name('giving');
});

// In routes/web.php
Route::get('/donate', [PageController::class, 'donate'])->name('site.donate');

// In routes/admin.php
Route::middleware(['auth:admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('prayer-requests', Admin\PrayerRequestController::class)
        ->only(['index','show','update','destroy'])
        ->middleware('permission:manage-prayer-requests');

    Route::resource('care', Admin\CareController::class)
        ->only(['index','show','update'])
        ->middleware('permission:manage-knock-help');

    Route::resource('feed', Admin\FeedController::class)
        ->middleware('permission:manage-community-feed');

    // Stub routes for Phase 3 features (Event Organizer sidebar links)
    Route::view('/attendance', 'admin.stubs.phase3', ['module' => 'Attendance'])->name('attendance.stub');
    Route::view('/reminders',  'admin.stubs.phase3', ['module' => 'Reminders'])->name('reminders.stub');
});
```

---

## 7. Directory Structure (additions on top of Phase 1)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── RegisterController.php              (new)
│   │   │   └── EmailVerificationController.php     (new)
│   │   ├── Member/                                  (new dir)
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── PrayerRequestController.php
│   │   │   ├── CareRequestController.php
│   │   │   └── FeedController.php
│   │   ├── Admin/
│   │   │   ├── PrayerRequestController.php         (new)
│   │   │   ├── CareController.php                  (new)
│   │   │   └── FeedController.php                  (new)
│   │   └── Site/
│   │       └── PageController.php                  (extended with donate())
│   ├── Requests/
│   │   ├── Auth/RegisterRequest.php                (new)
│   │   ├── Member/{ProfileRequest, ProfilePasswordRequest, PrayerRequestRequest, CareRequestRequest}.php
│   │   └── Admin/{PrayerRequestUpdateRequest, CareUpdateRequest, FeedPostRequest}.php
├── Models/
│   ├── PrayerRequest.php                            (new, uses Sluggable+LogsModelActivity)
│   ├── PrayerRequestPray.php                        (new)
│   ├── CareRequest.php                              (new)
│   ├── FeedPost.php                                 (new)
│   ├── FeedReaction.php                             (new)
│   └── User.php                                     (modified — adds relationships)
├── Policies/
│   ├── PrayerRequestPolicy.php                      (new)
│   ├── CareRequestPolicy.php                        (new)
│   └── FeedPostPolicy.php                           (new)
├── Observers/
│   └── PrayerRequestPrayObserver.php                (new — maintains pray_count denormalized)
├── Mail/
│   ├── CareRequestSubmitted.php                     (new — to Prayer Organizer + Site Admin)
│   ├── FeedPostBroadcast.php                        (new — to all verified members)
│   └── MemberInvite.php                             (new — for admin "Send invite")
└── Providers/
    └── AppServiceProvider.php                       (modified — registers observer + new policies)

resources/views/
├── layouts/
│   └── member.blade.php                             (new)
├── components/
│   └── member/                                       (new dir)
│       ├── header.blade.php
│       ├── footer.blade.php
│       └── reaction-button.blade.php
├── member/                                          (now full, was just coming-soon stub)
│   ├── dashboard.blade.php
│   ├── profile/edit.blade.php
│   ├── prayer-requests/{index,create,show}.blade.php
│   ├── care/{index,create,show,thanks}.blade.php
│   └── feed/index.blade.php
├── auth/
│   └── member/
│       ├── register.blade.php                        (new)
│       └── verify.blade.php                          (new)
├── admin/
│   ├── prayer-requests/{index,show}.blade.php       (new)
│   ├── care/{index,show}.blade.php                  (new)
│   ├── feed/{index,create,edit}.blade.php           (new)
│   └── stubs/phase3.blade.php                       (new — generic "Coming in Phase 3")
├── emails/
│   ├── care-submitted.blade.php                     (new)
│   ├── feed-broadcast.blade.php                     (new)
│   └── member-invite.blade.php                      (new)
└── preview/member/*                                  (new previews mirroring real member views)

database/
├── migrations/
│   ├── 2026_06_03_000001_create_prayer_requests_tables.php
│   ├── 2026_06_03_000002_create_care_requests_table.php
│   ├── 2026_06_03_000003_create_feed_tables.php
│   └── 2026_06_03_000004_add_phone_bio_to_users.php
├── seeders/
│   ├── RolePermissionSeeder.php                     (modified)
│   ├── SiteSettingsSeeder.php                       (modified — adds member.* and donate.*)
│   └── DemoContentSeeder.php                        (modified — adds donate page + sample feed/prayer)
└── factories/
    ├── PrayerRequestFactory.php                     (new)
    ├── CareRequestFactory.php                       (new)
    └── FeedPostFactory.php                          (new)

tests/Feature/
├── Auth/MemberRegisterTest.php
├── Member/{ProfileTest, PrayerRequestTest, CareRequestTest, CommunityFeedTest}.php
├── Admin/{PrayerAdminTest, CareAdminTest, FeedAdminTest, SubAdminSidebarTest}.php
└── Site/DonateTest.php
```

---

## 8. Security Plan (additions on top of Phase 1)

**Authentication & verification**
- New members redirected to `/email/verify` after registration; cannot access any member route until verified
- `signed` middleware on verification link route (prevents tampering)
- 6/minute rate limit on verification-resend
- Honeypot field on `/register` form (same hidden `website` field pattern as Phase 1 contact form)
- New rate limiter `member-register`: 3/hour/IP (slow self-signup spam)

**Authorization**
- `PrayerRequestPolicy`:
  - `view(User $u, PrayerRequest $r)` — return `$r->is_public || $r->user_id === $u->id || $u->can('manage-prayer-requests')`
  - `update/delete(User $u, PrayerRequest $r)` — return `$r->user_id === $u->id || $u->can('manage-prayer-requests')`
- `CareRequestPolicy`:
  - `view(User $u, CareRequest $r)` — return `$r->user_id === $u->id || $u->can('manage-knock-help')`
  - Update/delete restricted to `manage-knock-help` holders only (members never edit their care requests after submission — they email or call instead)
- `FeedPostPolicy`:
  - `view` — any verified user
  - `create/update/delete` — `manage-community-feed` permission required
- `Gate::before` for Site Admin from Phase 1 continues to bypass all policy checks

**Input & output**
- HTMLPurifier `cms` profile (Phase 1) reused on `prayer_requests.body` (admin-edit only — initial member submission is plain text and gets `nl2br()` at render time), `care_requests.response_notes`, and `feed_posts.body`
- `prayer_requests.body` and `care_requests.message` are plain text on insert (members submit plain textarea, not CKEditor) — escaped with `{{ }}` on render
- All form requests use FormRequest classes with explicit `rules()` and `authorize()` methods
- File uploads (feed post images, profile avatars) go through the existing `MediaUploader` service — MIME allowlist, extension allowlist, 10MB cap, auto-resize to 2400px

**Rate limiters added to `RateLimitServiceProvider`**
```php
RateLimiter::for('member-register', fn (Request $r) => Limit::perHour(3)->by($r->ip()));
RateLimiter::for('prayer-submit',   fn (Request $r) => Limit::perHour(10)->by($r->user()?->id ?: $r->ip()));
RateLimiter::for('care-submit',     fn (Request $r) => Limit::perHour(3)->by($r->user()?->id ?: $r->ip()));
RateLimiter::for('feed-react',      fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
```

**Anonymity protection**
- `is_anonymous=true` on a prayer request:
  - Public board: name renders as "Anonymous", avatar replaced with neutral icon
  - Admin index for Prayer Organizer: also renders "Anonymous" (Prayer Organizer can't unmask)
  - Admin detail for Site Admin only: full name + user_id visible (audit trail)
- Care requests are never anonymous — pastors need member identity to follow up

**Email security**
- `MemberInvite` mailer uses Laravel signed URLs (1-week expiry)
- Verification links use Laravel's default signed-URL implementation (1-hour expiry per Laravel default)

**CSP & headers**: unchanged from Phase 1 — no new external script sources required.

---

## 9. Testing Plan (Pest)

Target: ~45 new tests, all green. Phase 1 + Phase 2 total ≈ 120 tests.

**Auth (`tests/Feature/Auth/`)**
- `MemberRegisterTest` — successful signup, validation errors, honeypot drop, rate limit at 4th attempt, email verification email queued, verified user can access /member, unverified user redirected
- `EmailVerificationTest` (new) — signed link works, tampered link 403, expired link 403, resend works under rate limit

**Member (`tests/Feature/Member/`)**
- `ProfileTest` — edit name, edit email triggers re-verification (email_verified_at cleared, new link sent), avatar upload, password change with Password::defaults validation
- `PrayerRequestTest` — create (public/private/anonymous), edit/delete own only, can't view others' private requests, "I'm praying" toggle (creates/deletes prayer_request_prays row, updates pray_count), one-pray-per-user constraint, soft delete preserves audit
- `CareRequestTest` — create, can't view another member's, email sent to Prayer Organizer (`Mail::fake()`), rate limit at 4th submission in an hour, fields validated
- `CommunityFeedTest` — read paginated, react with each kind, single-reaction-per-user (replace on second react), remove reaction by re-clicking same kind, pinned posts ordered first, non-permissioned members can't access admin create endpoint

**Admin (`tests/Feature/Admin/`)**
- `PrayerAdminTest` — Site Admin sees all incl. private; Prayer Organizer sees all but anonymous masked; Event Organizer 403; status change creates activity_log
- `CareAdminTest` — Site Admin + Prayer Organizer access; other roles 403; status transitions log; closing sets closed_at
- `FeedAdminTest` — create with image upload, "Send to members" sends emails to all verified members (assert mail count), edit/delete, pinned toggle, scheduled published_at (future-dated post hidden from member feed until time)
- `SubAdminSidebarTest` — log in as each role, GET /admin, parse rendered sidebar HTML, assert exact set of links present per role
- `MemberDirectoryTest` — Site Admin can filter by role, last_login, verified; "Send invite" action issues a signed reset link via Password::broker

**Site (`tests/Feature/Site/`)**
- `DonateTest` — /donate returns 200, renders donate page body, donate button visible in public header + footer; admin can edit donate page content via Pages CRUD

---

## 10. Build Sequence

Mirrors Phase 1's pattern. Per project workflow: design-first sign-off gate at M1 only (visual approval); subsequent milestones proceed without user review.

| Milestone | Output | Sign-off? |
|---|---|---|
| **M1** | (a) Member layout + UI/UX preview under `/preview/member/*` with static seed (Dashboard, Profile, Prayer list+create, Knock list+create, Feed, Donate). (b) **Audit + fix all Alpine dropdowns across Phase 1 views** (admin topbar user menu, Pages-edit "Add section", public site mobile drawer, any other `x-data` menus) — verify each opens, closes on click-away, supports keyboard (Esc to close), has proper `aria-expanded`/`aria-haspopup`. (c) **Responsive QA pass** on every Phase 1 + Phase 2 view across sm/md/lg/xl breakpoints — no horizontal scroll on mobile, all forms usable, nav collapses correctly. | **YES — visual sign-off gate** |
| **M2** | DB: 4 migrations, model factories, observer for pray_count, permission seeder additions, settings + donate page seeders | No |
| **M3** | Member auth flow: /register, email verification, member layout wiring, member dashboard with real DB content, profile edit + password change | No |
| **M4** | Prayer Requests: member CRUD + community board + pray toggle + admin index/detail/update/destroy | No |
| **M5** | Knock for Help: member submit + admin list/detail/update + email-on-create | No |
| **M6** | Community Feed: admin CRUD + member read/react + optional broadcast email | No |
| **M7** | Donate page wiring: extend PageController, donate button in headers/footers, public route | No |
| **M8** | Tests: ~45 Pest tests across the above | No |
| **M9** | Polish: README update with Phase 2 features, member onboarding doc, final smoke probe | No |

---

## 11. Acceptance Criteria for Phase 2

- Visitor registers at `/register`, receives verification email, clicks link, lands on `/member` dashboard
- Member submits a public anonymous prayer request → it appears on community board as "Anonymous"; pray button works for other members; submitter sees status in their own dashboard
- Member submits a Knock for Help → Prayer Organizer + Site Admin receive an email; admin marks Responding then Closed; status changes logged
- Site Admin creates a Community Feed post with "Send to members" checked → all verified members receive email; logged-in members see post at top of `/member/feed`; reaction buttons increment per click
- Site Admin opens "Members" page, filters by Prayer Organizer role, sees the right user; "Send invite" issues a reset link
- Public visitor visits `/donate` and sees bank details exactly as Site Admin entered them via Pages CRUD
- Logged in as **Prayer Organizer**: admin sidebar shows exactly Dashboard + Prayer Requests + Knock for Help + Community Feed (4 items, nothing else)
- Logged in as **Event Organizer**: admin sidebar shows Dashboard + Events + Ministries + Attendance (Phase 3 stub) + Reminders (Phase 3 stub)
- Logged in as **Site Admin**: admin sidebar shows all 16+ items including Phase 2 additions
- All ~120 Pest tests green
- README updated with Phase 2 setup notes; no new env vars required
- **Every page (public + member + admin) is fully responsive** — tested on sm 375px, md 768px, lg 1024px, xl 1440px viewports; no horizontal scroll; nav collapses to mobile drawer below `md`; forms remain usable; tables scroll horizontally inside their container on mobile without breaking the page layout
- **Every Alpine dropdown works correctly** — opens on click, closes on click-away, closes on Escape, sets `aria-expanded`/`aria-haspopup`. Covers: admin topbar user menu, admin sidebar collapse, Pages-edit "Add section", any select-like menu, public site mobile drawer, member portal mobile drawer, all reaction buttons

---

## 12. Deferred to Phase 3

- Attendance tracking
- Event registration
- Reminders engine (scheduled mailers via Laravel Scheduler)
- Reports / analytics
- Communication module (admin → segmented member groups)
- Member-authored feed posts + comments
- In-app notification center
- Member-authored prayer comments
- Knock for Help internal team chat thread
- Member-to-member messaging
- Mobile app (still deferred indefinitely)

Stripe / online giving has been dropped from the project entirely.
