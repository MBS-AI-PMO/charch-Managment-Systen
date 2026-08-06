# Church CMS — Phase 3 Design (Attendance, RSVP, Reminders, Reports, Tithes)

**Date:** 2026-06-03
**Status:** Approved — ready for implementation planning
**Prior phases:**
- Phase 1 — Foundation, admin panel, public site, settings, brand, pages, events (read-only public)
- Phase 2 — Member auth, profile, prayer requests, knock-for-help, community feed, donate page (bank-transfer), responsive dropdowns

---

## 1. Scope

Phase 3 closes the operational loop of the CMS — moving from "publish content" to "manage participation."

**In scope:**
- **Event registration (Simple RSVP)** — members RSVP to events, optionally bringing guests.
- **Attendance tracking (Hybrid)** — self-check-in via 4-digit code + organizer manual marking; supports walk-ins.
- **Reminders engine** — scheduled emails: 24h-before event reminders, weekly member digest, optional admin daily digest.
- **Reports** — simple KPI dashboard (members, events, attendance, giving) with CSV export.
- **Tithes records** — manual entry of cash/cheque/bank-transfer gifts, fund accounting, finally wiring the existing `view-reports`/`manage-tithes` gates.

**Out of scope (deferred):**
- Communication module / bulk email blasts (deferred to a future phase if needed).
- Online payment processing (donations remain bank-transfer details on the static donate page — decided in Phase 2).
- Mobile app (still future).
- SMS reminders (email-only for Phase 3).

**Architecture continuity:** Approach A — Pure Laravel + Custom Admin (no Filament, no Jetstream, no SPA). Blade + Alpine + Tailwind v3 + Vite. Two auth guards (`admin`, `web`). Spatie permissions on `admin` guard.

---

## 2. Database schema

### New tables

**`event_rsvps`**
```
id                bigint PK
event_id          FK → events.id (cascade on delete)
user_id           FK → users.id  (cascade on delete)
guest_count       unsigned tinyint  default 0     -- 0..5
status            enum('going', 'cancelled')      default 'going'
note              text nullable
timestamps
unique(event_id, user_id)
index(event_id, status)
```

**`event_attendances`**
```
id                bigint PK
event_id          FK → events.id (cascade on delete)
user_id           FK → users.id nullable          -- null for walk-ins
guest_name        string(120) nullable            -- for walk-ins
guest_count       unsigned tinyint default 0      -- additional guests with this attendee
checked_in_at     timestamp default now()
method            enum('self', 'organizer')
recorded_by       FK → users.id (admin who recorded; nullable for self)
timestamps
index(event_id, user_id)
index(event_id, checked_in_at)
```

Note: no unique constraint on (event_id, user_id) — a walk-in row may share a user_id of NULL with others. Duplicate-prevention for member self-check-in is enforced in controller logic.

**`tithe_funds`**
```
id                bigint PK
slug              string(60) unique
name              string(120)
description       text nullable
is_active         boolean default true
sort_order        int default 0
timestamps
```
Seed: `general` (General Offering), `missions` (Missions Fund), `building` (Building Fund).

**`tithes`**
```
id                bigint PK
user_id           FK → users.id nullable           -- null for non-member givers
giver_name        string(160) nullable             -- captured display name; for members copied from users.name
fund_id           FK → tithe_funds.id
amount_cents      unsigned bigint
received_at       date
method            enum('cash', 'bank_transfer', 'cheque', 'other')
reference         string(120) nullable             -- cheque #, bank ref
note              text nullable                    -- HTMLPurifier 'cms' profile on save
recorded_by       FK → users.id (admin)
timestamps
index(received_at)
index(fund_id, received_at)
index(user_id)
```

### Columns added to existing tables

**`events`**
```
checkin_code      char(4) nullable                 -- 4-digit string; null until generated
attendance_open   boolean default false
reminded_at       timestamp nullable               -- set by 24h reminder job; prevents re-send
```

**`users`**
```
email_reminder_event_24h    boolean default true
email_weekly_digest         boolean default true
email_admin_daily_digest    boolean default false  -- only meaningful for admins
```

### Permissions and settings

**Permission:** `manage-tithes` — created in seeder, assigned to Site Admin role only.
**Permission wiring:** `view-reports` (already created in Phase 1) finally enforced on the reports route group in this phase.

**App settings seeded:**
- `finance.currency_symbol` = `$`
- `reminders.event_24h_enabled` = `true`
- `reminders.weekly_digest_enabled` = `true`
- `reminders.weekly_digest_day` = `Saturday`
- `reminders.weekly_digest_hour` = `18`
- `reminders.admin_daily_digest_enabled` = `false`

---

## 3. Member UI — RSVP + self-check-in

### Event pages (public + member)

The Phase 1 public events templates (`site.events.index`, `site.events.show`) are extended (not duplicated).

**Events index card footer strip:**
- Logged-in member who has RSVP'd → small "✓ You're going" badge in brand-secondary.
- Logged-in member who hasn't RSVP'd → "RSVP →" link inline with the date.
- Guest (not logged in) → no badge.

**Event detail page — new RSVP card** (below description, above breadcrumb footer):
- **Guest:** "Sign in to RSVP" → `/login?return=/events/{slug}`.
- **Logged-in, no RSVP:** inline form — "I'll be there" primary button, optional guest count select (0–5), optional collapsible note textarea.
- **Logged-in, RSVP'd:** "✓ You're coming · {date} {time}" + bringing-N-guests line (if >0) + "Edit RSVP" + "Cancel RSVP" button (DELETE form).
- **Going list:** "{count} people are coming" with first 8 first-names + "and {N-8} others." First names only; never last names or email.

### Self-check-in flow at `/member/check-in`

Mobile-first page used by members at the door:
- 4-digit code input (Alpine splits into 4 boxes visually; submits as single string).
- POST validates: code matches an `events.checkin_code`, `attendance_open = true`, and `starts_at` is within (now − 4h, now + 12h).
- On success → `event_attendances` row created (`method=self`, `user_id=current`, `checked_in_at=now`); redirect to thanks page.
- Errors:
  - Bad code → "That code doesn't match an event happening now."
  - Already checked in → "You're already checked in. ✓" (idempotent; no duplicate row).
  - Code valid but `attendance_open=false` → "Check-in isn't open yet."
- Rate limit: 10 attempts / 5 min / IP via new `checkin-submit` limiter.

### Member dashboard additions

- **"Your RSVPs" card** — next 3 upcoming events the member RSVP'd to (going status), with going-count per event.
- **"Check in" quick-action button** — links to `/member/check-in`; visible only when at least one event today has `attendance_open=true`.

### Member nav

Mobile drawer in `components/member/header.blade.php` gains a conditional "Check in" item — rendered only when there's an active event window. Desktop nav unchanged.

### Profile (no schema changes beyond Phase 2)

Existing profile editing covers all member-facing fields. Email-preference toggles are added in M8 (reminders engine).

### Routes (added to `routes/member.php`)

Inside the existing `auth:web` + `verified` + `prefix('member')` + `name('member.')` group:

```php
Route::post('/events/{event:slug}/rsvp',   [MemberEventController::class, 'rsvp'])
    ->name('events.rsvp')->middleware('throttle:event-rsvp');
Route::delete('/events/{event:slug}/rsvp', [MemberEventController::class, 'cancelRsvp'])
    ->name('events.cancel-rsvp');

Route::get('/check-in',  [MemberCheckinController::class, 'show'])->name('checkin.show');
Route::post('/check-in', [MemberCheckinController::class, 'submit'])
    ->name('checkin.submit')->middleware('throttle:checkin-submit');
Route::get('/check-in/thanks/{attendance}', [MemberCheckinController::class, 'thanks'])
    ->name('checkin.thanks');
```

The existing public `site.events.show` controller is extended to pass `$myRsvp = auth()->check() ? EventRsvp::where(...)->first() : null` to the view.

---

## 4. Admin UI — rosters, attendance, reports, tithes

### A. Event roster + attendance

Adds an **Attendance tab** to the event detail/edit layout at `/admin/events/{event}`. The tab is the workhorse for Event Organizers and Site Admins.

Components:
- **Check-in code** — 4-digit, auto-generated on first open, regeneratable via POST.
- **Open/closed toggle** — `events.attendance_open` boolean; member self-check-in only works while open.
- **Roster** — combined RSVPs + walk-ins in one sortable table. Columns: ✓ (toggleable), name, +guests, RSVP age, check-in method.
- **Add walk-in** modal — name (optional, defaults "Guest"), guest count 0–5.
- **Counts header** — RSVPs / checked in / walk-ins; refreshed on toggle and every 30s via Alpine fetch.
- **Export CSV** — name, RSVP guests, checked-in y/n, method, checked-in-at.

Authorization: Event Organizer can manage attendance for events they own (`events.organizer_id` from Phase 2); Site Admin can manage all. Enforced via policy.

### B. Reports dashboard at `/admin/reports`

Gated by `view-reports` permission.

Content:
- **Date range picker** — presets (Last 7 / 30 / 90 days, This year) + custom.
- **KPI cards** — Members (with delta), Events held, Average attendance, Giving total (currency-formatted from `amount_cents`).
- **Attendance trend** — inline SVG sparkline of per-event attendance over range.
- **Giving by fund** — horizontal bar list with totals.
- **Top events by attendance** — top 5 events.
- **Top givers** — count only (privacy); names accessible only via Tithes index for Site Admin.
- **CSV exports** — attendance CSV, giving CSV, both streamed.

Performance: KPI queries cached for 5 min per (date-range, user-id) key.

### C. Tithes CRUD (Site Admin only — `manage-tithes`)

**Index `/admin/tithes`:**
- Table: date, giver (with "(member)" badge if linked), fund, method, amount, recorded-by.
- Filter by fund and method; search by giver/reference.
- CSV export; pagination; sortable columns.

**Create/Edit modal:**
- Giver — typeahead matching members; free-text allowed for non-member givers (saved as `giver_name`, `user_id` null).
- Fund — select from active `tithe_funds`.
- Amount — currency input, stored as `amount_cents`.
- Method — cash / bank_transfer / cheque / other.
- Received-at — date picker, default today.
- Reference — string (cheque #, bank ref).
- Note — textarea, HTMLPurifier 'cms' profile.

**Funds sub-page `/admin/tithes/funds`:**
- CRUD for `tithe_funds` — name, slug (auto from name on create), description, active toggle, sort order.
- Soft "delete" via `is_active=false` (preserves historical tithes referencing the fund).

### D. Admin sidebar additions

- **Engagement** group (existing) — add "Attendance" → `/admin/attendance` (global cross-event index).
- **Finance** group (new, Site Admin only) — "Tithes", "Funds", "Donate page" (links to Pages editor).
- **Reports** group (new, gated by `view-reports`) — "Overview" → `/admin/reports`.

The Phase 2 stub views at `resources/views/admin/stubs/phase3.blade.php` are removed; sidebar items previously linking to `#` are wired to real route names.

### E. Routes (added to `routes/admin.php`)

```php
// Attendance
Route::get('/events/{event}/attendance', [AdminAttendanceController::class, 'show'])
    ->name('events.attendance');
Route::post('/events/{event}/checkin-code/regenerate', [AdminAttendanceController::class, 'regenerateCode'])
    ->name('events.checkin-code.regenerate');
Route::put('/events/{event}/attendance-open', [AdminAttendanceController::class, 'toggleOpen'])
    ->name('events.attendance.toggle');
Route::post('/events/{event}/attendance', [AdminAttendanceController::class, 'store'])
    ->name('events.attendance.store');
Route::delete('/events/{event}/attendance/{attendance}', [AdminAttendanceController::class, 'destroy'])
    ->name('events.attendance.destroy');
Route::get('/events/{event}/attendance/export', [AdminAttendanceController::class, 'export'])
    ->name('events.attendance.export');
Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');

// Reports
Route::middleware('can:view-reports')->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [AdminReportsController::class, 'index'])->name('index');
    Route::get('/attendance.csv', [AdminReportsController::class, 'attendanceCsv'])->name('attendance.csv');
    Route::get('/giving.csv',     [AdminReportsController::class, 'givingCsv'])->name('giving.csv');
});

// Tithes
Route::middleware('can:manage-tithes')->prefix('tithes')->name('tithes.')->group(function () {
    Route::resource('funds', AdminTitheFundController::class)->except(['show']);
    Route::get('export', [AdminTitheController::class, 'export'])->name('export');
    Route::get('/',                 [AdminTitheController::class, 'index'])->name('index');
    Route::get('/create',           [AdminTitheController::class, 'create'])->name('create');
    Route::post('/',                [AdminTitheController::class, 'store'])->name('store');
    Route::get('/{tithe}/edit',     [AdminTitheController::class, 'edit'])->name('edit');
    Route::put('/{tithe}',          [AdminTitheController::class, 'update'])->name('update');
    Route::delete('/{tithe}',       [AdminTitheController::class, 'destroy'])->name('destroy');
});
```

---

## 5. Reminders engine

Three scheduled jobs, all using Laravel's queue (database driver, already configured in Phase 2). Mail driver is `log` in dev. All scheduled in `bootstrap/app.php` `withSchedule()`.

### Job 1 — Event reminders (24h)

`App\Jobs\SendEventReminderEmails`, scheduled hourly on the hour.

Logic:
- Skip if `settings('reminders.event_24h_enabled')` is false.
- Find events where `starts_at` is between (now + 23h, now + 25h) AND `reminded_at IS NULL`.
- For each event:
  - Load `event_rsvps` where `status=going`.
  - For each RSVP: skip if user not verified OR `users.email_reminder_event_24h = false`; otherwise queue `EventReminderMail($event, $rsvp, $user)`.
  - Set `events.reminded_at = now()` (idempotency).

Email body includes: event name, date/time, location, the member's guest count, and a signed-URL "I can't make it" link routing to `member.events.cancel-rsvp` (7-day TTL).

### Job 2 — Weekly member digest

`App\Jobs\SendWeeklyMemberDigest`, scheduled weekly at the configured day/hour:

```php
$schedule->job(new SendWeeklyMemberDigest)
    ->weeklyOn(
        weekday(settings('reminders.weekly_digest_day', 'Saturday')),
        sprintf('%02d:00', (int) settings('reminders.weekly_digest_hour', 18))
    );
```

Where `weekday()` is a helper mapping `'Sunday' .. 'Saturday'` → `0 .. 6`.

Logic:
- Skip if `settings('reminders.weekly_digest_enabled')` is false.
- Chunk verified members (200 at a time) where `email_weekly_digest = true`.
- For each: build payload — upcoming events (next 7 days, top 5), unanswered prayer requests (top 3), latest feed posts (top 3), pending care-request status updates for that user.
- If payload is empty → skip; don't send a blank digest.
- Otherwise queue `WeeklyDigestMail($user, $payload)`.

### Job 3 — Admin daily digest (opt-in)

`App\Jobs\SendAdminDailyDigest`, scheduled daily at 08:00.

Logic:
- Skip if `settings('reminders.admin_daily_digest_enabled')` is false.
- Audience: admins where `email_admin_daily_digest = true`.
- Payload: yesterday's new members, new prayer requests, new care requests (open), feed posts pending moderation (if applicable), today's events with attendance open.
- Queue `AdminDailyDigestMail($admin, $payload)` per recipient.

### Mailable base class

`App\Mail\BrandedMail` — sets from/reply-to (`settings('contact.email')` + brand name), injects header logo + brand color, and footer with church address and an authenticated "Manage email preferences" link to `/member/profile#email-prefs`. All three mailables extend it and implement `ShouldQueue`.

### Settings UI

`/admin/settings` gains a "Reminders" tab:
- Event 24h reminder — on/off (global).
- Weekly digest — on/off (global).
- Weekly digest day — select (Sun–Sat).
- Weekly digest hour — select (00–23).
- Admin daily digest — on/off (global).

Stored under `reminders.*` keys in the existing `app_settings` table.

### Profile email preferences

Member profile page and admin profile page gain an "Email preferences" card with checkboxes for the relevant `users.email_*` columns. Saves through the existing `profile.update` action.

### Activity log

Each job calls `activity()->log(...)` summarizing the batch: `"Sent N event reminders for {event}"`, `"Sent N weekly digests"`, `"Sent N admin daily digests"`.

### Failure handling

- Default Laravel retry: `$tries = 3` with exponential backoff.
- Per-recipient failures land in `failed_jobs` without aborting the parent batch (each mailable is its own queued job).
- Failed jobs viewable at the existing `/admin/queue/failed`.

### Scheduler kernel

In `bootstrap/app.php`:

```php
->withSchedule(function (Schedule $schedule) {
    $schedule->job(new SendEventReminderEmails)
        ->hourly()->onOneServer()->withoutOverlapping();
    $schedule->job(new SendWeeklyMemberDigest)
        ->weeklyOn(
            weekday(settings('reminders.weekly_digest_day', 'Saturday')),
            sprintf('%02d:00', (int) settings('reminders.weekly_digest_hour', 18))
        )
        ->onOneServer()->withoutOverlapping();
    $schedule->job(new SendAdminDailyDigest)
        ->dailyAt('08:00')->onOneServer()->withoutOverlapping();
})
```

Deployment notes: scheduler entry (`* * * * * php artisan schedule:run`) and queue worker daemon (Supervisor on Linux, NSSM on Windows). In dev, run `php artisan queue:work` manually.

---

## 6. Implementation phasing, tests, and security

### Milestones (subagent-per-milestone)

**M1 — DB migrations + models + seeds**
- Migrations: 4 new tables (`event_rsvps`, `event_attendances`, `tithe_funds`, `tithes`), 3 columns on `events` (`checkin_code`, `attendance_open`, `reminded_at`), 3 columns on `users` (email-preference booleans).
- Models: `EventRsvp`, `EventAttendance`, `TitheFund`, `Tithe` — `HasFactory`, `$fillable`, casts (enums, `amount_cents` integer), relationships, `LogsModelActivity` where audit value is non-trivial (Tithe and EventAttendance).
- Seed: 3 default tithe funds (general, missions, building).
- Seed: `manage-tithes` permission, assigned to Site Admin role.
- Seed: 6 settings under `finance.*` and `reminders.*`.

**M2 — Member RSVP UI**
- Extend `MemberEventController` (or create if missing) with `rsvp` and `cancelRsvp` actions.
- Update `site.events.show` view: RSVP card with all four states (guest, no-rsvp, rsvp'd, edit).
- Update `site.events.index` view: RSVP'd badge on cards.
- Register `event-rsvp` rate limiter (10/min/user) in `RouteServiceProvider`.
- Pest tests: rsvp create/update/cancel, guest count validation (0–5), guest sign-in redirect, going-list privacy (first names only).

**M3 — Member check-in flow**
- `MemberCheckinController` with `show`, `submit`, `thanks` actions.
- View `member.checkin.show` — 4-digit Alpine input.
- View `member.checkin.thanks`.
- Register `checkin-submit` rate limiter (10/5min/IP).
- Conditional nav item in `components/member/header.blade.php` (visible only when any event has `attendance_open=true` AND falls within the check-in window today).
- Pest tests: bad code, expired window (>12h after or >4h before start), `attendance_open=false`, duplicate check-in returns "already checked in" without creating new row, rate limit triggers.

**M4 — Member dashboard update**
- Add "Your RSVPs" card to `member.dashboard` view (next 3 upcoming RSVP'd events with going-counts).
- Add "Check in" quick-action button (conditional on attendance window).
- Pest tests: dashboard renders RSVP card with correct data; hides check-in button when no event window open.

**M5 — Admin event roster + attendance grid**
- `AdminAttendanceController` with: `index` (global), `show` (per-event roster), `regenerateCode`, `toggleOpen`, `store` (toggle member or add walk-in), `destroy`, `export`.
- Add "Attendance" tab to admin event layout.
- Roster table view with Alpine for live toggle + counts (poll every 30s).
- Walk-in modal component.
- CSV export via `Response::streamDownload` with CSV-injection guard.
- Global `/admin/attendance` index.
- Delete Phase 2 stub view; wire sidebar to real route.
- Policy: Event Organizer limited to events they own (`events.organizer_id`); Site Admin unrestricted.
- Pest tests: code generation/regeneration, open/close toggle, member check toggle creates/destroys attendance, walk-in create, CSV header + sample row format, Event Organizer 403 on someone else's event.

**M6 — Tithes CRUD + funds management**
- `AdminTitheController` (resource + `export`).
- `AdminTitheFundController` (resource minus `show`).
- Tithe index view + create/edit modal + funds CRUD page.
- Member typeahead endpoint (Site Admin only, returns id+name only, no emails).
- Helper `formatMoney($cents)` using `settings('finance.currency_symbol')`.
- Currency input that converts to/from cents on save (input as decimal, stored as integer).
- Pest tests: tithe CRUD, fund CRUD, member typeahead returns id+name only, Event Organizer gets 403 on tithes routes, slug auto-generated from fund name, soft-deactivation preserves historical tithes.

**M7 — Reports dashboard**
- `AdminReportsController` with `index`, `attendanceCsv`, `givingCsv`.
- KPI queries (with 5-min cache per (range, user-id)): members count + delta, events held, avg attendance, giving total.
- Sparkline SVG Blade component (no chart library).
- "Giving by fund" + "Top events" + "Top givers (count only)" sections.
- Sidebar item under `view-reports` gate.
- CSV streaming with CSV-injection guard.
- Pest tests: KPI accuracy against known fixtures, date-range filter shifts results, CSV stream produces expected header + row count, Event Organizer without `view-reports` gets 403.

**M8 — Reminders engine**
- `App\Mail\BrandedMail` base class.
- `EventReminderMail`, `WeeklyDigestMail`, `AdminDailyDigestMail`.
- `SendEventReminderEmails`, `SendWeeklyMemberDigest`, `SendAdminDailyDigest` jobs (`ShouldQueue`).
- Scheduler entries in `bootstrap/app.php` `withSchedule()`.
- `/admin/settings` "Reminders" tab.
- Profile "Email preferences" cards (member + admin variants).
- `weekday()` helper for day-name → 0..6 mapping.
- Activity-log writes per batch.
- Delete Phase 2 reminders stub; wire sidebar.
- Pest tests (using `Mail::fake()` + `Queue::fake()`): each job dispatches correct count, user opt-out skips, global toggle skips, `reminded_at` prevents double-send, empty weekly digest is skipped (no mail dispatched), unverified members are skipped.

**M9 — Polish, docs, smoke**
- Update `docs/member-onboarding.md` with RSVP + check-in instructions.
- New `docs/organizer-attendance.md` for Event Organizers running events.
- New `docs/finance-tithes.md` for Site Admin (recording gifts, funds, exports).
- Manual smoke: full member flow (browse → RSVP → receive reminder via `queue:work` → check in) and full admin flow (create event → open attendance → mark attendance → record tithe → view reports).
- Run full Pest suite; fix any flakiness.

**Pause checkpoints:** M5 (heavy admin UX) and M8 (queues + mail — user must confirm queue worker is running for the smoke check).

### Testing strategy

- **Framework:** Pest v3 feature tests with `RefreshDatabase`, factory-driven (no real mail, no real queue).
- **Coverage targets:** every controller action has at least one happy-path and one authorization test.
- **Mailables/jobs:** asserted via `Mail::fake()` + `Queue::fake()` — assert dispatch count + payload, not delivery.
- **No browser tests in Phase 3.** Manual smoke at M9 covers UI integration.
- **Conventions** continue from Phases 1–2: `RefreshDatabase`, model factories, `actingAs(user, guard)` for auth, `assertForbidden` / `assertOk` for gate checks.

### Security checklist

- **Permission gates enforced**: `manage-tithes` (Site Admin only) on tithes + funds + giver typeahead; `view-reports` finally wired on reports group; Event Organizer scoped to their own events via policy on the attendance controller.
- **Rate limits**: `event-rsvp` 10/min/user; `checkin-submit` 10/5min/IP.
- **Check-in code privacy**: codes never appear in any public URL, public page, or member-facing view; rotatable via `regenerateCode`.
- **CSV-injection guard**: cells starting with `=`, `+`, `-`, or `@` are prefixed with `'` before writing; applied to all CSV exports (attendance, tithes, reports).
- **Money handling**: `amount_cents` integer throughout; never floats; UI input converted at controller boundary.
- **Signed URLs**: "Can't make it" link in event reminder email uses Laravel signed routes with 7-day TTL.
- **CSRF**: all POST/PUT/DELETE protected by default Laravel middleware; no `except` exceptions added.
- **Mass assignment**: `$fillable` on every new model; never `$guarded = []`.
- **HTML sanitization**: `note` field on tithes and any rich-text added in Phase 3 runs through HTMLPurifier `cms` profile.
- **Privacy on going-list**: first names only; no last names or emails surfaced.
- **No member enumeration leak**: giver typeahead endpoint requires `manage-tithes` permission and returns id + name only.
- **Reports cache key**: includes user-id to prevent leaking cached results across users with differing visibility.
- **No raw SQL**: all queries use Eloquent or query-builder bindings — no string-concat queries.
- **Auth assertions in tests**: every milestone includes at least one "wrong role gets 403" test.

### Done criteria

- All 9 milestones merged; Pest suite passes.
- Manual smoke confirms:
  - Member can RSVP, cancel RSVP, and receive a reminder email (verified via `log` mail driver and `queue:work`).
  - Member can check in with a valid code; duplicate check-in is idempotent; rate limit triggers as expected.
  - Event Organizer can mark attendance and export CSV for their events but not others'.
  - Site Admin can record a tithe (cash, bank transfer, cheque) and view it in reports.
  - Reports dashboard renders KPIs with cache and exports both CSVs.
  - Reminders settings tab toggles the three global gates; profile preferences toggle per-user.
- Three new docs published: `docs/member-onboarding.md` (updated), `docs/organizer-attendance.md` (new), `docs/finance-tithes.md` (new).
