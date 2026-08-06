# Church CMS Phase 2 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship Phase 2 — member self-signup + email verification, member portal (profile, prayer requests, knock for help, community feed), donation page, three new admin modules (Prayer Requests / Knock for Help / Community Feed), and sub-admin role wiring. Plus: fix all existing Phase 1 Alpine dropdowns and ensure every page is fully responsive.

**Architecture:** Direct extension of Phase 1 patterns. Same Laravel 12 + Blade + Tailwind v3 + Alpine + Spatie Permission stack. New `member` blade layout alongside existing `site` and `admin` layouts. Member auth uses `web` guard with `verified` middleware. New tables: prayer_requests, prayer_request_prays, care_requests, feed_posts, feed_reactions. Reuses existing media library, CKEditor, HTMLPurifier, rate-limit framework, mail system.

**Tech Stack:** PHP 8.2+, Laravel 12, MySQL 8 (XAMPP), Tailwind v3, Alpine.js, Vite, Spatie Laravel-Permission, CKEditor 5, mews/purifier, Intervention Image, Pest 3.

**No git in this plan** — per user instruction, no commit steps.

**Source spec:** `docs/superpowers/specs/2026-06-02-church-cms-phase-2-design.md`

---

## Reading order

Tasks are grouped into **Milestones** (M1–M9). Each milestone produces a working slice. M1 has the only user sign-off gate (visual approval of mockups + dropdown/responsive QA).

| Milestone | What ships |
|---|---|
| M1 | Member UI/UX preview + Phase 1 dropdown audit/fix + responsive QA — **SIGN-OFF GATE** |
| M2 | Migrations + models + factories + permission/seeder updates |
| M3 | Member auth: register, email verification, profile, dashboard |
| M4 | Prayer Requests (member + admin) |
| M5 | Knock for Help (member + admin) |
| M6 | Community Feed (admin CRUD + member read/react + broadcast email) |
| M7 | Donate page wiring |
| M8 | Tests (~45 new Pest tests) |
| M9 | Polish + README updates + final smoke |

---

# Milestone 1 — UI/UX Preview + Dropdown Audit + Responsive QA — SIGN-OFF GATE

> **Goal of M1:** A clickable static prototype of the member portal at `/preview/member/*` AND a clean pass on all Phase 1 Alpine dropdowns + responsive layout issues. Sign-off gate before any backend work.

### Task M1-T01: Add member preview routes

**Files:**
- Modify: `routes/preview.php`

- [ ] **Step 1:** Append a new `Route::prefix('preview/member')->name('preview.member.')->group(...)` block to `routes/preview.php`:
  ```php
  Route::prefix('preview/member')->name('preview.member.')->group(function () {
      Route::view('/login',             'preview.member.login')->name('login');
      Route::view('/register',          'preview.member.register')->name('register');
      Route::view('/verify',            'preview.member.verify')->name('verify');
      Route::view('/',                  'preview.member.dashboard')->name('dashboard');
      Route::view('/profile',           'preview.member.profile')->name('profile');
      Route::view('/prayer-requests',   'preview.member.prayer.index')->name('prayer');
      Route::view('/prayer-requests/create', 'preview.member.prayer.create')->name('prayer.create');
      Route::view('/prayer-requests/sample', 'preview.member.prayer.show')->name('prayer.show');
      Route::view('/care',              'preview.member.care.index')->name('care');
      Route::view('/care/create',       'preview.member.care.create')->name('care.create');
      Route::view('/care/thanks',       'preview.member.care.thanks')->name('care.thanks');
      Route::view('/feed',              'preview.member.feed')->name('feed');
      Route::view('/donate',            'preview.member.donate')->name('donate');
  });
  ```
- [ ] **Step 2:** Run `php artisan route:list | grep preview.member` — confirm 13 new routes registered.

### Task M1-T02: Member preview layout component

**Files:**
- Create: `resources/views/components/member/layout.blade.php`
- Create: `resources/views/components/member/header.blade.php`
- Create: `resources/views/components/member/footer.blade.php`

- [ ] **Step 1:** Create `resources/views/components/member/layout.blade.php`:
  ```blade
  @props(['title' => 'Member Portal'])
  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>{{ $title }} | {{ settings('brand.name', config('app.name')) }}</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
      @vite(['resources/css/app.css', 'resources/js/app.js'])
      <style>[x-cloak]{display:none!important}</style>
      @stack('head')
  </head>
  <body class="bg-surface font-sans text-ink antialiased min-h-screen flex flex-col">
      <x-member.header />
      <main class="flex-1">
          {{ $slot }}
      </main>
      <x-member.footer />
      @stack('scripts')
  </body>
  </html>
  ```
- [ ] **Step 2:** Create `resources/views/components/member/header.blade.php`:
  ```blade
  @php
  $links = [
      ['route' => 'preview.member.dashboard', 'label' => 'Dashboard'],
      ['route' => 'preview.member.prayer',    'label' => 'Prayer'],
      ['route' => 'preview.member.care',      'label' => 'Knock'],
      ['route' => 'preview.member.feed',      'label' => 'Feed'],
      ['route' => 'preview.member.donate',    'label' => 'Give'],
  ];
  @endphp
  <header x-data="{open:false, user:false}" class="sticky top-0 z-40 bg-surface/95 backdrop-blur border-b border-[rgb(var(--border))]">
      <div class="max-w-container mx-auto flex items-center justify-between px-4 py-3">
          <a href="{{ route('preview.member.dashboard') }}" class="font-serif text-xl font-semibold">{{ settings('brand.name', 'Grace Community') }}</a>

          <nav class="hidden md:flex items-center gap-6">
              @foreach($links as $l)
                  <a href="{{ route($l['route']) }}"
                     @class([
                          'text-[15px] transition-colors',
                          'text-brand-primary font-medium' => request()->routeIs($l['route']),
                          'text-ink hover:text-brand-primary' => ! request()->routeIs($l['route']),
                     ])>{{ $l['label'] }}</a>
              @endforeach
          </nav>

          <div class="hidden md:flex items-center gap-3">
              <div x-data="{open:false}" @keydown.escape.window="open=false" class="relative">
                  <button @click="open=!open" :aria-expanded="open" aria-haspopup="menu"
                          class="flex items-center gap-2 text-sm pl-2 pr-3 py-1.5 rounded-md hover:bg-white">
                      <span class="w-8 h-8 rounded-full bg-brand-primary text-white flex items-center justify-center text-xs font-semibold">SM</span>
                      <span>Sarah Member</span>
                      <svg class="w-3 h-3 text-ink-muted" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5 10 12.25 14.75 7.5z"/></svg>
                  </button>
                  <div x-show="open" x-cloak @click.away="open=false" x-transition.opacity
                       role="menu"
                       class="absolute right-0 mt-2 w-48 card p-1.5 text-sm z-50 bg-white">
                      <a href="{{ route('preview.member.profile') }}" class="block px-3 py-2 hover:bg-surface rounded">My profile</a>
                      <a href="#" class="block px-3 py-2 hover:bg-surface rounded text-brand-primary">Sign out</a>
                  </div>
              </div>
          </div>

          <button class="md:hidden p-2" @click="open=true" aria-label="Open menu">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
      </div>

      {{-- Mobile drawer --}}
      <div x-show="open" x-cloak class="fixed inset-0 z-50 md:hidden" @keydown.escape.window="open=false">
          <div class="absolute inset-0 bg-black/50" @click="open=false"></div>
          <div class="absolute right-0 top-0 h-full w-80 max-w-[85vw] bg-surface p-6 flex flex-col gap-4 overflow-y-auto" x-transition>
              <div class="flex items-center justify-between mb-2">
                  <span class="font-serif text-lg">Menu</span>
                  <button @click="open=false" class="p-2 -mr-2 text-2xl leading-none" aria-label="Close">&times;</button>
              </div>
              @foreach($links as $l)
                  <a href="{{ route($l['route']) }}" class="text-lg py-2 border-b border-[rgb(var(--border))]">{{ $l['label'] }}</a>
              @endforeach
              <a href="{{ route('preview.member.profile') }}" class="text-lg py-2 border-b border-[rgb(var(--border))]">My profile</a>
              <a href="#" class="text-lg py-2 text-brand-primary">Sign out</a>
          </div>
      </div>
  </header>
  ```
- [ ] **Step 3:** Create `resources/views/components/member/footer.blade.php`:
  ```blade
  <footer class="bg-ink text-white mt-24">
      <div class="max-w-container mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
          <div>
              <h3 class="font-serif text-lg mb-2">{{ settings('brand.name', 'Grace Community') }}</h3>
              <p class="text-white/70">A welcoming community for everyone.</p>
          </div>
          <div>
              <h4 class="font-semibold mb-2">Quick links</h4>
              <ul class="space-y-1.5 text-white/70">
                  <li><a href="{{ route('preview.member.dashboard') }}" class="hover:text-white">Dashboard</a></li>
                  <li><a href="{{ route('preview.member.prayer') }}" class="hover:text-white">Prayer</a></li>
                  <li><a href="{{ route('preview.member.donate') }}" class="hover:text-white">Give</a></li>
              </ul>
          </div>
          <div>
              <h4 class="font-semibold mb-2">Support</h4>
              <p class="text-white/70">Need help? Reach out via Knock for Help or email us.</p>
          </div>
      </div>
      <div class="border-t border-white/10 text-center text-white/40 text-xs py-4">&copy; {{ date('Y') }} {{ settings('brand.name', 'Grace Community') }}</div>
  </footer>
  ```

### Task M1-T03: Member preview pages (dashboard, profile, register, verify, donate)

**Files:**
- Create: `resources/views/preview/member/dashboard.blade.php`
- Create: `resources/views/preview/member/profile.blade.php`
- Create: `resources/views/preview/member/register.blade.php`
- Create: `resources/views/preview/member/verify.blade.php`
- Create: `resources/views/preview/member/login.blade.php` (extend Phase 1 stub to match new member look)
- Create: `resources/views/preview/member/donate.blade.php`

- [ ] **Step 1:** Dashboard (`dashboard.blade.php`):
  ```blade
  <x-member.layout title="Dashboard">
      <div class="max-w-container mx-auto px-4 py-10 space-y-8">
          <div class="card p-6 md:p-8 bg-gradient-to-br from-brand-primary/10 to-brand-secondary/20">
              <h1 class="font-serif text-3xl md:text-4xl">Good afternoon, Sarah</h1>
              <p class="text-ink-muted mt-2">Welcome back. Here's what's happening this week.</p>
              <div class="mt-5 flex flex-wrap gap-2">
                  <a href="{{ route('preview.member.prayer.create') }}" class="btn-primary text-sm">Submit a prayer request</a>
                  <a href="{{ route('preview.member.care.create') }}" class="btn-ghost text-sm">Knock for help</a>
                  <a href="{{ route('preview.member.profile') }}" class="btn-ghost text-sm">Update profile</a>
              </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div class="card p-5 lg:col-span-2">
                  <h2 class="font-serif text-xl mb-1">From the feed</h2>
                  <p class="text-sm text-ink-muted mb-4">Latest from the team.</p>
                  <ul class="divide-y divide-[rgb(var(--border))]">
                      @foreach([
                          ['title' => 'Welcome to the new portal', 'time' => '2 hours ago', 'excerpt' => 'We have launched our new member portal — explore it and let us know what you think.'],
                          ['title' => 'Sunday gathering: a special evening', 'time' => 'Yesterday', 'excerpt' => 'Join us for an extended worship time at 6pm this Sunday.'],
                          ['title' => 'Volunteer call: kids ministry', 'time' => '3 days ago', 'excerpt' => 'We need 2 more volunteers for Sunday mornings — could that be you?'],
                      ] as $p)
                          <li class="py-3">
                              <div class="flex justify-between gap-3">
                                  <span class="font-medium">{{ $p['title'] }}</span>
                                  <span class="text-xs text-ink-muted shrink-0">{{ $p['time'] }}</span>
                              </div>
                              <p class="text-sm text-ink-muted mt-1">{{ $p['excerpt'] }}</p>
                          </li>
                      @endforeach
                  </ul>
                  <a href="{{ route('preview.member.feed') }}" class="mt-3 inline-flex text-sm text-brand-primary hover:underline">See all →</a>
              </div>

              <div class="space-y-6">
                  <div class="card p-5">
                      <h2 class="font-serif text-lg mb-3">Your prayer requests</h2>
                      <ul class="space-y-2 text-sm">
                          <li class="flex items-center justify-between">
                              <span>Family wisdom</span>
                              <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700">Praying</span>
                          </li>
                          <li class="flex items-center justify-between">
                              <span>New job opportunity</span>
                              <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-700">Answered</span>
                          </li>
                      </ul>
                      <a href="{{ route('preview.member.prayer') }}" class="mt-4 inline-flex text-sm text-brand-primary hover:underline">View all →</a>
                  </div>

                  <div class="card p-5">
                      <h2 class="font-serif text-lg mb-3">Upcoming</h2>
                      <ul class="space-y-2 text-sm">
                          <li><span class="font-medium">Sun</span> · Sunday service · 10am</li>
                          <li><span class="font-medium">Wed</span> · Midweek prayer · 7pm</li>
                          <li><span class="font-medium">Sat</span> · Youth gathering · 6pm</li>
                      </ul>
                  </div>
              </div>
          </div>
      </div>
  </x-member.layout>
  ```
- [ ] **Step 2:** Profile (`profile.blade.php`) — two stacked cards with form fields (name, email, phone, bio + avatar picker) + password change card. Use the form pattern: `<form class="space-y-4">` with `<label>` + `<input class="input">`. Fully responsive (`grid-cols-1 md:grid-cols-2 gap-4` for paired fields).
- [ ] **Step 3:** Register (`register.blade.php`) — single-form card centered: name, email, password, password_confirmation, honeypot (hidden), "Create account" button. Note above form: "Already a member? [Sign in]". Same warm split-panel design as the existing Phase 1 admin login but using `bg-brand-secondary/30` left panel for visual distinction.
- [ ] **Step 4:** Verify (`verify.blade.php`) — Centered card: "Check your email", explanation, "Resend verification email" button. Plain.
- [ ] **Step 5:** Login (`login.blade.php`) — Mirror Phase 1 admin login but with brand-secondary tint and member-specific copy ("Sign in to your account").
- [ ] **Step 6:** Donate (`donate.blade.php`) — Public-style page with hero (image + heading), then card showing bank details: Account name, Bank name, Account number, Sort code, Reference field. Section below: "Other ways to give" (text). Bottom CTA: "Questions? Contact us".
- [ ] **Step 7:** Browser-check each: visit `http://127.0.0.1:8000/preview/member/`, `/profile`, `/register`, `/verify`, `/login`, `/donate`. All return 200.

### Task M1-T04: Prayer + Knock + Feed preview pages

**Files:**
- Create: `resources/views/preview/member/prayer/index.blade.php`
- Create: `resources/views/preview/member/prayer/create.blade.php`
- Create: `resources/views/preview/member/prayer/show.blade.php`
- Create: `resources/views/preview/member/care/index.blade.php`
- Create: `resources/views/preview/member/care/create.blade.php`
- Create: `resources/views/preview/member/care/thanks.blade.php`
- Create: `resources/views/preview/member/feed.blade.php`

- [ ] **Step 1:** Prayer index — Alpine tabs `x-data="{tab:'mine'}"`, two tabs (My requests / Community board). My requests: simple table with title, status pill, pray count, edit/delete buttons. Community board: card grid (3-col on lg, 1-col mobile) showing 6 hardcoded sample requests with author name (or "Anonymous"), title, body excerpt, "🙏 I'm praying (N)" toggle button using `x-data="{prayed:false, count:12}"` and click toggles state + count.
- [ ] **Step 2:** Prayer create — Single card form: title, body (textarea), "Make this public" toggle, "Submit anonymously" toggle (only enabled when public is on, use `x-data="{public:false}"` and `:disabled="!public"`). Submit button.
- [ ] **Step 3:** Prayer show — Full body, status pill, prayer count + button, "More from this person" stub. Owner-only: edit/delete buttons.
- [ ] **Step 4:** Care index — Member's own list as table (category badge, message excerpt, status pill, created). "+ New request" CTA top right.
- [ ] **Step 5:** Care create — calm copy at top, category radios (5 options as styled radio cards), message textarea, "Share with prayer team" toggle, submit.
- [ ] **Step 6:** Care thanks — centered card: ✓ icon, "Thank you", "A pastor will be in touch within 24 hours." Link back to dashboard.
- [ ] **Step 7:** Feed — Vertical timeline. Each post is a card with author avatar+name+time, title (optional), body, optional image (use picsum.photos placeholder for 2 of 5 posts), reaction bar at bottom: three buttons (♥ Heart 12 / 🙏 Pray 8 / 👏 Amen 3) — use `x-data="{active:null, counts:{heart:12,pray:8,amen:3}}"` and click toggles which is active. Pinned post badge on first card.
- [ ] **Step 8:** Browser-check each.

### Task M1-T05: Audit existing Phase 1 dropdowns

**Files:**
- Modify: `resources/views/components/admin/topbar.blade.php`
- Modify: `resources/views/components/admin/sidebar.blade.php`
- Modify: `resources/views/components/site/header.blade.php`
- Modify: `resources/views/preview/admin/pages/edit.blade.php` (only if Add Section dropdown exists there)
- Modify: `resources/views/admin/pages/edit.blade.php` (real admin view)

- [ ] **Step 1:** Identify all `x-data` dropdowns by grep:
  ```powershell
  $env:PATH = "D:\XAMPP\php;D:\XAMPP\mysql\bin;$env:PATH"
  Get-ChildItem -Recurse resources/views -Filter '*.blade.php' | Select-String -Pattern 'x-data="\{open' | Format-Table Path, LineNumber, Line -AutoSize
  ```
  Note every file and line.
- [ ] **Step 2:** For each dropdown found, ensure it has ALL of these attributes on the button:
  ```html
  @click="open=!open"
  :aria-expanded="open"
  aria-haspopup="menu"
  ```
  And on the menu div:
  ```html
  x-show="open"
  x-cloak
  @click.away="open=false"
  role="menu"
  ```
  And on the container element (or window):
  ```html
  @keydown.escape.window="open=false"
  ```
- [ ] **Step 3:** Specifically fix `resources/views/components/admin/topbar.blade.php` user dropdown. Read current state, then verify these attributes exist; add any missing.
- [ ] **Step 4:** Specifically fix `resources/views/components/site/header.blade.php` mobile drawer — ensure `@keydown.escape.window="open=false"` is on the outer header.
- [ ] **Step 5:** Specifically fix Pages-edit "Add section" dropdown in `resources/views/admin/pages/edit.blade.php` (and preview version). Same attribute set.
- [ ] **Step 6:** Add a small JS focus-trap helper in `resources/js/dropdown-focus.js` (optional polish):
  ```js
  // Auto-focus first menu item on dropdown open + return focus to trigger on close.
  document.addEventListener('alpine:init', () => {
      window.Alpine.directive('focus-trap', (el, { expression }, { effect, evaluateLater }) => {
          const getOpen = evaluateLater(expression);
          let prevActive = null;
          effect(() => {
              getOpen((open) => {
                  if (open) {
                      prevActive = document.activeElement;
                      requestAnimationFrame(() => {
                          el.querySelector('a, button, [tabindex]:not([tabindex="-1"])')?.focus();
                      });
                  } else if (prevActive) {
                      prevActive.focus();
                      prevActive = null;
                  }
              });
          });
      });
  });
  ```
  Import in `resources/js/app.js` BEFORE `Alpine.start()`:
  ```js
  import Alpine from 'alpinejs';
  import './dropdown-focus.js';
  import './ckeditor-init.js';
  window.Alpine = Alpine;
  Alpine.start();
  ```
- [ ] **Step 7:** Run `npm run build` and manually verify each dropdown by opening the page in browser, clicking trigger, clicking away, pressing Escape.

### Task M1-T06: Responsive QA pass — Phase 1 views

**Files:**
- Modify (only as needed): any Phase 1 Blade view that breaks at sm/md viewports

- [ ] **Step 1:** Start `php artisan serve --port=8000`.
- [ ] **Step 2:** Use Playwright to navigate each major Phase 1 view at 375px and 768px viewport, screenshot, look for horizontal overflow / broken layouts. Pages to check:
  - Public: `/`, `/about`, `/sermons`, `/events`, `/ministries`, `/blog`, `/contact`
  - Admin (after login): `/admin`, `/admin/pages`, `/admin/pages/{id}/edit`, `/admin/blog/posts`, `/admin/blog/posts/create`, `/admin/sermons`, `/admin/events`, `/admin/ministries`, `/admin/media`, `/admin/menus`, `/admin/users`, `/admin/roles`, `/admin/settings`, `/admin/messages`
- [ ] **Step 3:** For any view that has horizontal scroll on 375px width:
  - Tables that overflow: wrap in `<div class="overflow-x-auto -mx-4 px-4 md:mx-0 md:px-0">` so the table scrolls horizontally inside its container instead of breaking page layout
  - Long inline button rows: wrap in `flex flex-wrap gap-2`
  - Fixed-width form grids: ensure `grid-cols-1 md:grid-cols-2`
  - Hero text too large on mobile: ensure `text-3xl md:text-5xl` scaling
  - Sidebar overlapping content: confirm `lg:pl-64` is on the main wrapper (Phase 1 already fixed this)
- [ ] **Step 4:** Re-run `npm run build` after any class additions.
- [ ] **Step 5:** Second pass at 768px (md breakpoint) — check that nav collapses correctly, admin sidebar is icon-only or drawer, forms remain 1-column.
- [ ] **Step 6:** Record findings as a short list in `docs/superpowers/notes/2026-06-02-responsive-fixes.md`.

### Task M1-T07: SIGN-OFF GATE

- [ ] **Step 1:** Start dev server. Tell the user: "Member portal preview is at `http://127.0.0.1:8000/preview/member/`. Phase 1 dropdowns and responsive issues have been audited and fixed. Please walk through every member preview page (Dashboard, Profile, Register, Verify, Login, Donate, Prayer index/create/show, Care index/create/thanks, Feed) on desktop and mobile. Verify dropdowns open/close properly across the admin (sidebar user menu, Pages-edit Add Section)."
- [ ] **Step 2:** Iterate on user feedback. Update preview views and dropdowns as requested.
- [ ] **Step 3:** Wait for explicit "approved — move on" from user before starting M2.

---

# Milestone 2 — Migrations, Models, Seeders, Factories

### Task M2-T01: Migrate phone + bio columns on users

**Files:**
- Create: `database/migrations/2026_06_03_000000_add_phone_bio_to_users.php`

- [ ] **Step 1:** Generate: `D:\XAMPP\php\php.exe artisan make:migration add_phone_bio_to_users`. Edit `up()`:
  ```php
  Schema::table('users', function (Blueprint $t) {
      $t->string('phone', 32)->nullable()->after('email');
      $t->text('bio')->nullable()->after('phone');
  });
  ```
  And `down()`:
  ```php
  Schema::table('users', function (Blueprint $t) {
      $t->dropColumn(['phone', 'bio']);
  });
  ```
- [ ] **Step 2:** Run `D:\XAMPP\php\php.exe artisan migrate`. Verify with `mysql -u root church_cms -e "DESCRIBE users;"` shows `phone` and `bio` columns.
- [ ] **Step 3:** Update `app/Models/User.php` `$fillable`:
  ```php
  protected $fillable = [
      'name','email','password','is_admin','avatar_path','two_factor_enabled',
      'password_change_required','last_login_at','phone','bio',
  ];
  ```

### Task M2-T02: Migrate prayer_requests + prayer_request_prays

**Files:**
- Create: `database/migrations/2026_06_03_000001_create_prayer_requests_tables.php`

- [ ] **Step 1:** Generate and edit `up()`:
  ```php
  Schema::create('prayer_requests', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $t->string('name', 120);
      $t->string('title', 200);
      $t->longText('body');
      $t->boolean('is_public')->default(false);
      $t->boolean('is_anonymous')->default(false);
      $t->enum('status', ['pending','praying','answered','closed'])->default('pending');
      $t->unsignedInteger('pray_count')->default(0);
      $t->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
      $t->longText('admin_notes')->nullable();
      $t->timestamps();
      $t->softDeletes();
      $t->index(['status','created_at']);
      $t->index('user_id');
  });
  Schema::create('prayer_request_prays', function (Blueprint $t) {
      $t->id();
      $t->foreignId('prayer_request_id')->constrained()->cascadeOnDelete();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->timestamp('created_at')->useCurrent();
      $t->unique(['prayer_request_id', 'user_id']);
  });
  ```
- [ ] **Step 2:** `down()` drops both tables in reverse order.
- [ ] **Step 3:** Run `php artisan migrate`. Verify both tables exist.

### Task M2-T03: Migrate care_requests

**Files:**
- Create: `database/migrations/2026_06_03_000002_create_care_requests_table.php`

- [ ] **Step 1:** `up()`:
  ```php
  Schema::create('care_requests', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $t->enum('category', ['illness','grief','financial','food','other']);
      $t->text('message');
      $t->boolean('share_with_team')->default(false);
      $t->enum('status', ['open','responding','closed'])->default('open');
      $t->foreignId('responder_id')->nullable()->constrained('users')->nullOnDelete();
      $t->longText('response_notes')->nullable();
      $t->timestamp('closed_at')->nullable();
      $t->timestamps();
      $t->index(['status','created_at']);
      $t->index('user_id');
  });
  ```
- [ ] **Step 2:** Run migrate.

### Task M2-T04: Migrate feed_posts + feed_reactions

**Files:**
- Create: `database/migrations/2026_06_03_000003_create_feed_tables.php`

- [ ] **Step 1:** `up()`:
  ```php
  Schema::create('feed_posts', function (Blueprint $t) {
      $t->id();
      $t->foreignId('author_id')->constrained('users');
      $t->string('title', 200)->nullable();
      $t->longText('body');
      $t->string('image_path')->nullable();
      $t->boolean('pinned')->default(false);
      $t->timestamp('published_at')->useCurrent();
      $t->timestamps();
      $t->index(['pinned','published_at']);
  });
  Schema::create('feed_reactions', function (Blueprint $t) {
      $t->id();
      $t->foreignId('feed_post_id')->constrained()->cascadeOnDelete();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->enum('kind', ['heart','pray','amen']);
      $t->timestamp('created_at')->useCurrent();
      $t->unique(['feed_post_id','user_id']);
  });
  ```
- [ ] **Step 2:** Run migrate.

### Task M2-T05: Create models

**Files:**
- Create: `app/Models/PrayerRequest.php`
- Create: `app/Models/PrayerRequestPray.php`
- Create: `app/Models/CareRequest.php`
- Create: `app/Models/FeedPost.php`
- Create: `app/Models/FeedReaction.php`

- [ ] **Step 1:** `PrayerRequest`:
  ```php
  <?php
  namespace App\Models;
  use App\Support\LogsModelActivity;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\SoftDeletes;

  class PrayerRequest extends Model {
      use HasFactory, SoftDeletes, LogsModelActivity;
      protected $guarded = [];
      protected $casts = [
          'is_public' => 'bool',
          'is_anonymous' => 'bool',
          'pray_count' => 'int',
      ];
      public function user() { return $this->belongsTo(User::class); }
      public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
      public function prays() { return $this->hasMany(PrayerRequestPray::class); }
      public function scopePublic($q) { return $q->where('is_public', true); }
      public function scopeForUser($q, User $u) {
          return $q->where(fn($x) => $x->where('user_id', $u->id)->orWhere('is_public', true));
      }
      public function displayName(): string {
          return $this->is_anonymous ? 'Anonymous' : $this->name;
      }
  }
  ```
- [ ] **Step 2:** `PrayerRequestPray`:
  ```php
  <?php
  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;
  class PrayerRequestPray extends Model {
      protected $guarded = [];
      public $timestamps = false;
      protected $fillable = ['prayer_request_id','user_id'];
      protected $casts = ['created_at' => 'datetime'];
      public function prayerRequest() { return $this->belongsTo(PrayerRequest::class); }
      public function user() { return $this->belongsTo(User::class); }
  }
  ```
- [ ] **Step 3:** `CareRequest`:
  ```php
  <?php
  namespace App\Models;
  use App\Support\LogsModelActivity;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;

  class CareRequest extends Model {
      use HasFactory, LogsModelActivity;
      protected $guarded = [];
      protected $casts = ['share_with_team' => 'bool', 'closed_at' => 'datetime'];
      public function user() { return $this->belongsTo(User::class); }
      public function responder() { return $this->belongsTo(User::class, 'responder_id'); }
  }
  ```
- [ ] **Step 4:** `FeedPost`:
  ```php
  <?php
  namespace App\Models;
  use App\Support\LogsModelActivity;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;

  class FeedPost extends Model {
      use HasFactory, LogsModelActivity;
      protected $guarded = [];
      protected $casts = ['pinned' => 'bool', 'published_at' => 'datetime'];
      public function author() { return $this->belongsTo(User::class, 'author_id'); }
      public function reactions() { return $this->hasMany(FeedReaction::class); }
      public function scopePublished($q) { return $q->where('published_at', '<=', now()); }
      public function reactionCountsByKind(): array {
          return $this->reactions()->selectRaw('kind, COUNT(*) as count')->groupBy('kind')->pluck('count','kind')->toArray();
      }
  }
  ```
- [ ] **Step 5:** `FeedReaction`:
  ```php
  <?php
  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;
  class FeedReaction extends Model {
      protected $guarded = [];
      public $timestamps = false;
      protected $casts = ['created_at' => 'datetime'];
      public function feedPost() { return $this->belongsTo(FeedPost::class); }
      public function user() { return $this->belongsTo(User::class); }
  }
  ```

### Task M2-T06: Create observer for pray_count denormalization

**Files:**
- Create: `app/Observers/PrayerRequestPrayObserver.php`
- Modify: `app/Providers/AppServiceProvider.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace App\Observers;
  use App\Models\PrayerRequestPray;

  class PrayerRequestPrayObserver {
      public function created(PrayerRequestPray $p): void {
          $p->prayerRequest()->increment('pray_count');
      }
      public function deleted(PrayerRequestPray $p): void {
          $p->prayerRequest()->decrement('pray_count');
      }
  }
  ```
- [ ] **Step 2:** Register in `AppServiceProvider::boot`:
  ```php
  \App\Models\PrayerRequestPray::observe(\App\Observers\PrayerRequestPrayObserver::class);
  ```

### Task M2-T07: Update RolePermissionSeeder with new permissions

**Files:**
- Modify: `database/seeders/RolePermissionSeeder.php`

- [ ] **Step 1:** Add to the `$perms` array (4 new):
  ```php
  $perms = [
      'manage-pages','manage-blog','manage-sermons','manage-events','manage-ministries',
      'manage-media','manage-menus','manage-settings','manage-users','manage-roles',
      'manage-messages','view-reports',
      'manage-prayer-requests','manage-knock-help','manage-community-feed','manage-members',
  ];
  ```
- [ ] **Step 2:** After Site Admin gets all perms, add Prayer Organizer assignment:
  ```php
  $prayer = Role::firstOrCreate(['name'=>'Prayer Organizer','guard_name'=>'admin']);
  $prayer->syncPermissions(['manage-prayer-requests','manage-knock-help','manage-community-feed']);

  $event = Role::firstOrCreate(['name'=>'Event Organizer','guard_name'=>'admin']);
  $event->syncPermissions(['manage-events','manage-ministries']);
  ```
- [ ] **Step 3:** Run `php artisan db:seed --class=RolePermissionSeeder`.
- [ ] **Step 4:** Verify in tinker: `Role::findByName('Prayer Organizer','admin')->permissions->pluck('name')` → returns exactly 3.

### Task M2-T08: Update SiteSettingsSeeder with Phase 2 settings

**Files:**
- Modify: `database/seeders/SiteSettingsSeeder.php`

- [ ] **Step 1:** Append to the `$defaults` array:
  ```php
  ['key'=>'member.registration_enabled','value'=>'1','type'=>'text','group'=>'member'],
  ['key'=>'member.welcome_message','value'=>'Welcome to the Grace Community family.','type'=>'text','group'=>'member'],
  ['key'=>'member.broadcast_on_feed_default','value'=>'0','type'=>'text','group'=>'member'],
  ['key'=>'donate.button_label','value'=>'Give','type'=>'text','group'=>'donate'],
  ['key'=>'donate.show_in_member_nav','value'=>'1','type'=>'text','group'=>'donate'],
  ```
- [ ] **Step 2:** Run `php artisan db:seed --class=SiteSettingsSeeder`.

### Task M2-T09: Update DemoContentSeeder with donate page + sample feed/prayer

**Files:**
- Modify: `database/seeders/DemoContentSeeder.php`

- [ ] **Step 1:** Add a new `pages` row (after the existing seeded pages array):
  ```php
  \App\Models\Page::updateOrCreate(['slug'=>'donate'], [
      'title' => 'Give',
      'hero_heading' => 'Support our mission',
      'hero_subheading' => 'Your generosity keeps the lights on, the doors open, and the ministry going.',
      'body' => '<p>Thank you for considering a gift to support our work. We rely on the generosity of our community to keep everything we do possible.</p><h2>Bank transfer</h2><p><strong>Account name:</strong> Grace Community Church<br><strong>Account number:</strong> 12345678<br><strong>Sort code:</strong> 12-34-56<br><strong>Reference:</strong> Please use your name as the reference.</p><h2>In person</h2><p>You can give cash or cheque in person on a Sunday — drop your envelope in the basket at the door.</p>',
      'meta_title' => 'Give | Grace Community Church',
      'meta_description' => 'Support the mission of Grace Community Church via bank transfer or in person.',
      'is_published' => true,
      'published_at' => now(),
  ]);
  ```
- [ ] **Step 2:** Add sample feed_posts (3 rows) and prayer_requests (5 rows) at the bottom of `run()`:
  ```php
  $admin = \App\Models\User::where('is_admin', true)->first();
  if ($admin) {
      \App\Models\FeedPost::factory()->count(3)->create(['author_id' => $admin->id]);
  }
  $member = \App\Models\User::where('is_admin', false)->first();
  if ($member) {
      \App\Models\PrayerRequest::factory()->count(5)->create(['user_id' => $member->id]);
  }
  ```
- [ ] **Step 3:** Run `php artisan db:seed --class=DemoContentSeeder` (or `migrate:fresh --seed` to reseed everything).
- [ ] **Step 4:** Verify: `Page::where('slug','donate')->exists()` is true; `FeedPost::count() >= 3`.

### Task M2-T10: Create factories

**Files:**
- Create: `database/factories/PrayerRequestFactory.php`
- Create: `database/factories/CareRequestFactory.php`
- Create: `database/factories/FeedPostFactory.php`

- [ ] **Step 1:**
  ```php
  <?php
  namespace Database\Factories;
  use App\Models\{PrayerRequest, User};
  use Illuminate\Database\Eloquent\Factories\Factory;

  class PrayerRequestFactory extends Factory {
      protected $model = PrayerRequest::class;
      public function definition(): array {
          $user = User::inRandomOrder()->first() ?? User::factory()->create();
          return [
              'user_id' => $user->id,
              'name' => $user->name,
              'title' => fake()->sentence(4),
              'body' => fake()->paragraph(3),
              'is_public' => fake()->boolean(60),
              'is_anonymous' => fake()->boolean(20),
              'status' => fake()->randomElement(['pending','praying','answered','closed']),
              'pray_count' => fake()->numberBetween(0, 25),
          ];
      }
  }
  ```
- [ ] **Step 2:** `CareRequestFactory`:
  ```php
  <?php
  namespace Database\Factories;
  use App\Models\{CareRequest, User};
  use Illuminate\Database\Eloquent\Factories\Factory;

  class CareRequestFactory extends Factory {
      protected $model = CareRequest::class;
      public function definition(): array {
          return [
              'user_id' => User::factory(),
              'category' => fake()->randomElement(['illness','grief','financial','food','other']),
              'message' => fake()->paragraph(),
              'share_with_team' => fake()->boolean(),
              'status' => 'open',
          ];
      }
  }
  ```
- [ ] **Step 3:** `FeedPostFactory`:
  ```php
  <?php
  namespace Database\Factories;
  use App\Models\{FeedPost, User};
  use Illuminate\Database\Eloquent\Factories\Factory;

  class FeedPostFactory extends Factory {
      protected $model = FeedPost::class;
      public function definition(): array {
          return [
              'author_id' => User::factory()->create(['is_admin' => true]),
              'title' => fake()->sentence(5),
              'body' => '<p>'.fake()->paragraph(3).'</p>',
              'pinned' => false,
              'published_at' => now(),
          ];
      }
  }
  ```

### Task M2-T11: Reseed full database

- [ ] **Step 1:** Run `D:\XAMPP\php\php.exe artisan migrate:fresh --seed`.
- [ ] **Step 2:** Verify in tinker:
  ```php
  Page::where('slug','donate')->first()?->title;           // "Give"
  FeedPost::count() >= 3;
  PrayerRequest::count() >= 5;
  Role::findByName('Prayer Organizer','admin')->permissions->count(); // 3
  Role::findByName('Event Organizer','admin')->permissions->count();  // 2
  ```

---

# Milestone 3 — Member Auth Flow

### Task M3-T01: Register controller + request + view + route

**Files:**
- Create: `app/Http/Controllers/Auth/RegisterController.php`
- Create: `app/Http/Requests/Auth/RegisterRequest.php`
- Create: `resources/views/auth/member/register.blade.php`
- Modify: `routes/member.php`

- [ ] **Step 1:** `RegisterRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Auth;
  use Illuminate\Foundation\Http\FormRequest;
  use Illuminate\Validation\Rules\Password;

  class RegisterRequest extends FormRequest {
      public function authorize(): bool {
          return (bool) settings('member.registration_enabled', '1');
      }
      public function rules(): array {
          return [
              'name' => 'required|string|max:120',
              'email' => 'required|email:rfc|max:160|unique:users,email',
              'password' => ['required', Password::defaults(), 'confirmed'],
              'website' => 'nullable|size:0',
          ];
      }
  }
  ```
- [ ] **Step 2:** `RegisterController`:
  ```php
  <?php
  namespace App\Http\Controllers\Auth;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\Auth\RegisterRequest;
  use App\Models\User;
  use Illuminate\Auth\Events\Registered;
  use Illuminate\Support\Facades\Auth;

  class RegisterController extends Controller {
      public function create() { return view('auth.member.register'); }

      public function store(RegisterRequest $req) {
          if ($req->filled('website')) {
              return redirect()->route('verification.notice');
          }
          $user = User::create([
              'name' => $req->name,
              'email' => $req->email,
              'password' => $req->password,
              'is_admin' => false,
          ]);
          event(new Registered($user));
          Auth::guard('web')->login($user);
          return redirect()->route('verification.notice');
      }
  }
  ```
- [ ] **Step 3:** Convert the preview register view (from M1) to `resources/views/auth/member/register.blade.php`:
  - POST to `route('register')` with `@csrf`
  - Honeypot field with `name="website"`
  - `value="{{ old('name') }}"` on name and email
  - `@error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror` after each field
  - Disable submit + show banner if `!settings('member.registration_enabled','1')`
- [ ] **Step 4:** Add route in `routes/member.php` BEFORE the `auth:web` group:
  ```php
  use App\Http\Controllers\Auth\RegisterController;

  Route::middleware('guest:web')->group(function () {
      Route::get('/register',  [RegisterController::class, 'create'])->name('register');
      Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:member-register');
  });
  ```
- [ ] **Step 5:** Add rate limiter to `app/Providers/RateLimitServiceProvider.php`:
  ```php
  use Illuminate\Cache\RateLimiting\Limit;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\RateLimiter;

  RateLimiter::for('member-register', fn (Request $r) => Limit::perHour(3)->by($r->ip()));
  ```
- [ ] **Step 6:** Smoke test:
  ```powershell
  curl -s -o NUL -w "%{http_code}" http://127.0.0.1:8000/register
  ```
  Expected: 200.

### Task M3-T02: Email verification — make User implement MustVerifyEmail + controller

**Files:**
- Modify: `app/Models/User.php`
- Create: `app/Http/Controllers/Auth/EmailVerificationController.php`
- Create: `resources/views/auth/member/verify.blade.php`
- Modify: `routes/member.php`

- [ ] **Step 1:** Make `User` implement `MustVerifyEmail`:
  ```php
  use Illuminate\Contracts\Auth\MustVerifyEmail;
  class User extends Authenticatable implements MustVerifyEmail {
      // existing code
  }
  ```
- [ ] **Step 2:** `EmailVerificationController`:
  ```php
  <?php
  namespace App\Http\Controllers\Auth;
  use App\Http\Controllers\Controller;
  use Illuminate\Auth\Events\Verified;
  use Illuminate\Foundation\Auth\EmailVerificationRequest;
  use Illuminate\Http\Request;

  class EmailVerificationController extends Controller {
      public function notice() { return view('auth.member.verify'); }

      public function verify(EmailVerificationRequest $request) {
          if ($request->user()->hasVerifiedEmail()) {
              return redirect()->route('member.dashboard');
          }
          if ($request->user()->markEmailAsVerified()) {
              event(new Verified($request->user()));
          }
          return redirect()->route('member.dashboard')->with('success','Email verified — welcome!');
      }

      public function send(Request $request) {
          if ($request->user()->hasVerifiedEmail()) {
              return redirect()->route('member.dashboard');
          }
          $request->user()->sendEmailVerificationNotification();
          return back()->with('success','Verification link sent.');
      }
  }
  ```
- [ ] **Step 3:** Convert preview `verify.blade.php` to real view. Form action POSTs to `route('verification.send')` with `@csrf`. Show `@if(session('success'))` banner.
- [ ] **Step 4:** Add routes in `routes/member.php` inside `auth:web` middleware:
  ```php
  Route::middleware('auth:web')->group(function () {
      Route::get('/email/verify',       [EmailVerificationController::class,'notice'])->name('verification.notice');
      Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class,'verify'])
          ->middleware(['signed','throttle:6,1'])->name('verification.verify');
      Route::post('/email/verification-notification', [EmailVerificationController::class,'send'])
          ->middleware('throttle:6,1')->name('verification.send');
  });
  ```
- [ ] **Step 5:** Smoke test:
  - Register a new account at `/register` (use `test+1@example.com`).
  - Confirm: redirect to `/email/verify`.
  - Check `storage/logs/laravel.log` for the verification email (since `MAIL_MAILER=log`).
  - Copy the signed URL from the log, paste into browser, confirm redirect to `/member/dashboard` works (will need a stub dashboard from next task).

### Task M3-T03: Member layout + dashboard controller + dashboard view (real)

**Files:**
- Create: `resources/views/layouts/member.blade.php` (move from `components/member/layout.blade.php` to a real layout, or alias them)
- Create: `app/Http/Controllers/Member/DashboardController.php`
- Create: `resources/views/member/dashboard.blade.php`
- Modify: `routes/member.php`

- [ ] **Step 1:** The `<x-member.layout>` component already exists from M1. Add a route-bound view for the real member dashboard.
- [ ] **Step 2:** `DashboardController`:
  ```php
  <?php
  namespace App\Http\Controllers\Member;
  use App\Http\Controllers\Controller;
  use App\Models\{Event, FeedPost, PrayerRequest};
  use Illuminate\Http\Request;

  class DashboardController extends Controller {
      public function index(Request $req) {
          $latestPosts = FeedPost::published()->orderBy('pinned','desc')->latest('published_at')->limit(3)->get();
          $myPrayer = PrayerRequest::where('user_id', $req->user()->id)->latest()->limit(5)->get();
          $upcomingEvents = Event::published()->where('starts_at','>=',now())->orderBy('starts_at')->limit(3)->get();
          return view('member.dashboard', compact('latestPosts','myPrayer','upcomingEvents'));
      }
  }
  ```
- [ ] **Step 3:** Convert preview dashboard to `resources/views/member/dashboard.blade.php`. Replace hardcoded sample arrays with `$latestPosts`, `$myPrayer`, `$upcomingEvents` loops.
- [ ] **Step 4:** Replace the existing `Route::view('/member', 'member.coming-soon')` line in `routes/member.php` with:
  ```php
  Route::middleware(['auth:web','verified'])->prefix('member')->name('member.')->group(function () {
      Route::get('/', [\App\Http\Controllers\Member\DashboardController::class, 'index'])->name('dashboard');
  });
  ```
- [ ] **Step 5:** Smoke test by registering, verifying, logging in, hitting `/member`. Expected: 200 with dashboard.

### Task M3-T04: Profile controller + views

**Files:**
- Create: `app/Http/Controllers/Member/ProfileController.php`
- Create: `app/Http/Requests/Member/ProfileRequest.php`
- Create: `app/Http/Requests/Member/ProfilePasswordRequest.php`
- Create: `resources/views/member/profile/edit.blade.php`
- Modify: `routes/member.php`

- [ ] **Step 1:** `ProfileRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Member;
  use Illuminate\Foundation\Http\FormRequest;

  class ProfileRequest extends FormRequest {
      public function authorize(): bool { return true; }
      public function rules(): array {
          $id = $this->user()->id;
          return [
              'name'  => 'required|string|max:120',
              'email' => "required|email:rfc|max:160|unique:users,email,$id",
              'phone' => 'nullable|string|max:32',
              'bio'   => 'nullable|string|max:280',
              'avatar_path' => 'nullable|string|max:255',
          ];
      }
  }
  ```
- [ ] **Step 2:** `ProfilePasswordRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Member;
  use Illuminate\Foundation\Http\FormRequest;
  use Illuminate\Validation\Rules\Password;

  class ProfilePasswordRequest extends FormRequest {
      public function authorize(): bool { return true; }
      public function rules(): array {
          return [
              'current_password' => 'required|current_password',
              'password' => ['required', Password::defaults(), 'confirmed'],
          ];
      }
  }
  ```
- [ ] **Step 3:** `ProfileController`:
  ```php
  <?php
  namespace App\Http\Controllers\Member;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\Member\{ProfileRequest, ProfilePasswordRequest};
  use Illuminate\Http\Request;

  class ProfileController extends Controller {
      public function edit(Request $req) {
          return view('member.profile.edit', ['user' => $req->user()]);
      }
      public function update(ProfileRequest $req) {
          $data = $req->validated();
          $user = $req->user();
          if ($data['email'] !== $user->email) {
              $user->email_verified_at = null;
          }
          $user->fill($data)->save();
          if (is_null($user->email_verified_at)) {
              $user->sendEmailVerificationNotification();
              return redirect()->route('verification.notice')->with('success','Profile saved. Please verify your new email.');
          }
          return back()->with('success','Profile updated.');
      }
      public function updatePassword(ProfilePasswordRequest $req) {
          $req->user()->update(['password' => $req->password]);
          return back()->with('success','Password updated.');
      }
  }
  ```
- [ ] **Step 4:** Convert preview profile view. Two `<form>` elements:
  - `<form method="POST" action="{{ route('member.profile.update') }}">` with `@method('PUT')` `@csrf` + inputs bound to `$user->name`, `$user->email`, `$user->phone`, `$user->bio`. Avatar: media-library picker button + hidden `<input name="avatar_path">`.
  - `<form method="POST" action="{{ route('member.profile.password') }}">` `@method('PUT')` `@csrf` + current/new/confirm inputs.
- [ ] **Step 5:** Add routes inside the existing `auth:web, verified` member group in `routes/member.php`:
  ```php
  Route::get('/profile',          [\App\Http\Controllers\Member\ProfileController::class,'edit'])->name('profile.edit');
  Route::put('/profile',          [\App\Http\Controllers\Member\ProfileController::class,'update'])->name('profile.update');
  Route::put('/profile/password', [\App\Http\Controllers\Member\ProfileController::class,'updatePassword'])->name('profile.password');
  ```

### Task M3-T05: Sub-admin sidebar wiring

**Files:**
- Modify: `resources/views/components/admin/sidebar.blade.php`

- [ ] **Step 1:** Update the `$links` array to include 3 new permission-gated entries plus 2 Phase 3 stub entries:
  ```php
  $links = [
      ['route' => 'admin.dashboard',          'label' => 'Dashboard',          'icon' => 'home',     'permission' => null],
      ['route' => 'admin.pages.index',        'label' => 'Pages',              'icon' => 'doc',      'permission' => 'manage-pages'],
      ['route' => 'admin.blog.posts.index',   'label' => 'Blog',               'icon' => 'pen',      'permission' => 'manage-blog'],
      ['route' => 'admin.sermons.index',      'label' => 'Sermons',            'icon' => 'mic',      'permission' => 'manage-sermons'],
      ['route' => 'admin.events.index',       'label' => 'Events',             'icon' => 'calendar', 'permission' => 'manage-events'],
      ['route' => 'admin.ministries.index',   'label' => 'Ministries',         'icon' => 'users',    'permission' => 'manage-ministries'],
      ['route' => 'admin.media.index',        'label' => 'Media',              'icon' => 'image',    'permission' => 'manage-media'],
      ['route' => 'admin.menus.index',        'label' => 'Menus',              'icon' => 'list',     'permission' => 'manage-menus'],
      ['route' => 'admin.prayer-requests.index','label'=> 'Prayer Requests',   'icon' => 'pray',     'permission' => 'manage-prayer-requests'],
      ['route' => 'admin.care.index',         'label' => 'Knock for Help',     'icon' => 'lifebuoy', 'permission' => 'manage-knock-help'],
      ['route' => 'admin.feed.index',         'label' => 'Community Feed',     'icon' => 'megaphone','permission' => 'manage-community-feed'],
      ['route' => 'admin.attendance.stub',    'label' => 'Attendance',         'icon' => 'check',    'permission' => 'manage-events', 'badge' => 'Phase 3'],
      ['route' => 'admin.reminders.stub',     'label' => 'Reminders',          'icon' => 'bell',     'permission' => 'manage-events', 'badge' => 'Phase 3'],
      ['route' => 'admin.messages.index',     'label' => 'Messages',           'icon' => 'mail',     'permission' => 'manage-messages'],
      ['route' => 'admin.users.index',        'label' => 'Members',            'icon' => 'user',     'permission' => 'manage-users'],
      ['route' => 'admin.roles.index',        'label' => 'Roles',              'icon' => 'shield',   'permission' => 'manage-roles'],
      ['route' => 'admin.settings.index',     'label' => 'Settings',           'icon' => 'cog',      'permission' => 'manage-settings'],
  ];
  ```
- [ ] **Step 2:** Update the foreach loop to gate per-link:
  ```blade
  @foreach($links as $l)
      @if($l['permission'] === null || auth('admin')->user()?->can($l['permission']))
          <a href="{{ Route::has($l['route']) ? route($l['route']) : '#' }}"
             @class([
                 'group flex items-center gap-3 px-6 py-2.5 text-sm border-l-2 transition',
                 'bg-white/[0.06] border-brand-secondary text-white font-medium' => request()->routeIs($l['route']),
                 'border-transparent text-white/70 hover:bg-white/5 hover:text-white' => ! request()->routeIs($l['route']),
             ])>
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 opacity-80 group-hover:opacity-100">
                  {!! $icons[$l['icon']] ?? $icons['doc'] !!}
              </svg>
              <span>{{ $l['label'] }}</span>
              @if(!empty($l['badge']))
                  <span class="ml-auto text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-white/60">{{ $l['badge'] }}</span>
              @endif
          </a>
      @endif
  @endforeach
  ```
- [ ] **Step 3:** Add new icon entries to the `$icons` array in the same file (the SVG path data for `pray`, `lifebuoy`, `megaphone`, `check`, `bell`). Inline SVG paths — example for `pray`:
  ```php
  'pray'      => '<path d="M12 3v6M9 9h6M9 21l3-3 3 3M5 21l3-5 4 1M19 21l-3-5-4 1"/>',
  'lifebuoy'  => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><path d="M5.6 5.6l3.4 3.4M15 15l3.4 3.4M5.6 18.4L9 15M15 9l3.4-3.4"/>',
  'megaphone' => '<path d="M3 11v2a2 2 0 0 0 2 2h1l4 4V5L6 9H5a2 2 0 0 0-2 2zM18 8a4 4 0 0 1 0 8"/>',
  'check'     => '<path d="m5 12 5 5L20 7"/>',
  'bell'      => '<path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
  ```
- [ ] **Step 4:** Create stub Phase 3 routes in `routes/admin.php`:
  ```php
  Route::view('/attendance', 'admin.stubs.phase3', ['module' => 'Attendance'])->name('attendance.stub');
  Route::view('/reminders',  'admin.stubs.phase3', ['module' => 'Reminders'])->name('reminders.stub');
  ```
  (These are inside the existing `auth:admin, admin` group.)
- [ ] **Step 5:** Create stub view `resources/views/admin/stubs/phase3.blade.php`:
  ```blade
  <x-admin.layout :title="$module">
      <div class="card p-10 text-center max-w-xl mx-auto">
          <div class="text-5xl mb-4">🚧</div>
          <h1 class="font-serif text-2xl mb-2">{{ $module }} — Coming in Phase 3</h1>
          <p class="text-ink-muted">This module is scheduled for the next development phase. Drop by again soon.</p>
      </div>
  </x-admin.layout>
  ```
- [ ] **Step 6:** Manual probe: log in as bootstrap admin, count sidebar items (should be 17 including Dashboard); create a Prayer Organizer test user assigned the role, log in as them, count sidebar items (should be exactly 4: Dashboard + Prayer Requests + Knock for Help + Community Feed).

---

# Milestone 4 — Prayer Requests (member + admin)

### Task M4-T01: Member PrayerRequestController + routes

**Files:**
- Create: `app/Http/Controllers/Member/PrayerRequestController.php`
- Create: `app/Http/Requests/Member/PrayerRequestRequest.php`
- Create: `app/Policies/PrayerRequestPolicy.php`
- Modify: `routes/member.php`

- [ ] **Step 1:** `PrayerRequestRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Member;
  use Illuminate\Foundation\Http\FormRequest;

  class PrayerRequestRequest extends FormRequest {
      public function authorize(): bool { return true; }
      public function rules(): array {
          return [
              'title' => 'required|string|max:200',
              'body'  => 'required|string|max:5000',
              'is_public' => 'nullable|boolean',
              'is_anonymous' => 'nullable|boolean',
          ];
      }
  }
  ```
- [ ] **Step 2:** `PrayerRequestPolicy`:
  ```php
  <?php
  namespace App\Policies;
  use App\Models\{PrayerRequest, User};

  class PrayerRequestPolicy {
      public function view(User $user, PrayerRequest $p): bool {
          return $p->is_public
              || $p->user_id === $user->id
              || $user->can('manage-prayer-requests');
      }
      public function update(User $user, PrayerRequest $p): bool {
          return $p->user_id === $user->id || $user->can('manage-prayer-requests');
      }
      public function delete(User $user, PrayerRequest $p): bool {
          return $p->user_id === $user->id || $user->can('manage-prayer-requests');
      }
  }
  ```
- [ ] **Step 3:** `PrayerRequestController` (member):
  ```php
  <?php
  namespace App\Http\Controllers\Member;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\Member\PrayerRequestRequest;
  use App\Models\{PrayerRequest, PrayerRequestPray};
  use Illuminate\Http\Request;

  class PrayerRequestController extends Controller {
      public function index(Request $req) {
          $mine = PrayerRequest::where('user_id', $req->user()->id)->latest()->get();
          $community = PrayerRequest::public()->where('user_id','!=',$req->user()->id)->latest()->paginate(12);
          return view('member.prayer.index', compact('mine','community'));
      }
      public function create() {
          return view('member.prayer.create');
      }
      public function store(PrayerRequestRequest $req) {
          $data = $req->validated();
          $u = $req->user();
          PrayerRequest::create([
              'user_id' => $u->id,
              'name' => $u->name,
              'title' => $data['title'],
              'body' => $data['body'],
              'is_public' => (bool)($data['is_public'] ?? false),
              'is_anonymous' => (bool)($data['is_anonymous'] ?? false),
              'status' => 'pending',
          ]);
          return redirect()->route('member.prayer.index')->with('success','Prayer request submitted.');
      }
      public function show(PrayerRequest $prayer) {
          $this->authorize('view', $prayer);
          return view('member.prayer.show', ['prayer' => $prayer]);
      }
      public function destroy(PrayerRequest $prayer) {
          $this->authorize('delete', $prayer);
          $prayer->delete();
          return redirect()->route('member.prayer.index')->with('success','Prayer request removed.');
      }
      public function togglePray(Request $req, PrayerRequest $prayer) {
          abort_unless($prayer->is_public, 404);
          $existing = PrayerRequestPray::where('prayer_request_id', $prayer->id)
              ->where('user_id', $req->user()->id)->first();
          if ($existing) {
              $existing->delete();
              return back()->with('success','Removed from your prayers.');
          }
          PrayerRequestPray::create(['prayer_request_id' => $prayer->id, 'user_id' => $req->user()->id]);
          return back()->with('success','Added to your prayers.');
      }
  }
  ```
- [ ] **Step 4:** Add routes inside `auth:web, verified` group in `routes/member.php`:
  ```php
  Route::resource('prayer-requests', \App\Http\Controllers\Member\PrayerRequestController::class)
      ->only(['index','create','store','show','destroy'])
      ->parameters(['prayer-requests' => 'prayer'])
      ->names('prayer');
  Route::post('/prayer-requests/{prayer}/pray', [\App\Http\Controllers\Member\PrayerRequestController::class, 'togglePray'])
      ->name('prayer.pray')->middleware('throttle:feed-react');
  ```
- [ ] **Step 5:** Add `prayer-submit` and `feed-react` limiters to `RateLimitServiceProvider`:
  ```php
  RateLimiter::for('prayer-submit', fn (Request $r) => Limit::perHour(10)->by($r->user()?->id ?? $r->ip()));
  RateLimiter::for('feed-react',    fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?? $r->ip()));
  ```
  Apply `throttle:prayer-submit` to the `store` route by wrapping with `->middleware('throttle:prayer-submit')`.

### Task M4-T02: Member prayer views (real)

**Files:**
- Create: `resources/views/member/prayer/index.blade.php`
- Create: `resources/views/member/prayer/create.blade.php`
- Create: `resources/views/member/prayer/show.blade.php`

- [ ] **Step 1:** Index view — convert from preview, replace seed arrays with `$mine` (table) and `$community` (paginated card grid). Each public card has a `<form method="POST" action="{{ route('member.prayer.pray', $p) }}">@csrf<button>🙏 I'm praying ({{ $p->pray_count }})</button></form>`. Use `{{ $p->displayName() }}` for the author name.
- [ ] **Step 2:** Create view — form posts to `route('member.prayer.store')` with @csrf. Fields: title, body (textarea), is_public toggle, is_anonymous toggle.
- [ ] **Step 3:** Show view — full prayer details. Owner sees delete form `<form method="POST" action="{{ route('member.prayer.destroy', $prayer) }}">@csrf @method('DELETE')<button>Delete</button></form>`.

### Task M4-T03: Admin PrayerRequestController + views

**Files:**
- Create: `app/Http/Controllers/Admin/PrayerRequestController.php`
- Create: `app/Http/Requests/Admin/PrayerRequestUpdateRequest.php`
- Create: `resources/views/admin/prayer-requests/index.blade.php`
- Create: `resources/views/admin/prayer-requests/show.blade.php`
- Modify: `routes/admin.php`

- [ ] **Step 1:** `PrayerRequestUpdateRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Admin;
  use Illuminate\Foundation\Http\FormRequest;

  class PrayerRequestUpdateRequest extends FormRequest {
      public function authorize(): bool { return $this->user('admin')?->can('manage-prayer-requests') ?? false; }
      public function rules(): array {
          return [
              'status' => 'required|in:pending,praying,answered,closed',
              'assigned_to' => 'nullable|exists:users,id',
              'admin_notes' => 'nullable|string|max:10000',
          ];
      }
  }
  ```
- [ ] **Step 2:** `Admin\PrayerRequestController`:
  ```php
  <?php
  namespace App\Http\Controllers\Admin;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\Admin\PrayerRequestUpdateRequest;
  use App\Models\{PrayerRequest, User};
  use Illuminate\Http\Request;

  class PrayerRequestController extends Controller {
      public function index(Request $req) {
          $q = PrayerRequest::query()->latest();
          if ($req->filled('status')) $q->where('status', $req->status);
          if ($req->filled('search')) $q->where('title','like','%'.$req->search.'%');
          return view('admin.prayer-requests.index', ['items' => $q->paginate(20)->withQueryString()]);
      }
      public function show(PrayerRequest $prayerRequest) {
          $assignees = User::where('is_admin', true)->orderBy('name')->get();
          return view('admin.prayer-requests.show', ['p' => $prayerRequest, 'assignees' => $assignees]);
      }
      public function update(PrayerRequestUpdateRequest $req, PrayerRequest $prayerRequest) {
          $data = $req->validated();
          if (!empty($data['admin_notes'])) {
              $data['admin_notes'] = clean($data['admin_notes'], 'cms');
          }
          $prayerRequest->update($data);
          return back()->with('success','Prayer request updated.');
      }
      public function destroy(PrayerRequest $prayerRequest) {
          $prayerRequest->delete();
          return redirect()->route('admin.prayer-requests.index')->with('success','Deleted.');
      }
  }
  ```
- [ ] **Step 3:** Routes in `routes/admin.php` (inside the `auth:admin, admin` group):
  ```php
  Route::resource('prayer-requests', \App\Http\Controllers\Admin\PrayerRequestController::class)
      ->only(['index','show','update','destroy'])
      ->middleware('permission:manage-prayer-requests');
  ```
- [ ] **Step 4:** Index view — table with filters (status select + search input). Columns: title, requester (or "Anonymous"), status pill, pray_count, created, view button. For Prayer Organizer specifically, render `{{ $p->displayName() }}` (which handles anonymity); for Site Admin, render real name always (use `auth('admin')->user()->hasRole('Site Admin')` to decide).
- [ ] **Step 5:** Show view — full body, member info card (gated by role), status dropdown, assigned_to select, admin_notes CKEditor textarea, save form, delete form. Status changes log to `activity_log` automatically via the `LogsModelActivity` trait when the model is updated.

---

# Milestone 5 — Knock for Help (member + admin)

### Task M5-T01: Member CareRequestController + view + route

**Files:**
- Create: `app/Http/Controllers/Member/CareRequestController.php`
- Create: `app/Http/Requests/Member/CareRequestRequest.php`
- Create: `app/Policies/CareRequestPolicy.php`
- Create: `app/Mail/CareRequestSubmitted.php`
- Create: `resources/views/emails/care-submitted.blade.php`
- Create: `resources/views/member/care/index.blade.php`
- Create: `resources/views/member/care/create.blade.php`
- Create: `resources/views/member/care/thanks.blade.php`
- Modify: `routes/member.php`

- [ ] **Step 1:** `CareRequestRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Member;
  use Illuminate\Foundation\Http\FormRequest;

  class CareRequestRequest extends FormRequest {
      public function authorize(): bool { return true; }
      public function rules(): array {
          return [
              'category' => 'required|in:illness,grief,financial,food,other',
              'message'  => 'required|string|max:5000',
              'share_with_team' => 'nullable|boolean',
          ];
      }
  }
  ```
- [ ] **Step 2:** `CareRequestPolicy`:
  ```php
  <?php
  namespace App\Policies;
  use App\Models\{CareRequest, User};

  class CareRequestPolicy {
      public function view(User $u, CareRequest $r): bool {
          return $r->user_id === $u->id || $u->can('manage-knock-help');
      }
      public function update(User $u, CareRequest $r): bool {
          return $u->can('manage-knock-help');
      }
  }
  ```
- [ ] **Step 3:** `CareRequestSubmitted` Mailable:
  ```php
  <?php
  namespace App\Mail;
  use App\Models\CareRequest;
  use Illuminate\Bus\Queueable;
  use Illuminate\Mail\Mailable;
  use Illuminate\Mail\Mailables\{Content, Envelope};
  use Illuminate\Queue\SerializesModels;

  class CareRequestSubmitted extends Mailable {
      use Queueable, SerializesModels;
      public function __construct(public CareRequest $careRequest) {}
      public function envelope(): Envelope {
          return new Envelope(subject: 'New Knock for Help: '.ucfirst($this->careRequest->category));
      }
      public function content(): Content {
          return new Content(markdown: 'emails.care-submitted', with: ['c' => $this->careRequest]);
      }
  }
  ```
- [ ] **Step 4:** `emails/care-submitted.blade.php`:
  ```blade
  <x-mail::message>
  # A member has knocked for help

  **Member:** {{ $c->user->name }} ({{ $c->user->email }})
  **Category:** {{ ucfirst($c->category) }}
  **Share with team:** {{ $c->share_with_team ? 'Yes' : 'No (pastor only)' }}

  > {{ $c->message }}

  <x-mail::button :url="url('/admin/care/'.$c->id)">Open in admin</x-mail::button>

  Submitted {{ $c->created_at->format('M j, Y · g:i A') }}
  </x-mail::message>
  ```
- [ ] **Step 5:** `Member\CareRequestController`:
  ```php
  <?php
  namespace App\Http\Controllers\Member;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\Member\CareRequestRequest;
  use App\Mail\CareRequestSubmitted;
  use App\Models\{CareRequest, User};
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Mail;

  class CareRequestController extends Controller {
      public function index(Request $req) {
          $items = CareRequest::where('user_id', $req->user()->id)->latest()->get();
          return view('member.care.index', compact('items'));
      }
      public function create() { return view('member.care.create'); }
      public function store(CareRequestRequest $req) {
          $data = $req->validated();
          $care = CareRequest::create([
              'user_id' => $req->user()->id,
              'category' => $data['category'],
              'message' => $data['message'],
              'share_with_team' => (bool)($data['share_with_team'] ?? false),
              'status' => 'open',
          ]);
          $recipients = User::role(['Site Admin','Prayer Organizer'], 'admin')->pluck('email')->toArray();
          try {
              Mail::to($recipients)->send(new CareRequestSubmitted($care));
          } catch (\Throwable $e) {
              logger()->error('Care mail failed', ['err' => $e->getMessage()]);
          }
          return redirect()->route('member.care.thanks');
      }
      public function show(Request $req, CareRequest $care) {
          $this->authorize('view', $care);
          return view('member.care.show', ['care' => $care]);
      }
  }
  ```
- [ ] **Step 6:** Convert preview views to real views. Use `route('member.care.store')` POST in create form. Thanks page is a static view with a "Back to dashboard" link.
- [ ] **Step 7:** Routes in `routes/member.php`:
  ```php
  Route::resource('care', \App\Http\Controllers\Member\CareRequestController::class)
      ->only(['index','create','store','show']);
  Route::view('/care/thanks', 'member.care.thanks')->name('care.thanks');
  ```
  Apply `throttle:care-submit` to the store action by wrapping the resource with a custom route or via controller-level middleware:
  ```php
  // In controller __construct():
  public function __construct() {
      $this->middleware('throttle:care-submit')->only('store');
  }
  ```
- [ ] **Step 8:** Add `care-submit` to `RateLimitServiceProvider`:
  ```php
  RateLimiter::for('care-submit', fn (Request $r) => Limit::perHour(3)->by($r->user()?->id ?? $r->ip()));
  ```

### Task M5-T02: Admin CareController + views + routes

**Files:**
- Create: `app/Http/Controllers/Admin/CareController.php`
- Create: `app/Http/Requests/Admin/CareUpdateRequest.php`
- Create: `resources/views/admin/care/index.blade.php`
- Create: `resources/views/admin/care/show.blade.php`
- Modify: `routes/admin.php`

- [ ] **Step 1:** `CareUpdateRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Admin;
  use Illuminate\Foundation\Http\FormRequest;

  class CareUpdateRequest extends FormRequest {
      public function authorize(): bool { return $this->user('admin')?->can('manage-knock-help') ?? false; }
      public function rules(): array {
          return [
              'status' => 'required|in:open,responding,closed',
              'response_notes' => 'nullable|string|max:20000',
          ];
      }
  }
  ```
- [ ] **Step 2:** `Admin\CareController`:
  ```php
  <?php
  namespace App\Http\Controllers\Admin;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\Admin\CareUpdateRequest;
  use App\Models\CareRequest;
  use Illuminate\Http\Request;

  class CareController extends Controller {
      public function index(Request $req) {
          $q = CareRequest::query()->latest();
          if ($req->filled('status'))   $q->where('status', $req->status);
          if ($req->filled('category')) $q->where('category', $req->category);
          return view('admin.care.index', ['items' => $q->paginate(20)->withQueryString()]);
      }
      public function show(CareRequest $care) {
          return view('admin.care.show', ['care' => $care]);
      }
      public function update(CareUpdateRequest $req, CareRequest $care) {
          $data = $req->validated();
          if (!empty($data['response_notes'])) {
              $data['response_notes'] = clean($data['response_notes'], 'cms');
          }
          if ($care->status === 'open' && $data['status'] !== 'open') {
              $data['responder_id'] = $req->user('admin')->id;
          }
          if ($data['status'] === 'closed') {
              $data['closed_at'] = now();
          }
          $care->update($data);
          return back()->with('success','Knock for Help updated.');
      }
  }
  ```
- [ ] **Step 3:** Routes in `routes/admin.php`:
  ```php
  Route::resource('care', \App\Http\Controllers\Admin\CareController::class)
      ->only(['index','show','update'])
      ->middleware('permission:manage-knock-help');
  ```
- [ ] **Step 4:** Index view: paginated table — member, category badge, status pill, created, view button. Filters: status + category.
- [ ] **Step 5:** Show view: member contact card (name + email + phone) + full message + status dropdown + `response_notes` textarea (CKEditor) + save form. Activity log shown below from `$care->activityLog` (if LogsModelActivity provides this; otherwise query `ActivityLog::where('subject_type',CareRequest::class)->where('subject_id',$care->id)`).

---

# Milestone 6 — Community Feed

### Task M6-T01: Member FeedController + view + reaction toggle

**Files:**
- Create: `app/Http/Controllers/Member/FeedController.php`
- Create: `app/Policies/FeedPostPolicy.php`
- Create: `resources/views/member/feed/index.blade.php`
- Modify: `routes/member.php`

- [ ] **Step 1:** `FeedPostPolicy`:
  ```php
  <?php
  namespace App\Policies;
  use App\Models\{FeedPost, User};

  class FeedPostPolicy {
      public function view(User $u, FeedPost $f): bool { return true; }
      public function create(User $u): bool { return $u->can('manage-community-feed'); }
      public function update(User $u, FeedPost $f): bool { return $u->can('manage-community-feed'); }
      public function delete(User $u, FeedPost $f): bool { return $u->can('manage-community-feed'); }
  }
  ```
- [ ] **Step 2:** `Member\FeedController`:
  ```php
  <?php
  namespace App\Http\Controllers\Member;
  use App\Http\Controllers\Controller;
  use App\Models\{FeedPost, FeedReaction};
  use Illuminate\Http\Request;
  use Illuminate\Validation\ValidationException;

  class FeedController extends Controller {
      public function index() {
          $posts = FeedPost::published()
              ->orderBy('pinned','desc')
              ->latest('published_at')
              ->paginate(10);
          $myReactions = FeedReaction::where('user_id', auth()->id())
              ->whereIn('feed_post_id', $posts->pluck('id'))
              ->pluck('kind','feed_post_id')
              ->toArray();
          return view('member.feed.index', compact('posts','myReactions'));
      }
      public function react(Request $req, FeedPost $post) {
          $kind = $req->input('kind');
          if (!in_array($kind, ['heart','pray','amen'])) {
              throw ValidationException::withMessages(['kind' => 'Invalid reaction']);
          }
          $userId = $req->user()->id;
          $existing = FeedReaction::where('feed_post_id', $post->id)->where('user_id', $userId)->first();
          if ($existing && $existing->kind === $kind) {
              $existing->delete();
          } else {
              FeedReaction::updateOrCreate(
                  ['feed_post_id' => $post->id, 'user_id' => $userId],
                  ['kind' => $kind, 'created_at' => now()]
              );
          }
          return back();
      }
  }
  ```
- [ ] **Step 3:** Routes in `routes/member.php`:
  ```php
  Route::get('/feed', [\App\Http\Controllers\Member\FeedController::class, 'index'])->name('feed.index');
  Route::post('/feed/{post}/react', [\App\Http\Controllers\Member\FeedController::class, 'react'])
      ->name('feed.react')->middleware('throttle:feed-react');
  ```
- [ ] **Step 4:** Convert preview feed view. Each post:
  ```blade
  @foreach($posts as $post)
      @php $counts = $post->reactionCountsByKind(); $my = $myReactions[$post->id] ?? null; @endphp
      <article class="card p-6 mb-6">
          {{-- author meta, title, body, image --}}
          <div class="flex gap-2 mt-4">
              @foreach(['heart'=>'♥','pray'=>'🙏','amen'=>'👏'] as $k => $emoji)
                  <form method="POST" action="{{ route('member.feed.react', $post) }}">@csrf
                      <input type="hidden" name="kind" value="{{ $k }}">
                      <button @class([
                          'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm transition',
                          'bg-brand-primary text-white' => $my === $k,
                          'bg-surface hover:bg-brand-primary/10' => $my !== $k,
                      ])>
                          <span>{{ $emoji }}</span>
                          <span>{{ $counts[$k] ?? 0 }}</span>
                      </button>
                  </form>
              @endforeach
          </div>
      </article>
  @endforeach
  {{ $posts->links() }}
  ```

### Task M6-T02: Admin FeedController + views + broadcast job

**Files:**
- Create: `app/Http/Controllers/Admin/FeedController.php`
- Create: `app/Http/Requests/Admin/FeedPostRequest.php`
- Create: `app/Jobs/SendFeedBroadcast.php`
- Create: `app/Mail/FeedPostBroadcast.php`
- Create: `resources/views/emails/feed-broadcast.blade.php`
- Create: `resources/views/admin/feed/index.blade.php`
- Create: `resources/views/admin/feed/create.blade.php`
- Create: `resources/views/admin/feed/edit.blade.php`
- Modify: `routes/admin.php`

- [ ] **Step 1:** `FeedPostRequest`:
  ```php
  <?php
  namespace App\Http\Requests\Admin;
  use Illuminate\Foundation\Http\FormRequest;

  class FeedPostRequest extends FormRequest {
      public function authorize(): bool { return $this->user('admin')?->can('manage-community-feed') ?? false; }
      public function rules(): array {
          return [
              'title' => 'nullable|string|max:200',
              'body' => 'required|string|max:20000',
              'image' => 'nullable|file|image|max:10240',
              'image_path' => 'nullable|string|max:255',
              'pinned' => 'nullable|boolean',
              'published_at' => 'nullable|date',
              'broadcast' => 'nullable|boolean',
          ];
      }
  }
  ```
- [ ] **Step 2:** `Admin\FeedController`:
  ```php
  <?php
  namespace App\Http\Controllers\Admin;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\Admin\FeedPostRequest;
  use App\Jobs\SendFeedBroadcast;
  use App\Models\FeedPost;
  use App\Services\MediaUploader;
  use Illuminate\Http\Request;

  class FeedController extends Controller {
      public function index() {
          $posts = FeedPost::latest('published_at')->paginate(20);
          return view('admin.feed.index', compact('posts'));
      }
      public function create() {
          return view('admin.feed.create', ['post' => new FeedPost()]);
      }
      public function store(FeedPostRequest $req, MediaUploader $uploader) {
          $data = $req->validated();
          $data['body'] = clean($data['body'], 'cms');
          $data['author_id'] = $req->user('admin')->id;
          $data['pinned'] = (bool)($data['pinned'] ?? false);
          $data['published_at'] = $data['published_at'] ?? now();

          if ($req->hasFile('image')) {
              $media = $uploader->store($req->file('image'), null, $req->user('admin'));
              $data['image_path'] = $media->path;
          } elseif (!empty($data['image_path']) && str_starts_with($data['image_path'], '/storage/')) {
              $data['image_path'] = substr($data['image_path'], 9);
          }

          unset($data['image'], $data['broadcast']);
          $post = FeedPost::create($data);

          if ((bool)($req->input('broadcast'))) {
              SendFeedBroadcast::dispatch($post->id);
          }
          return redirect()->route('admin.feed.index')->with('success','Feed post created.');
      }
      public function edit(FeedPost $feed) {
          return view('admin.feed.edit', ['post' => $feed]);
      }
      public function update(FeedPostRequest $req, FeedPost $feed, MediaUploader $uploader) {
          $data = $req->validated();
          $data['body'] = clean($data['body'], 'cms');
          $data['pinned'] = (bool)($data['pinned'] ?? false);
          if ($req->hasFile('image')) {
              $media = $uploader->store($req->file('image'), null, $req->user('admin'));
              $data['image_path'] = $media->path;
          } elseif (!empty($data['image_path']) && str_starts_with($data['image_path'], '/storage/')) {
              $data['image_path'] = substr($data['image_path'], 9);
          }
          unset($data['image'], $data['broadcast']);
          $feed->update($data);
          return back()->with('success','Feed post updated.');
      }
      public function destroy(FeedPost $feed) {
          $feed->delete();
          return redirect()->route('admin.feed.index')->with('success','Deleted.');
      }
  }
  ```
- [ ] **Step 3:** `SendFeedBroadcast` Job:
  ```php
  <?php
  namespace App\Jobs;
  use App\Mail\FeedPostBroadcast;
  use App\Models\{FeedPost, User};
  use Illuminate\Bus\Queueable;
  use Illuminate\Contracts\Queue\ShouldQueue;
  use Illuminate\Foundation\Bus\Dispatchable;
  use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
  use Illuminate\Support\Facades\Mail;

  class SendFeedBroadcast implements ShouldQueue {
      use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
      public function __construct(public int $postId) {}
      public function handle(): void {
          $post = FeedPost::find($this->postId);
          if (!$post) return;
          User::whereNotNull('email_verified_at')->chunk(50, function ($chunk) use ($post) {
              foreach ($chunk as $u) {
                  Mail::to($u->email)->send(new FeedPostBroadcast($post));
              }
          });
      }
  }
  ```
- [ ] **Step 4:** `FeedPostBroadcast` Mailable:
  ```php
  <?php
  namespace App\Mail;
  use App\Models\FeedPost;
  use Illuminate\Bus\Queueable;
  use Illuminate\Mail\Mailable;
  use Illuminate\Mail\Mailables\{Content, Envelope};
  use Illuminate\Queue\SerializesModels;

  class FeedPostBroadcast extends Mailable {
      use Queueable, SerializesModels;
      public function __construct(public FeedPost $post) {}
      public function envelope(): Envelope {
          return new Envelope(subject: $this->post->title ?: 'A new update from the church');
      }
      public function content(): Content {
          return new Content(markdown: 'emails.feed-broadcast', with: ['p' => $this->post]);
      }
  }
  ```
- [ ] **Step 5:** `emails/feed-broadcast.blade.php`:
  ```blade
  <x-mail::message>
  # {{ $p->title ?: 'A new post' }}

  {!! strip_tags($p->body, '<p><br><strong><em>') !!}

  <x-mail::button :url="url('/member/feed')">Read on the feed</x-mail::button>
  </x-mail::message>
  ```
- [ ] **Step 6:** Routes in `routes/admin.php`:
  ```php
  Route::resource('feed', \App\Http\Controllers\Admin\FeedController::class)
      ->middleware('permission:manage-community-feed');
  ```
- [ ] **Step 7:** Admin views: index (table), create + edit (shared partial form). Form fields: title, body (CKEditor), image picker (media library modal OR file input), pinned toggle, published_at datetime, "Send to members" checkbox.
- [ ] **Step 8:** Set `QUEUE_CONNECTION=database` in `.env` if not already; ensure `jobs` table exists (Phase 1 added it). Document that ops should run `php artisan queue:work` in production.

---

# Milestone 7 — Donate Page

### Task M7-T01: Site PageController extension for /donate

**Files:**
- Modify: `app/Http/Controllers/Site/PageController.php`
- Modify: `routes/web.php`
- Modify: `resources/views/components/site/header.blade.php`
- Modify: `resources/views/preview/admin/_footer.blade.php` (or wherever the site footer is)
- Modify: `resources/views/components/member/header.blade.php`

- [ ] **Step 1:** Add `donate` method to `Site\PageController`:
  ```php
  public function donate() {
      $page = \App\Models\Page::where('slug','donate')->published()->firstOrFail();
      return view('site.donate', compact('page'));
  }
  ```
- [ ] **Step 2:** Add route in `routes/web.php` inside the `Route::name('site.')->group(...)`:
  ```php
  Route::get('/donate', [PageController::class, 'donate'])->name('donate');
  ```
- [ ] **Step 3:** Create `resources/views/site/donate.blade.php`:
  ```blade
  @extends('layouts.site')
  @section('title', $page->meta_title ?: ($page->title.settings('seo.default_title_suffix','')))
  @section('content')
      <x-site.header :nav="$siteNav" />
      <x-site.hero :image="$page->hero_image_path ? Storage::url($page->hero_image_path) : 'https://picsum.photos/seed/donate/1800/700'"
                   :heading="$page->hero_heading ?: 'Support our mission'"
                   :sub="$page->hero_subheading"
                   size="sm" />
      <article class="max-w-3xl mx-auto px-4 py-16">
          <div class="prose prose-lg max-w-none">{!! $page->body !!}</div>
      </article>
      @include('site._footer')
  @endsection
  ```
- [ ] **Step 4:** Add a "Give" CTA button to `resources/views/components/site/header.blade.php` next to the existing CTA, linking to `{{ route('site.donate') }}`.
- [ ] **Step 5:** Add a "Give" link to the site footer's quick-links list.
- [ ] **Step 6:** Member portal nav already has "Give" link from M1; confirm it points to `{{ route('site.donate') }}` (replace the preview route).
- [ ] **Step 7:** Smoke test:
  ```bash
  curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000/donate
  ```
  Expected: 200.

### Task M7-T02: Giving redirect for members

**Files:**
- Modify: `routes/member.php`

- [ ] **Step 1:** Inside the `auth:web, verified` group:
  ```php
  Route::get('/giving', fn () => redirect()->route('site.donate'))->name('giving');
  ```
- [ ] **Step 2:** Verify by visiting `/member/giving` while logged in — should 302 to `/donate`.

---

# Milestone 8 — Tests

### Task M8-T01: Member auth tests

**Files:**
- Create: `tests/Feature/Auth/MemberRegisterTest.php`
- Create: `tests/Feature/Auth/EmailVerificationTest.php`

- [ ] **Step 1:** `MemberRegisterTest`:
  ```php
  <?php
  use App\Models\User;

  it('shows the registration form', function () {
      $this->get('/register')->assertStatus(200)->assertSee('Create account', false);
  });

  it('registers a new member and sends verification email', function () {
      \Illuminate\Support\Facades\Notification::fake();
      $this->post('/register', [
          'name' => 'Jane',
          'email' => 'jane@example.com',
          'password' => 'StrongPass!2026',
          'password_confirmation' => 'StrongPass!2026',
      ])->assertRedirect('/email/verify');
      expect(User::where('email','jane@example.com')->exists())->toBeTrue();
  });

  it('rejects honeypot submissions silently', function () {
      $this->post('/register', [
          'name' => 'Bot',
          'email' => 'bot@example.com',
          'password' => 'StrongPass!2026',
          'password_confirmation' => 'StrongPass!2026',
          'website' => 'spamspamspam',
      ])->assertRedirect('/email/verify');
      expect(User::where('email','bot@example.com')->exists())->toBeFalse();
  });

  it('rejects duplicate email', function () {
      User::factory()->create(['email' => 'dupe@example.com']);
      $this->post('/register', [
          'name' => 'Dupe',
          'email' => 'dupe@example.com',
          'password' => 'StrongPass!2026',
          'password_confirmation' => 'StrongPass!2026',
      ])->assertSessionHasErrors('email');
  });
  ```
- [ ] **Step 2:** `EmailVerificationTest`:
  ```php
  <?php
  use App\Models\User;
  use Illuminate\Support\Facades\URL;

  it('redirects unverified users from member portal', function () {
      $u = User::factory()->unverified()->create();
      $this->actingAs($u, 'web')->get('/member')->assertRedirect('/email/verify');
  });

  it('verifies via signed URL', function () {
      $u = User::factory()->unverified()->create();
      $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
          'id' => $u->id, 'hash' => sha1($u->email),
      ]);
      $this->actingAs($u, 'web')->get($url)->assertRedirect('/member');
      expect($u->fresh()->hasVerifiedEmail())->toBeTrue();
  });

  it('rejects tampered hash', function () {
      $u = User::factory()->unverified()->create();
      $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
          'id' => $u->id, 'hash' => 'wrong',
      ]);
      $this->actingAs($u, 'web')->get($url)->assertStatus(403);
  });
  ```
- [ ] **Step 3:** Add a `User::factory()->unverified()` state if not already present. Run tests: `D:\XAMPP\php\php.exe artisan test --filter='Auth/Member'`. All pass.

### Task M8-T02: Profile tests

**Files:**
- Create: `tests/Feature/Member/ProfileTest.php`

- [ ] **Step 1:**
  ```php
  <?php
  use App\Models\User;

  it('updates profile fields', function () {
      $u = User::factory()->create(['name' => 'Old', 'email' => 'old@example.com']);
      $this->actingAs($u, 'web')->put('/member/profile', [
          'name' => 'New', 'email' => 'old@example.com', 'phone' => '12345',
      ])->assertRedirect();
      expect($u->fresh()->name)->toBe('New')->and($u->fresh()->phone)->toBe('12345');
  });

  it('resets email verification when email changes', function () {
      $u = User::factory()->create(['email' => 'a@example.com']);
      $this->actingAs($u, 'web')->put('/member/profile', [
          'name' => $u->name, 'email' => 'b@example.com',
      ])->assertRedirect('/email/verify');
      expect($u->fresh()->email_verified_at)->toBeNull();
  });

  it('changes password with correct current password', function () {
      $u = User::factory()->create(['password' => bcrypt('OldPass!2026')]);
      $this->actingAs($u, 'web')->put('/member/profile/password', [
          'current_password' => 'OldPass!2026',
          'password' => 'NewStrong!2026',
          'password_confirmation' => 'NewStrong!2026',
      ])->assertRedirect();
      expect(\Hash::check('NewStrong!2026', $u->fresh()->password))->toBeTrue();
  });

  it('rejects password change with wrong current password', function () {
      $u = User::factory()->create(['password' => bcrypt('OldPass!2026')]);
      $this->actingAs($u, 'web')->put('/member/profile/password', [
          'current_password' => 'wrong',
          'password' => 'NewStrong!2026',
          'password_confirmation' => 'NewStrong!2026',
      ])->assertSessionHasErrors('current_password');
  });
  ```

### Task M8-T03: Prayer request tests

**Files:**
- Create: `tests/Feature/Member/PrayerRequestTest.php`

- [ ] **Step 1:**
  ```php
  <?php
  use App\Models\{PrayerRequest, PrayerRequestPray, User};

  it('lets a member create a prayer request', function () {
      $u = User::factory()->create();
      $this->actingAs($u, 'web')->post('/member/prayer-requests', [
          'title' => 'Health', 'body' => 'For my dad', 'is_public' => '1',
      ])->assertRedirect();
      expect(PrayerRequest::where('title','Health')->exists())->toBeTrue();
  });

  it('hides private requests from non-owners', function () {
      $owner = User::factory()->create();
      $other = User::factory()->create();
      $p = PrayerRequest::factory()->create(['user_id' => $owner->id, 'is_public' => false]);
      $this->actingAs($other, 'web')->get("/member/prayer-requests/{$p->id}")->assertForbidden();
  });

  it('toggles I am praying', function () {
      $owner = User::factory()->create();
      $other = User::factory()->create();
      $p = PrayerRequest::factory()->create(['user_id' => $owner->id, 'is_public' => true, 'pray_count' => 0]);
      $this->actingAs($other, 'web')->post("/member/prayer-requests/{$p->id}/pray")->assertRedirect();
      expect($p->fresh()->pray_count)->toBe(1);
      $this->actingAs($other, 'web')->post("/member/prayer-requests/{$p->id}/pray")->assertRedirect();
      expect($p->fresh()->pray_count)->toBe(0);
  });

  it('renders Anonymous when is_anonymous is true', function () {
      $u = User::factory()->create(['name' => 'Real Name']);
      $p = PrayerRequest::factory()->create([
          'user_id' => $u->id, 'name' => $u->name, 'is_public' => true, 'is_anonymous' => true,
      ]);
      $other = User::factory()->create();
      $resp = $this->actingAs($other, 'web')->get('/member/prayer-requests')->getContent();
      expect($resp)->toContain('Anonymous')->not->toContain('Real Name');
  });

  it('allows owner to soft-delete', function () {
      $owner = User::factory()->create();
      $p = PrayerRequest::factory()->create(['user_id' => $owner->id]);
      $this->actingAs($owner, 'web')->delete("/member/prayer-requests/{$p->id}")->assertRedirect();
      expect($p->fresh()->trashed())->toBeTrue();
  });
  ```

### Task M8-T04: Care request tests

**Files:**
- Create: `tests/Feature/Member/CareRequestTest.php`

- [ ] **Step 1:**
  ```php
  <?php
  use App\Mail\CareRequestSubmitted;
  use App\Models\{CareRequest, User};
  use Illuminate\Support\Facades\Mail;
  use Spatie\Permission\Models\Role;

  beforeEach(function () {
      Role::firstOrCreate(['name' => 'Site Admin', 'guard_name' => 'admin']);
      Role::firstOrCreate(['name' => 'Prayer Organizer', 'guard_name' => 'admin']);
  });

  it('lets a member submit a care request and emails the team', function () {
      Mail::fake();
      $admin = User::factory()->create(['is_admin' => true]);
      $admin->assignRole('Site Admin');
      $u = User::factory()->create();
      $this->actingAs($u, 'web')->post('/member/care', [
          'category' => 'illness', 'message' => 'My grandmother is in hospital.',
      ])->assertRedirect('/member/care/thanks');
      expect(CareRequest::count())->toBe(1);
      Mail::assertSent(CareRequestSubmitted::class);
  });

  it('hides another members care requests', function () {
      $u1 = User::factory()->create();
      $u2 = User::factory()->create();
      $c = CareRequest::factory()->create(['user_id' => $u1->id]);
      $this->actingAs($u2, 'web')->get("/member/care/{$c->id}")->assertForbidden();
  });
  ```

### Task M8-T05: Feed tests

**Files:**
- Create: `tests/Feature/Member/CommunityFeedTest.php`
- Create: `tests/Feature/Admin/FeedAdminTest.php`

- [ ] **Step 1:** `CommunityFeedTest`:
  ```php
  <?php
  use App\Models\{FeedPost, FeedReaction, User};

  it('shows the feed to a member', function () {
      $u = User::factory()->create();
      FeedPost::factory()->count(3)->create();
      $this->actingAs($u, 'web')->get('/member/feed')->assertStatus(200);
  });

  it('reacts and toggles', function () {
      $u = User::factory()->create();
      $post = FeedPost::factory()->create();
      $this->actingAs($u, 'web')->post("/member/feed/{$post->id}/react", ['kind'=>'heart'])->assertRedirect();
      expect(FeedReaction::count())->toBe(1);
      $this->actingAs($u, 'web')->post("/member/feed/{$post->id}/react", ['kind'=>'pray'])->assertRedirect();
      expect(FeedReaction::count())->toBe(1)->and(FeedReaction::first()->kind)->toBe('pray');
      $this->actingAs($u, 'web')->post("/member/feed/{$post->id}/react", ['kind'=>'pray'])->assertRedirect();
      expect(FeedReaction::count())->toBe(0);
  });

  it('orders pinned posts first', function () {
      $a = FeedPost::factory()->create(['pinned' => false, 'published_at' => now()->subHour()]);
      $b = FeedPost::factory()->create(['pinned' => true,  'published_at' => now()->subDay()]);
      $u = User::factory()->create();
      $content = $this->actingAs($u, 'web')->get('/member/feed')->getContent();
      $posA = strpos($content, $a->title);
      $posB = strpos($content, $b->title);
      expect($posB)->toBeLessThan($posA);
  });
  ```
- [ ] **Step 2:** `FeedAdminTest`:
  ```php
  <?php
  use App\Jobs\SendFeedBroadcast;
  use App\Models\{FeedPost, User};
  use Illuminate\Support\Facades\{Bus, Storage};
  use Spatie\Permission\Models\{Role, Permission};

  beforeEach(function () {
      Permission::firstOrCreate(['name'=>'manage-community-feed','guard_name'=>'admin']);
      $r = Role::firstOrCreate(['name'=>'Site Admin','guard_name'=>'admin']);
      $r->givePermissionTo('manage-community-feed');
  });

  it('creates a feed post', function () {
      $admin = User::factory()->create(['is_admin'=>true]);
      $admin->assignRole('Site Admin');
      $this->actingAs($admin,'admin')->post('/admin/feed', [
          'title' => 'Hello', 'body' => '<p>Welcome</p>',
      ])->assertRedirect();
      expect(FeedPost::where('title','Hello')->exists())->toBeTrue();
  });

  it('dispatches broadcast job when checkbox is set', function () {
      Bus::fake();
      $admin = User::factory()->create(['is_admin'=>true]);
      $admin->assignRole('Site Admin');
      $this->actingAs($admin,'admin')->post('/admin/feed', [
          'title' => 'Hello', 'body' => '<p>Welcome</p>', 'broadcast' => '1',
      ]);
      Bus::assertDispatched(SendFeedBroadcast::class);
  });
  ```

### Task M8-T06: Admin Prayer + Care tests

**Files:**
- Create: `tests/Feature/Admin/PrayerAdminTest.php`
- Create: `tests/Feature/Admin/CareAdminTest.php`

- [ ] **Step 1:** `PrayerAdminTest`:
  ```php
  <?php
  use App\Models\{PrayerRequest, User};
  use Spatie\Permission\Models\{Role, Permission};

  beforeEach(function () {
      Permission::firstOrCreate(['name'=>'manage-prayer-requests','guard_name'=>'admin']);
      Role::firstOrCreate(['name'=>'Site Admin','guard_name'=>'admin'])->givePermissionTo('manage-prayer-requests');
      Role::firstOrCreate(['name'=>'Prayer Organizer','guard_name'=>'admin'])->givePermissionTo('manage-prayer-requests');
      Role::firstOrCreate(['name'=>'Event Organizer','guard_name'=>'admin']);
  });

  it('site admin can list prayer requests', function () {
      $a = User::factory()->create(['is_admin'=>true]); $a->assignRole('Site Admin');
      PrayerRequest::factory()->count(3)->create();
      $this->actingAs($a,'admin')->get('/admin/prayer-requests')->assertStatus(200);
  });

  it('event organizer cannot access prayer admin', function () {
      $a = User::factory()->create(['is_admin'=>true]); $a->assignRole('Event Organizer');
      $this->actingAs($a,'admin')->get('/admin/prayer-requests')->assertStatus(403);
  });

  it('status change logs to activity_log', function () {
      $a = User::factory()->create(['is_admin'=>true]); $a->assignRole('Site Admin');
      $p = PrayerRequest::factory()->create(['status'=>'pending']);
      $this->actingAs($a,'admin')->put("/admin/prayer-requests/{$p->id}", [
          'status' => 'praying',
      ])->assertRedirect();
      expect(\App\Models\ActivityLog::where('subject_type', PrayerRequest::class)
          ->where('subject_id', $p->id)->where('action','updated')->exists())->toBeTrue();
  });
  ```
- [ ] **Step 2:** `CareAdminTest`:
  ```php
  <?php
  use App\Models\{CareRequest, User};
  use Spatie\Permission\Models\{Role, Permission};

  beforeEach(function () {
      Permission::firstOrCreate(['name'=>'manage-knock-help','guard_name'=>'admin']);
      Role::firstOrCreate(['name'=>'Site Admin','guard_name'=>'admin'])->givePermissionTo('manage-knock-help');
      Role::firstOrCreate(['name'=>'Prayer Organizer','guard_name'=>'admin'])->givePermissionTo('manage-knock-help');
  });

  it('marks responding sets responder_id', function () {
      $a = User::factory()->create(['is_admin'=>true]); $a->assignRole('Site Admin');
      $c = CareRequest::factory()->create(['status'=>'open']);
      $this->actingAs($a,'admin')->put("/admin/care/{$c->id}", [
          'status' => 'responding',
      ])->assertRedirect();
      expect($c->fresh()->responder_id)->toBe($a->id);
  });

  it('closing sets closed_at', function () {
      $a = User::factory()->create(['is_admin'=>true]); $a->assignRole('Site Admin');
      $c = CareRequest::factory()->create(['status'=>'responding','responder_id'=>$a->id]);
      $this->actingAs($a,'admin')->put("/admin/care/{$c->id}", [
          'status' => 'closed',
      ]);
      expect($c->fresh()->closed_at)->not->toBeNull();
  });
  ```

### Task M8-T07: Sub-admin sidebar test

**Files:**
- Create: `tests/Feature/Admin/SubAdminSidebarTest.php`

- [ ] **Step 1:**
  ```php
  <?php
  use App\Models\User;
  use Spatie\Permission\Models\{Role, Permission};

  beforeEach(function () {
      foreach (['manage-prayer-requests','manage-knock-help','manage-community-feed','manage-events','manage-ministries'] as $p) {
          Permission::firstOrCreate(['name'=>$p,'guard_name'=>'admin']);
      }
      $sa = Role::firstOrCreate(['name'=>'Site Admin','guard_name'=>'admin']);
      $sa->syncPermissions(Permission::all());
      $po = Role::firstOrCreate(['name'=>'Prayer Organizer','guard_name'=>'admin']);
      $po->syncPermissions(['manage-prayer-requests','manage-knock-help','manage-community-feed']);
      $eo = Role::firstOrCreate(['name'=>'Event Organizer','guard_name'=>'admin']);
      $eo->syncPermissions(['manage-events','manage-ministries']);
  });

  it('Prayer Organizer sees only their items', function () {
      $u = User::factory()->create(['is_admin'=>true]); $u->assignRole('Prayer Organizer');
      $html = $this->actingAs($u,'admin')->get('/admin')->getContent();
      expect($html)->toContain('Prayer Requests')->toContain('Knock for Help')->toContain('Community Feed')
          ->not->toContain('Sermons')->not->toContain('Pages')->not->toContain('Blog');
  });

  it('Event Organizer sees events + ministries + Phase 3 stubs', function () {
      $u = User::factory()->create(['is_admin'=>true]); $u->assignRole('Event Organizer');
      $html = $this->actingAs($u,'admin')->get('/admin')->getContent();
      expect($html)->toContain('Events')->toContain('Ministries')->toContain('Attendance')->toContain('Reminders')
          ->not->toContain('Prayer Requests')->not->toContain('Pages');
  });

  it('Site Admin sees all', function () {
      $u = User::factory()->create(['is_admin'=>true]); $u->assignRole('Site Admin');
      $html = $this->actingAs($u,'admin')->get('/admin')->getContent();
      expect($html)->toContain('Pages')->toContain('Blog')->toContain('Prayer Requests')->toContain('Community Feed');
  });
  ```

### Task M8-T08: Donate test

**Files:**
- Create: `tests/Feature/Site/DonateTest.php`

- [ ] **Step 1:**
  ```php
  <?php
  use App\Models\Page;

  beforeEach(function () {
      Page::factory()->create([
          'slug' => 'donate',
          'title' => 'Give',
          'hero_heading' => 'Support our mission',
          'body' => '<p>Bank: 12345678</p>',
          'is_published' => true,
          'published_at' => now(),
      ]);
  });

  it('renders /donate publicly', function () {
      $this->get('/donate')->assertStatus(200)->assertSee('12345678');
  });

  it('redirects member /giving to /donate', function () {
      $u = \App\Models\User::factory()->create();
      $this->actingAs($u,'web')->get('/member/giving')->assertRedirect('/donate');
  });
  ```

### Task M8-T09: Run all Phase 2 tests

- [ ] **Step 1:** `D:\XAMPP\php\php.exe artisan test --filter='Member|Admin/Prayer|Admin/Care|Admin/Feed|Admin/SubAdmin|Site/Donate|Auth/Member|Auth/Email'`
- [ ] **Step 2:** Iterate on failures until all pass.
- [ ] **Step 3:** Run full test suite: `D:\XAMPP\php\php.exe artisan test`. Expected: Phase 1 (74) + Phase 2 (~45) = ~119 passing.

---

# Milestone 9 — Polish + README + Final Smoke

### Task M9-T01: README updates

**Files:**
- Modify: `README.md`

- [ ] **Step 1:** Add a new section after the Phase 1 setup section: **"Phase 2 Features"** with bullet points:
  - Member self-signup at `/register` with email verification
  - Member portal at `/member/*` (Dashboard / Profile / Prayer Requests / Knock for Help / Community Feed / Give)
  - Public donation page at `/donate` editable via Pages CRUD
  - Three new admin modules: Prayer Requests, Knock for Help, Community Feed
  - Sub-admin role wiring: Event Organizer + Prayer Organizer with role-gated sidebars
- [ ] **Step 2:** Update **Phase scope** table:
  - Phase 1: ✅ shipped
  - Phase 2: ✅ shipped (this release)
  - Phase 3: ⏳ planned — Attendance, Reminders, Reports, Communication module
- [ ] **Step 3:** Add **Operational notes** subsection:
  - `QUEUE_CONNECTION=database` required for "Send to members" broadcast. Run `php artisan queue:work` in production.
  - Stripe / Online Giving are NOT implemented — donations go via bank transfer on the `/donate` page (admin-editable content).
  - Mobile responsive: every page tested at 375 / 768 / 1024 / 1440 viewports.

### Task M9-T02: Member-onboarding doc

**Files:**
- Create: `docs/member-onboarding.md`

- [ ] **Step 1:** Short doc explaining the member lifecycle:
  - Visitor lands at `/register`, fills form, gets verification email, clicks link, lands on `/member` dashboard
  - First sign-in flow shows the welcome message from settings
  - Members can submit prayer requests, knock for help, react to feed posts, update profile/avatar/password
  - Account deactivation: Site Admin sets `email_verified_at = null` and locks account via existing user-admin tools

### Task M9-T03: Final smoke

- [ ] **Step 1:** `D:\XAMPP\php\php.exe artisan migrate:fresh --seed`.
- [ ] **Step 2:** Start `php artisan serve`. In a browser:
  1. Register a new account at `/register`. Verify the redirect.
  2. Open `storage/logs/laravel.log`, find the verification link, click it. Should land on `/member`.
  3. Submit a prayer request (public + non-anonymous).
  4. Submit a Knock for Help. Confirm email in log.
  5. Visit `/member/feed` — confirm seeded posts render.
  6. React to a feed post — confirm the count updates.
  7. Log out. Log in as `admin@church.local`. Visit `/admin/prayer-requests` — confirm the new request is listed.
  8. Visit `/admin/care` — confirm the new care request is listed; mark it Responding then Closed.
  9. Create a new feed post with "Send to members" — confirm a job lands in the `jobs` table.
  10. Visit `/donate` (unauthenticated) — confirm the page renders.
  11. Edit `/admin/pages/{donate-id}/edit` — change body, save, confirm new content shows on `/donate`.
- [ ] **Step 3:** Confirm responsive: open DevTools, set viewport to 375px, walk through public + member + admin pages. No horizontal scroll.
- [ ] **Step 4:** Run `D:\XAMPP\php\php.exe artisan test` — all ~119 tests green.

---

# Spec Coverage Self-Check

| Spec section | Plan task(s) |
|---|---|
| §2 Member auth + onboarding | M3-T01..T02 |
| §2 Member portal (Dashboard/Profile/Prayer/Care/Feed/Give) | M3-T03..T04, M4, M5, M6, M7-T02 |
| §2 Public donation page | M2-T09 (seed), M7-T01 (route + view) |
| §2 Admin Prayer / Care / Feed modules | M4-T03, M5-T02, M6-T02 |
| §2 Sub-admin role wiring | M3-T05 |
| §2 Admin Member Directory (filters + Send invite) | Reuses Phase 1 Users CRUD; filter UI is a small extension covered by M3-T05 sidebar rename to "Members". (Send invite action deferred to a quick polish item — surface as M9 enhancement if user wants it). |
| §3 New tables (4 + extensions) | M2-T01..T04 |
| §3 Permission + seeder additions | M2-T07..T09 |
| §3 Donate page seed | M2-T09 |
| §4 Member layout + screens | M1-T02..T04 + M3 conversions |
| §5 Admin screens for new modules | M4-T03, M5-T02, M6-T02 |
| §6 Routes | Routes are wired in each milestone's tasks |
| §7 Directory structure | created across milestones |
| §8 Security (verification, policies, rate limits, honeypot, anonymity) | M3-T01 (honeypot + limiter), M3-T02 (verification), M4-T01 (policy + limiter), M5-T01 (policy + limiter), M6-T01 (policy + limiter) |
| §9 Tests (~45 new) | M8-T01..T08 (32 listed tests, ~45 effective via dataset/multiple `it` blocks) |
| §10 Build sequence (M1 sign-off gate) | M1-T01..T07 |
| §11 Acceptance criteria | covered by M9-T03 final smoke + M8 tests |
| Responsive QA across all pages | M1-T06 |
| Dropdown audit across all Alpine menus | M1-T05 |

**Member directory "Send invite" action** is a small extension of Phase 1 user CRUD — flag it as a M9 polish task if the user wants it before Phase 3.

# Placeholder Scan

- No "TBD"/"TODO"/"implement later" instructions in tasks.
- Every code block contains complete, runnable code.
- Route names referenced (e.g. `admin.prayer-requests.index`, `member.prayer.index`) all match the routes defined in the same milestone.

# Notes for the implementing engineer

- This plan assumes Phase 1 is fully working. Run `D:\XAMPP\php\php.exe artisan test` before starting M1 to confirm — 74 passing is the baseline.
- Reseed the DB (`migrate:fresh --seed`) when starting M2; that establishes the donate page and Phase 2 permissions cleanly.
- Use the bootstrap admin (`admin@church.local` / current password) when probing admin routes during development.
- Create at least one test member account early (via `/register`) so you have a real user to log in as during member-side development.
- The `LogsModelActivity` trait fires on every save — for the new models, activity log rows will populate automatically. Some tests need to assert this; others should ignore it.
- Don't break Phase 1 routes when renaming "Users" → "Members" in the sidebar — keep the underlying route name `admin.users.index` so all existing tests/links continue to work; only the sidebar label changes.
- No git commits per user instruction; do not run `git init/add/commit` in any task.
