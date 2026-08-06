# Church CMS Phase 3 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship Phase 3 — Event RSVPs, hybrid attendance tracking (self-check-in + organizer marking + walk-ins), reminders engine (event 24h, weekly digest, admin daily digest), reports dashboard with CSV exports, and Tithes records with funds management.

**Architecture:** Direct extension of Phase 1 and Phase 2 patterns. Same Laravel 12 + Blade + Tailwind v3 + Alpine + Spatie Permission stack. Four new tables (event_rsvps, event_attendances, tithe_funds, tithes), three new columns on `events`, three new email-preference columns on `users`. New permission `manage-tithes`; finally wires `view-reports`. Three queued mailables + three scheduled jobs. Reuses existing settings store, activity log, HTMLPurifier, rate-limit framework.

**Tech Stack:** PHP 8.2+, Laravel 12, MySQL 8 (XAMPP), Tailwind v3, Alpine.js, Vite, Spatie Laravel-Permission, mews/purifier, Pest 3. Mail driver `log` in dev. Queue driver `database`.

**No git in this plan** — per user instruction, no commit steps.

**Source spec:** `docs/superpowers/specs/2026-06-03-church-cms-phase-3-design.md`

---

## Reading order

Tasks are grouped into **Milestones** (M1–M9). Each milestone produces a working slice.

| Milestone | What ships | Pause checkpoint |
|---|---|---|
| M1 | Migrations + models + factories + permission/settings seeders | — |
| M2 | Member RSVP UI (public event detail + index extensions) | — |
| M3 | Member self check-in flow (`/member/check-in`) | — |
| M4 | Member dashboard updates ("Your RSVPs", conditional check-in button) | — |
| M5 | Admin event roster + attendance grid + walk-ins + CSV export | **PAUSE — UX review** |
| M6 | Tithes CRUD + funds management | — |
| M7 | Reports dashboard + CSV exports | — |
| M8 | Reminders engine — mailables, jobs, scheduler, settings tab, profile prefs | **PAUSE — confirm queue worker running** |
| M9 | Polish, docs (3 new/updated), end-to-end smoke | — |

---

## Local-env reminder (PowerShell prologue)

When running `php`, `composer`, `artisan`, or `npm`, all paths are XAMPP-relative because nothing is on PATH:

```powershell
$env:Path = "D:\XAMPP\php;" + $env:Path
# php = D:\XAMPP\php\php.exe
# composer = D:\XAMPP\php\php.exe D:\XAMPP\composer\composer
# mysql is via XAMPP control panel; database name: church_cms
```

For artisan calls inside this plan, the literal command is:
```powershell
D:\XAMPP\php\php.exe artisan <subcommand>
```
…but it's shortened to `php artisan <subcommand>` in tasks for readability.

---

# Milestone 1 — Migrations, models, factories, seeders

> **Goal of M1:** All new tables and columns exist; all four models with factories and relationships; default tithe funds seeded; `manage-tithes` permission seeded and assigned to Site Admin; six new app settings seeded.

### Task M1-T01: Migration — `event_rsvps` table

**Files:**
- Create: `database/migrations/2026_06_03_000001_create_event_rsvps_table.php`

- [ ] **Step 1: Create migration**

```powershell
php artisan make:migration create_event_rsvps_table
```

Then replace the migration body with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('guest_count')->default(0);
            $table->enum('status', ['going', 'cancelled'])->default('going');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->index(['event_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_rsvps');
    }
};
```

- [ ] **Step 2: Run the migration**

```powershell
php artisan migrate
```
Expected: "Migrating: 2026_06_03_000001_create_event_rsvps_table" → DONE.

### Task M1-T02: Migration — `event_attendances` table

**Files:**
- Create: `database/migrations/2026_06_03_000002_create_event_attendances_table.php`

- [ ] **Step 1: Create migration with this body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('guest_name', 120)->nullable();
            $table->unsignedTinyInteger('guest_count')->default(0);
            $table->timestamp('checked_in_at')->useCurrent();
            $table->enum('method', ['self', 'organizer']);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['event_id', 'user_id']);
            $table->index(['event_id', 'checked_in_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
```

- [ ] **Step 2: Run migration**

```powershell
php artisan migrate
```

### Task M1-T03: Migration — `tithe_funds` table

**Files:**
- Create: `database/migrations/2026_06_03_000003_create_tithe_funds_table.php`

- [ ] **Step 1: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tithe_funds', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tithe_funds');
    }
};
```

- [ ] **Step 2: Run migration**

```powershell
php artisan migrate
```

### Task M1-T04: Migration — `tithes` table

**Files:**
- Create: `database/migrations/2026_06_03_000004_create_tithes_table.php`

- [ ] **Step 1: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tithes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('giver_name', 160)->nullable();
            $table->foreignId('fund_id')->constrained('tithe_funds');
            $table->unsignedBigInteger('amount_cents');
            $table->date('received_at');
            $table->enum('method', ['cash', 'bank_transfer', 'cheque', 'other']);
            $table->string('reference', 120)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();

            $table->index('received_at');
            $table->index(['fund_id', 'received_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tithes');
    }
};
```

- [ ] **Step 2: Run migration**

```powershell
php artisan migrate
```

### Task M1-T05: Migration — add columns to `events`

**Files:**
- Create: `database/migrations/2026_06_03_000005_add_checkin_columns_to_events_table.php`

- [ ] **Step 1: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->char('checkin_code', 4)->nullable()->after('location');
            $table->boolean('attendance_open')->default(false)->after('checkin_code');
            $table->timestamp('reminded_at')->nullable()->after('attendance_open');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['checkin_code', 'attendance_open', 'reminded_at']);
        });
    }
};
```

- [ ] **Step 2: Run migration**

```powershell
php artisan migrate
```

### Task M1-T06: Migration — add email-preference columns to `users`

**Files:**
- Create: `database/migrations/2026_06_03_000006_add_email_prefs_to_users_table.php`

- [ ] **Step 1: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_reminder_event_24h')->default(true);
            $table->boolean('email_weekly_digest')->default(true);
            $table->boolean('email_admin_daily_digest')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_reminder_event_24h',
                'email_weekly_digest',
                'email_admin_daily_digest',
            ]);
        });
    }
};
```

- [ ] **Step 2: Run migration**

```powershell
php artisan migrate
```

### Task M1-T07: `EventRsvp` model + factory

**Files:**
- Create: `app/Models/EventRsvp.php`
- Create: `database/factories/EventRsvpFactory.php`

- [ ] **Step 1: Model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'user_id', 'guest_count', 'status', 'note',
    ];

    protected $casts = [
        'guest_count' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 2: Factory**

```php
<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRsvpFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'guest_count' => 0,
            'status' => 'going',
            'note' => null,
        ];
    }

    public function cancelled(): self
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }
}
```

### Task M1-T08: `EventAttendance` model + factory

**Files:**
- Create: `app/Models/EventAttendance.php`
- Create: `database/factories/EventAttendanceFactory.php`

- [ ] **Step 1: Model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'user_id', 'guest_name', 'guest_count',
        'checked_in_at', 'method', 'recorded_by',
    ];

    protected $casts = [
        'guest_count' => 'integer',
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function displayName(): string
    {
        return $this->user?->name ?? ($this->guest_name ?: 'Guest');
    }
}
```

- [ ] **Step 2: Factory**

```php
<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventAttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'guest_name' => null,
            'guest_count' => 0,
            'checked_in_at' => now(),
            'method' => 'self',
            'recorded_by' => null,
        ];
    }

    public function walkIn(?string $name = null): self
    {
        return $this->state(fn () => [
            'user_id' => null,
            'guest_name' => $name ?: 'Guest',
            'method' => 'organizer',
            'recorded_by' => User::factory(),
        ]);
    }

    public function organizer(): self
    {
        return $this->state(fn () => [
            'method' => 'organizer',
            'recorded_by' => User::factory(),
        ]);
    }
}
```

### Task M1-T09: `TitheFund` model + factory

**Files:**
- Create: `app/Models/TitheFund.php`
- Create: `database/factories/TitheFundFactory.php`

- [ ] **Step 1: Model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TitheFund extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'name', 'description', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $fund) {
            if (! $fund->slug) {
                $fund->slug = Str::slug($fund->name);
            }
        });
    }

    public function tithes(): HasMany
    {
        return $this->hasMany(Tithe::class, 'fund_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
```

- [ ] **Step 2: Factory**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TitheFundFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->randomElement(['General Offering', 'Missions Fund', 'Building Fund', 'Outreach']);
        return [
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 9999),
            'name' => $name,
            'description' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
```

### Task M1-T10: `Tithe` model + factory

**Files:**
- Create: `app/Models/Tithe.php`
- Create: `database/factories/TitheFactory.php`

- [ ] **Step 1: Model**

```php
<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tithe extends Model
{
    use HasFactory, LogsModelActivity;

    protected $fillable = [
        'user_id', 'giver_name', 'fund_id', 'amount_cents',
        'received_at', 'method', 'reference', 'note', 'recorded_by',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'received_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(TitheFund::class, 'fund_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function giverDisplayName(): string
    {
        return $this->user?->name ?? ($this->giver_name ?: 'Anonymous');
    }
}
```

- [ ] **Step 2: Factory**

```php
<?php

namespace Database\Factories;

use App\Models\TitheFund;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TitheFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'giver_name' => $this->faker->name(),
            'fund_id' => TitheFund::factory(),
            'amount_cents' => $this->faker->numberBetween(500, 50000),
            'received_at' => now()->toDateString(),
            'method' => $this->faker->randomElement(['cash', 'bank_transfer', 'cheque', 'other']),
            'reference' => null,
            'note' => null,
            'recorded_by' => User::factory(),
        ];
    }
}
```

### Task M1-T11: Helper — `formatMoney`

**Files:**
- Modify: `app/Support/helpers.php` (or create if missing — check `composer.json` "files" autoload)

- [ ] **Step 1: Confirm `helpers.php` is autoloaded**

```powershell
php -r "echo function_exists('settings') ? 'yes' : 'no';"
```
Expected: `yes` (proves Phase 1 helpers file exists and is loaded).

- [ ] **Step 2: Append helper**

```php
if (! function_exists('formatMoney')) {
    function formatMoney(int|float|null $cents, ?string $symbol = null): string
    {
        $symbol ??= settings('finance.currency_symbol', '$');
        $cents = (int) ($cents ?? 0);
        return $symbol.number_format($cents / 100, 2);
    }
}

if (! function_exists('weekday')) {
    function weekday(string $name): int
    {
        return match (strtolower($name)) {
            'sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
            'thursday' => 4, 'friday' => 5, 'saturday' => 6,
            default => 6,
        };
    }
}
```

### Task M1-T12: Seeder — default tithe funds

**Files:**
- Create: `database/seeders/TitheFundSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\TitheFund;
use Illuminate\Database\Seeder;

class TitheFundSeeder extends Seeder
{
    public function run(): void
    {
        $funds = [
            ['slug' => 'general',  'name' => 'General Offering', 'description' => 'General tithes and offerings for the church.', 'sort_order' => 1],
            ['slug' => 'missions', 'name' => 'Missions Fund',    'description' => 'Supports missionary work and outreach.',         'sort_order' => 2],
            ['slug' => 'building', 'name' => 'Building Fund',    'description' => 'Maintenance and expansion of facilities.',        'sort_order' => 3],
        ];

        foreach ($funds as $row) {
            TitheFund::updateOrCreate(['slug' => $row['slug']], $row + ['is_active' => true]);
        }
    }
}
```

- [ ] **Step 2: Register in `DatabaseSeeder.php`**

In the `run()` method add `$this->call(TitheFundSeeder::class);` after existing seeders.

- [ ] **Step 3: Run seeder**

```powershell
php artisan db:seed --class=TitheFundSeeder
```
Expected: 3 rows in `tithe_funds`.

### Task M1-T13: Seeder — `manage-tithes` permission + settings

**Files:**
- Modify: `database/seeders/PermissionSeeder.php` (or equivalent — find the Phase 1 seeder)
- Modify: `database/seeders/SettingsSeeder.php` (or equivalent)

- [ ] **Step 1: Locate Phase 1 seeders**

```powershell
php artisan tinker --execute="echo glob(database_path('seeders/*Seeder.php')) ? implode(PHP_EOL, glob(database_path('seeders/*Seeder.php'))) : 'none';"
```

- [ ] **Step 2: In the permission seeder**, after existing permissions, add:

```php
$manageTithes = Permission::firstOrCreate(['name' => 'manage-tithes', 'guard_name' => 'admin']);
Role::where('name', 'Site Admin')->first()?->givePermissionTo($manageTithes);
```

- [ ] **Step 3: In the settings seeder**, append these defaults:

```php
$defaults = [
    'finance.currency_symbol' => '$',
    'reminders.event_24h_enabled' => '1',
    'reminders.weekly_digest_enabled' => '1',
    'reminders.weekly_digest_day' => 'Saturday',
    'reminders.weekly_digest_hour' => '18',
    'reminders.admin_daily_digest_enabled' => '0',
];

foreach ($defaults as $key => $value) {
    \App\Models\AppSetting::firstOrCreate(['key' => $key], ['value' => $value]);
}
```
(Replace `AppSetting` with the actual settings model used in Phase 1 — check existing seeder.)

- [ ] **Step 4: Run seeders**

```powershell
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=SettingsSeeder
```

### Task M1-T14: M1 verification test

**Files:**
- Create: `tests/Feature/Phase3/SchemaTest.php`

- [ ] **Step 1: Write test**

```php
<?php

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRsvp;
use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;
use Spatie\Permission\Models\Permission;

it('has all four new tables', function () {
    expect(Schema::hasTable('event_rsvps'))->toBeTrue()
        ->and(Schema::hasTable('event_attendances'))->toBeTrue()
        ->and(Schema::hasTable('tithe_funds'))->toBeTrue()
        ->and(Schema::hasTable('tithes'))->toBeTrue();
});

it('adds new columns to events and users', function () {
    expect(Schema::hasColumn('events', 'checkin_code'))->toBeTrue()
        ->and(Schema::hasColumn('events', 'attendance_open'))->toBeTrue()
        ->and(Schema::hasColumn('events', 'reminded_at'))->toBeTrue()
        ->and(Schema::hasColumn('users', 'email_reminder_event_24h'))->toBeTrue()
        ->and(Schema::hasColumn('users', 'email_weekly_digest'))->toBeTrue()
        ->and(Schema::hasColumn('users', 'email_admin_daily_digest'))->toBeTrue();
});

it('factories create related models', function () {
    $rsvp = EventRsvp::factory()->create();
    expect($rsvp->event)->toBeInstanceOf(Event::class)
        ->and($rsvp->user)->toBeInstanceOf(User::class)
        ->and($rsvp->status)->toBe('going');

    $att = EventAttendance::factory()->walkIn('Jane')->create();
    expect($att->user_id)->toBeNull()
        ->and($att->guest_name)->toBe('Jane')
        ->and($att->method)->toBe('organizer');

    $tithe = Tithe::factory()->create();
    expect($tithe->amount_cents)->toBeInt()->toBeGreaterThan(0)
        ->and($tithe->fund)->toBeInstanceOf(TitheFund::class);
});

it('seeds default tithe funds and manage-tithes permission', function () {
    $this->seed(\Database\Seeders\TitheFundSeeder::class);

    expect(TitheFund::where('slug', 'general')->exists())->toBeTrue()
        ->and(TitheFund::where('slug', 'missions')->exists())->toBeTrue()
        ->and(TitheFund::where('slug', 'building')->exists())->toBeTrue();

    expect(Permission::where('name', 'manage-tithes')->where('guard_name', 'admin')->exists())->toBeTrue();
});

it('formats money using settings symbol', function () {
    expect(formatMoney(12345))->toBe('$123.45')
        ->and(formatMoney(0))->toBe('$0.00')
        ->and(formatMoney(null))->toBe('$0.00');
});

it('maps weekday names', function () {
    expect(weekday('Sunday'))->toBe(0)
        ->and(weekday('Saturday'))->toBe(6);
});
```

- [ ] **Step 2: Run tests**

```powershell
php artisan test --filter Phase3/SchemaTest
```
Expected: all green.

---

# Milestone 2 — Member RSVP UI

> **Goal of M2:** Logged-in members can RSVP on event detail pages, edit/cancel their RSVP, and see a "going" badge on the events index. Public visitors see a sign-in prompt.

### Task M2-T01: Add rate limiter `event-rsvp`

**Files:**
- Modify: `bootstrap/app.php` OR `app/Providers/RouteServiceProvider.php` — wherever existing limiters are defined (Phase 2 has `feed-react`, etc.).

- [ ] **Step 1: Locate existing limiters**

```powershell
php artisan tinker --execute="echo file_get_contents(base_path('bootstrap/app.php'));"
```
Find the call to `RateLimiter::for(...)`.

- [ ] **Step 2: Append a new limiter** alongside `feed-react`:

```php
RateLimiter::for('event-rsvp', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('checkin-submit', function (Request $request) {
    return Limit::perMinutes(5, 10)->by($request->ip());
});
```

### Task M2-T02: Controller — `MemberEventController`

**Files:**
- Create: `app/Http/Controllers/Member/EventController.php`

- [ ] **Step 1: Controller skeleton**

```php
<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function rsvp(Request $request, Event $event)
    {
        $data = $request->validate([
            'guest_count' => ['nullable', 'integer', 'min:0', 'max:5'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $rsvp = EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $request->user()->id],
            [
                'guest_count' => $data['guest_count'] ?? 0,
                'note' => $data['note'] ?? null,
                'status' => 'going',
            ],
        );

        return redirect()
            ->route('site.events.show', $event)
            ->with('status', 'You\'re going to '.$event->title.'.');
    }

    public function cancelRsvp(Request $request, Event $event)
    {
        EventRsvp::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->update(['status' => 'cancelled']);

        return redirect()
            ->route('site.events.show', $event)
            ->with('status', 'RSVP cancelled.');
    }
}
```

### Task M2-T03: Routes — member events

**Files:**
- Modify: `routes/member.php`

- [ ] **Step 1: Add inside the `auth:web` + `verified` + `prefix('member')` + `name('member.')` group**:

```php
// M2 Event RSVPs
Route::post('/events/{event:slug}/rsvp', [\App\Http\Controllers\Member\EventController::class, 'rsvp'])
    ->name('events.rsvp')->middleware('throttle:event-rsvp');
Route::delete('/events/{event:slug}/rsvp', [\App\Http\Controllers\Member\EventController::class, 'cancelRsvp'])
    ->name('events.cancel-rsvp');
```

- [ ] **Step 2: Verify routes**

```powershell
php artisan route:list | findstr events.rsvp
```
Expected: 2 routes listed.

### Task M2-T04: Public event-detail controller — pass `$myRsvp`

**Files:**
- Modify: `app/Http/Controllers/Site/EventController.php` (find the actual file via `grep "site.events.show" routes/web.php`)

- [ ] **Step 1: Locate**

```powershell
php artisan route:list --name=site.events.show
```

- [ ] **Step 2: In the `show` method**, extend the returned view data:

```php
public function show(\App\Models\Event $event)
{
    $myRsvp = auth()->check()
        ? \App\Models\EventRsvp::where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->first()
        : null;

    $goingCount = \App\Models\EventRsvp::where('event_id', $event->id)
        ->where('status', 'going')->count();

    $goingPreview = \App\Models\EventRsvp::with('user:id,name')
        ->where('event_id', $event->id)
        ->where('status', 'going')
        ->latest()
        ->limit(8)
        ->get()
        ->map(fn ($r) => explode(' ', $r->user->name ?? 'Friend')[0])
        ->all();

    return view('site.events.show', compact('event', 'myRsvp', 'goingCount', 'goingPreview'));
}
```

### Task M2-T05: RSVP card partial

**Files:**
- Create: `resources/views/site/events/_rsvp_card.blade.php`
- Modify: `resources/views/site/events/show.blade.php`

- [ ] **Step 1: Partial**

```blade
@php
    $going = $goingCount ?? 0;
    $names = $goingPreview ?? [];
    $extra = max(0, $going - count($names));
@endphp

<section class="mt-12 p-6 md:p-8 rounded-xl border border-[rgb(var(--border))] bg-white" id="rsvp">
    <h3 class="font-serif text-xl mb-4">RSVP for this event</h3>

    @guest('web')
        <p class="text-ink-muted text-sm mb-4">Sign in to let us know you're coming.</p>
        <a href="{{ route('login') }}?return={{ urlencode(request()->fullUrl()) }}" class="btn-primary">Sign in to RSVP</a>
    @else
        @if($myRsvp && $myRsvp->status === 'going')
            <p class="text-sm mb-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-secondary/15 text-brand-secondary text-xs font-semibold">✓ You're coming</span>
            </p>
            @if($myRsvp->guest_count > 0)
                <p class="text-sm text-ink-muted mb-3">Bringing {{ $myRsvp->guest_count }} guest{{ $myRsvp->guest_count === 1 ? '' : 's' }}.</p>
            @endif
            <details class="text-sm mb-3">
                <summary class="cursor-pointer text-brand-primary">Edit RSVP</summary>
                <form method="POST" action="{{ route('member.events.rsvp', $event) }}" class="mt-3 flex flex-col gap-3">
                    @csrf
                    <label class="flex items-center gap-3">
                        <span class="text-sm">Guests (incl. you):</span>
                        <select name="guest_count" class="border rounded px-2 py-1">
                            @for($i = 0; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected($myRsvp->guest_count === $i)>{{ $i }}</option>
                            @endfor
                        </select>
                    </label>
                    <textarea name="note" rows="2" class="border rounded p-2 text-sm" placeholder="Note (optional)">{{ $myRsvp->note }}</textarea>
                    <button class="btn-primary self-start">Save changes</button>
                </form>
            </details>
            <form method="POST" action="{{ route('member.events.cancel-rsvp', $event) }}">
                @csrf @method('DELETE')
                <button class="text-sm text-brand-primary underline">Cancel RSVP</button>
            </form>
        @else
            <form method="POST" action="{{ route('member.events.rsvp', $event) }}" class="flex flex-col gap-3">
                @csrf
                <label class="flex items-center gap-3">
                    <span class="text-sm">Bringing guests?</span>
                    <select name="guest_count" class="border rounded px-2 py-1">
                        @for($i = 0; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </label>
                <details class="text-sm">
                    <summary class="cursor-pointer text-brand-primary">Add a note (optional)</summary>
                    <textarea name="note" rows="2" class="border rounded p-2 text-sm w-full mt-2" maxlength="500"></textarea>
                </details>
                <button class="btn-primary self-start">I'll be there</button>
            </form>
        @endif
    @endguest

    @if($going > 0)
        <div class="mt-6 pt-4 border-t border-[rgb(var(--border))] text-sm text-ink-muted">
            <strong class="text-ink">{{ $going }}</strong> {{ Str::plural('person', $going) }} {{ $going === 1 ? 'is' : 'are' }} coming
            @if(count($names))
                — {{ implode(', ', $names) }}@if($extra) and {{ $extra }} {{ Str::plural('other', $extra) }}@endif.
            @endif
        </div>
    @endif
</section>
```

- [ ] **Step 2: Include partial** in `resources/views/site/events/show.blade.php` immediately after the event description block (search for the closing `</article>` of body content and insert before it):

```blade
@include('site.events._rsvp_card')
```

### Task M2-T06: RSVP badge on events index

**Files:**
- Modify: `resources/views/site/events/index.blade.php`

- [ ] **Step 1: At the top of the file**, add a small query to map RSVP'd event IDs for the current member:

```blade
@php
    $myRsvpIds = auth('web')->check()
        ? \App\Models\EventRsvp::where('user_id', auth('web')->id())
            ->where('status', 'going')
            ->pluck('event_id')->all()
        : [];
@endphp
```

- [ ] **Step 2: In each event card** (find the `@foreach($events as $e)` block), add a small footer:

```blade
@if(in_array($e->id, $myRsvpIds))
    <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full bg-brand-secondary/15 text-brand-secondary text-xs font-semibold">✓ You're going</span>
@elseif(auth('web')->check())
    <a href="{{ route('site.events.show', $e) }}#rsvp" class="text-xs text-brand-primary mt-2 inline-block">RSVP →</a>
@endif
```

### Task M2-T07: M2 feature tests

**Files:**
- Create: `tests/Feature/Phase3/MemberRsvpTest.php`

- [ ] **Step 1: Write tests**

```php
<?php

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;

beforeEach(function () {
    $this->member = User::factory()->create(['email_verified_at' => now()]);
    $this->event = Event::factory()->create(['starts_at' => now()->addDays(5)]);
});

it('lets a verified member RSVP', function () {
    $this->actingAs($this->member, 'web')
        ->post(route('member.events.rsvp', $this->event), ['guest_count' => 2])
        ->assertRedirect(route('site.events.show', $this->event));

    expect(EventRsvp::where('event_id', $this->event->id)
        ->where('user_id', $this->member->id)
        ->where('status', 'going')->first()->guest_count)->toBe(2);
});

it('updates an existing RSVP', function () {
    EventRsvp::factory()->create([
        'event_id' => $this->event->id, 'user_id' => $this->member->id, 'guest_count' => 1,
    ]);

    $this->actingAs($this->member, 'web')
        ->post(route('member.events.rsvp', $this->event), ['guest_count' => 4]);

    expect(EventRsvp::where('event_id', $this->event->id)
        ->where('user_id', $this->member->id)
        ->count())->toBe(1)
        ->and(EventRsvp::where('event_id', $this->event->id)
            ->where('user_id', $this->member->id)
            ->first()->guest_count)->toBe(4);
});

it('cancels an RSVP via DELETE', function () {
    EventRsvp::factory()->create([
        'event_id' => $this->event->id, 'user_id' => $this->member->id,
    ]);

    $this->actingAs($this->member, 'web')
        ->delete(route('member.events.cancel-rsvp', $this->event))
        ->assertRedirect();

    expect(EventRsvp::where('event_id', $this->event->id)
        ->where('user_id', $this->member->id)
        ->first()->status)->toBe('cancelled');
});

it('rejects guest_count over 5', function () {
    $this->actingAs($this->member, 'web')
        ->post(route('member.events.rsvp', $this->event), ['guest_count' => 99])
        ->assertSessionHasErrors('guest_count');
});

it('requires verified email', function () {
    $unverified = User::factory()->create(['email_verified_at' => null]);

    $this->actingAs($unverified, 'web')
        ->post(route('member.events.rsvp', $this->event))
        ->assertRedirect(route('verification.notice'));
});

it('shows going badge on event index for RSVP\'d events', function () {
    EventRsvp::factory()->create([
        'event_id' => $this->event->id, 'user_id' => $this->member->id,
    ]);

    $this->actingAs($this->member, 'web')
        ->get(route('site.events.index'))
        ->assertSee("You're going");
});

it('shows sign-in link to guests on event detail', function () {
    $this->get(route('site.events.show', $this->event))
        ->assertSee('Sign in to RSVP');
});

it('shows only first names in the going list', function () {
    $other = User::factory()->create(['name' => 'Sarah Chen']);
    EventRsvp::factory()->create(['event_id' => $this->event->id, 'user_id' => $other->id]);

    $response = $this->get(route('site.events.show', $this->event));
    $response->assertSee('Sarah');
    $response->assertDontSee('Sarah Chen');
});
```

- [ ] **Step 2: Run**

```powershell
php artisan test --filter Phase3/MemberRsvpTest
```
Expected: all green.

---

# Milestone 3 — Member self check-in flow

> **Goal of M3:** Member visits `/member/check-in`, enters 4-digit code, gets checked in. Idempotent, rate-limited, window-validated.

### Task M3-T01: Controller — `MemberCheckinController`

**Files:**
- Create: `app/Http/Controllers/Member/CheckinController.php`

- [ ] **Step 1: Controller**

```php
<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function show()
    {
        return view('member.checkin.show');
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:4'],
        ]);

        $event = Event::query()
            ->where('checkin_code', $data['code'])
            ->where('attendance_open', true)
            ->whereBetween('starts_at', [now()->subHours(4), now()->addHours(12)])
            ->first();

        if (! $event) {
            return back()->withErrors(['code' => 'That code doesn\'t match an event happening now. Double-check with an organizer.']);
        }

        $existing = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            return redirect()
                ->route('member.checkin.thanks', $existing)
                ->with('status', 'You\'re already checked in. ✓');
        }

        $attendance = EventAttendance::create([
            'event_id' => $event->id,
            'user_id' => $request->user()->id,
            'checked_in_at' => now(),
            'method' => 'self',
        ]);

        return redirect()->route('member.checkin.thanks', $attendance);
    }

    public function thanks(EventAttendance $attendance, Request $request)
    {
        abort_unless($attendance->user_id === $request->user()->id, 403);
        $attendance->load('event');
        return view('member.checkin.thanks', compact('attendance'));
    }
}
```

### Task M3-T02: Routes — check-in

**Files:**
- Modify: `routes/member.php`

- [ ] **Step 1: Add inside the member group**:

```php
// M3 Check-in
Route::get('/check-in',  [\App\Http\Controllers\Member\CheckinController::class, 'show'])->name('checkin.show');
Route::post('/check-in', [\App\Http\Controllers\Member\CheckinController::class, 'submit'])
    ->middleware('throttle:checkin-submit')
    ->name('checkin.submit');
Route::get('/check-in/thanks/{attendance}', [\App\Http\Controllers\Member\CheckinController::class, 'thanks'])
    ->name('checkin.thanks');
```

### Task M3-T03: Check-in show view

**Files:**
- Create: `resources/views/member/checkin/show.blade.php`

- [ ] **Step 1: View**

```blade
<x-member.layout title="Check in">
    <div class="max-w-md mx-auto px-4 py-12">
        <h1 class="font-serif text-2xl mb-2">Check in to an event</h1>
        <p class="text-ink-muted text-sm mb-8">Enter the 4-digit code shown at the door.</p>

        @if($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-800 text-sm">
                {{ $errors->first('code') }}
            </div>
        @endif

        @if(session('status'))
            <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('member.checkin.submit') }}"
              x-data="{ d:['','','',''], code(){ return this.d.join('') } }"
              class="flex flex-col items-center gap-6">
            @csrf
            <input type="hidden" name="code" :value="code()">

            <div class="flex gap-3" @keydown.backspace="
                const idx = Array.from($event.target.parentNode.children).indexOf($event.target);
                if (!$event.target.value && idx > 0) $event.target.parentNode.children[idx-1].focus();
            ">
                <template x-for="(_, i) in 4" :key="i">
                    <input type="text" inputmode="numeric" maxlength="1"
                           x-model="d[i]"
                           class="w-14 h-16 text-center text-2xl font-semibold border border-[rgb(var(--border))] rounded-lg focus:border-brand-primary focus:outline-none"
                           @input="
                               d[i] = $event.target.value.replace(/[^0-9]/g,'');
                               if (d[i] && i < 3) $event.target.parentNode.children[i+1].focus();
                           "
                           x-init="if(i===0) $nextTick(() => $el.focus())">
                </template>
            </div>

            <button type="submit" class="btn-primary w-full" :disabled="code().length !== 4" :class="code().length !== 4 && 'opacity-50'">Check in</button>
        </form>

        <p class="text-sm text-ink-muted text-center mt-8">
            If the code doesn't work, ask an organizer to add you manually.
        </p>
    </div>
</x-member.layout>
```

### Task M3-T04: Check-in thanks view

**Files:**
- Create: `resources/views/member/checkin/thanks.blade.php`

- [ ] **Step 1: View**

```blade
<x-member.layout title="Checked in">
    <div class="max-w-md mx-auto px-4 py-16 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-brand-secondary/15 text-brand-secondary flex items-center justify-center text-3xl">✓</div>
        <h1 class="font-serif text-2xl mb-2">{{ session('status', 'You\'re checked in') }}</h1>
        <p class="text-ink-muted mb-6">{{ $attendance->event->title }} · {{ $attendance->event->starts_at?->format('D, M j · g:i A') }}</p>

        <a href="{{ route('member.dashboard') }}" class="btn-primary">Back to dashboard</a>
    </div>
</x-member.layout>
```

### Task M3-T05: Conditional "Check in" item in member header

**Files:**
- Modify: `resources/views/components/member/header.blade.php`

- [ ] **Step 1: In the `@php` block at top**, after `$brand = ...`, add:

```php
$attendanceOpenToday = \App\Models\Event::query()
    ->where('attendance_open', true)
    ->whereBetween('starts_at', [now()->subHours(4), now()->addHours(12)])
    ->exists();
```

- [ ] **Step 2: In the `$links` array**, after the existing links, conditionally add:

```php
if ($attendanceOpenToday && \Illuminate\Support\Facades\Route::has('member.checkin.show')) {
    $links[] = [
        'url' => route('member.checkin.show'),
        'active' => request()->routeIs('member.checkin.*'),
        'label' => 'Check in',
    ];
}
```

### Task M3-T06: M3 feature tests

**Files:**
- Create: `tests/Feature/Phase3/MemberCheckinTest.php`

- [ ] **Step 1: Write tests**

```php
<?php

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\User;

beforeEach(function () {
    $this->member = User::factory()->create(['email_verified_at' => now()]);
    $this->event = Event::factory()->create([
        'starts_at' => now()->addHour(),
        'checkin_code' => '1234',
        'attendance_open' => true,
    ]);
});

it('checks in a member with valid code', function () {
    $this->actingAs($this->member, 'web')
        ->post(route('member.checkin.submit'), ['code' => '1234'])
        ->assertRedirectContains('/check-in/thanks/');

    expect(EventAttendance::where('event_id', $this->event->id)
        ->where('user_id', $this->member->id)
        ->where('method', 'self')->exists())->toBeTrue();
});

it('rejects bad code', function () {
    $this->actingAs($this->member, 'web')
        ->post(route('member.checkin.submit'), ['code' => '9999'])
        ->assertSessionHasErrors('code');

    expect(EventAttendance::count())->toBe(0);
});

it('rejects code when attendance closed', function () {
    $this->event->update(['attendance_open' => false]);

    $this->actingAs($this->member, 'web')
        ->post(route('member.checkin.submit'), ['code' => '1234'])
        ->assertSessionHasErrors('code');
});

it('rejects code outside the event window', function () {
    $this->event->update(['starts_at' => now()->addDays(3)]);

    $this->actingAs($this->member, 'web')
        ->post(route('member.checkin.submit'), ['code' => '1234'])
        ->assertSessionHasErrors('code');
});

it('is idempotent on duplicate check-in', function () {
    $existing = EventAttendance::factory()->create([
        'event_id' => $this->event->id,
        'user_id' => $this->member->id,
        'method' => 'self',
    ]);

    $this->actingAs($this->member, 'web')
        ->post(route('member.checkin.submit'), ['code' => '1234'])
        ->assertRedirect(route('member.checkin.thanks', $existing));

    expect(EventAttendance::where('event_id', $this->event->id)
        ->where('user_id', $this->member->id)
        ->count())->toBe(1);
});

it('rate-limits after 10 attempts', function () {
    for ($i = 1; $i <= 10; $i++) {
        $this->actingAs($this->member, 'web')
            ->post(route('member.checkin.submit'), ['code' => '0000']);
    }

    $this->actingAs($this->member, 'web')
        ->post(route('member.checkin.submit'), ['code' => '0000'])
        ->assertStatus(429);
});

it('thanks page is gated to the attendee', function () {
    $other = User::factory()->create();
    $attendance = EventAttendance::factory()->create([
        'event_id' => $this->event->id,
        'user_id' => $other->id,
    ]);

    $this->actingAs($this->member, 'web')
        ->get(route('member.checkin.thanks', $attendance))
        ->assertForbidden();
});
```

- [ ] **Step 2: Run tests**

```powershell
php artisan test --filter Phase3/MemberCheckinTest
```

---

# Milestone 4 — Member dashboard updates

> **Goal of M4:** Dashboard shows next 3 upcoming RSVPs and a conditional "Check in" quick-action button when an attendance window is open.

### Task M4-T01: Pass dashboard data from controller

**Files:**
- Modify: `app/Http/Controllers/Member/DashboardController.php`

- [ ] **Step 1: Extend `index()`**

```php
public function index()
{
    $user = auth()->user();

    $upcomingRsvps = \App\Models\EventRsvp::with(['event' => function ($q) {
            $q->where('starts_at', '>=', now())->orderBy('starts_at');
        }])
        ->where('user_id', $user->id)
        ->where('status', 'going')
        ->get()
        ->filter(fn ($r) => $r->event !== null)
        ->take(3);

    $hasOpenCheckin = \App\Models\Event::query()
        ->where('attendance_open', true)
        ->whereBetween('starts_at', [now()->subHours(4), now()->addHours(12)])
        ->exists();

    return view('member.dashboard', compact('user', 'upcomingRsvps', 'hasOpenCheckin'));
}
```

### Task M4-T02: Dashboard view updates

**Files:**
- Modify: `resources/views/member/dashboard.blade.php`

- [ ] **Step 1: Add "Your RSVPs" card** somewhere in the dashboard grid (Phase 2 has prayer/care/feed cards — slot this alongside):

```blade
<div class="card p-5">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-serif text-lg">Your RSVPs</h3>
        @if($hasOpenCheckin)
            <a href="{{ route('member.checkin.show') }}" class="text-sm font-semibold text-brand-primary">Check in →</a>
        @endif
    </div>

    @if($upcomingRsvps->isEmpty())
        <p class="text-ink-muted text-sm">You haven't RSVP'd to anything yet. <a href="{{ route('site.events.index') }}" class="text-brand-primary">Browse events</a>.</p>
    @else
        <ul class="space-y-3">
            @foreach($upcomingRsvps as $r)
                <li class="flex items-start justify-between">
                    <div>
                        <a href="{{ route('site.events.show', $r->event) }}" class="font-medium hover:text-brand-primary">{{ $r->event->title }}</a>
                        <p class="text-xs text-ink-muted">{{ $r->event->starts_at?->format('D, M j · g:i A') }}</p>
                    </div>
                    @if($r->guest_count > 0)
                        <span class="text-xs text-ink-muted">+{{ $r->guest_count }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
```

### Task M4-T03: M4 feature tests

**Files:**
- Create: `tests/Feature/Phase3/MemberDashboardTest.php`

- [ ] **Step 1: Write tests**

```php
<?php

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;

beforeEach(function () {
    $this->member = User::factory()->create(['email_verified_at' => now()]);
});

it('shows upcoming RSVPs on dashboard', function () {
    $event = Event::factory()->create(['title' => 'Sunday Service', 'starts_at' => now()->addDays(3)]);
    EventRsvp::factory()->create(['event_id' => $event->id, 'user_id' => $this->member->id]);

    $this->actingAs($this->member, 'web')
        ->get(route('member.dashboard'))
        ->assertSee('Sunday Service')
        ->assertSee('Your RSVPs');
});

it('hides check-in button when no open window', function () {
    Event::factory()->create(['attendance_open' => false]);

    $this->actingAs($this->member, 'web')
        ->get(route('member.dashboard'))
        ->assertDontSee('Check in →');
});

it('shows check-in button when event window is open', function () {
    Event::factory()->create([
        'attendance_open' => true,
        'starts_at' => now()->addHour(),
        'checkin_code' => '1234',
    ]);

    $this->actingAs($this->member, 'web')
        ->get(route('member.dashboard'))
        ->assertSee('Check in →');
});

it('omits past events from upcoming RSVPs', function () {
    $past = Event::factory()->create(['starts_at' => now()->subDays(5), 'title' => 'Past Event']);
    EventRsvp::factory()->create(['event_id' => $past->id, 'user_id' => $this->member->id]);

    $this->actingAs($this->member, 'web')
        ->get(route('member.dashboard'))
        ->assertDontSee('Past Event');
});
```

- [ ] **Step 2: Run tests**

```powershell
php artisan test --filter Phase3/MemberDashboardTest
```

---

# Milestone 5 — Admin attendance grid + walk-ins + CSV — **PAUSE FOR REVIEW**

> **Goal of M5:** Event Organizer or Site Admin opens an event in admin, sees Attendance tab with code generation, open/close toggle, live roster, walk-in modal, CSV export. Global attendance index at `/admin/attendance`.

### Task M5-T01: Authorization policy — `EventAttendancePolicy`

**Files:**
- Create: `app/Policies/EventAttendancePolicy.php`
- Modify: `app/Providers/AuthServiceProvider.php` (register the policy mapping)

- [ ] **Step 1: Policy**

```php
<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventAttendancePolicy
{
    public function manage(User $user, Event $event): bool
    {
        if ($user->hasRole('Site Admin')) return true;
        return $user->hasRole('Event Organizer') && $event->organizer_id === $user->id;
    }
}
```

- [ ] **Step 2: Register policy** in `AuthServiceProvider::boot()`:

```php
\Illuminate\Support\Facades\Gate::define('manage-event-attendance', [\App\Policies\EventAttendancePolicy::class, 'manage']);
```

### Task M5-T02: Controller — `AdminAttendanceController`

**Files:**
- Create: `app/Http/Controllers/Admin/AttendanceController.php`

- [ ] **Step 1: Controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->where(function ($q) {
                $q->where('attendance_open', true)
                  ->orWhereDate('starts_at', '>=', now()->subDays(30));
            })
            ->orderByDesc('starts_at')
            ->paginate(20);

        return view('admin.attendance.index', compact('events'));
    }

    public function show(Event $event)
    {
        Gate::authorize('manage-event-attendance', $event);

        $rsvps = $event->rsvps()->with('user')->where('status', 'going')->get();
        $attendances = $event->attendances()->with(['user', 'recorder'])->get();

        $rsvpCount = $rsvps->count();
        $checkedInCount = $attendances->where('user_id', '!=', null)->count();
        $walkInCount = $attendances->whereNull('user_id')->count();

        return view('admin.attendance.show', compact(
            'event', 'rsvps', 'attendances', 'rsvpCount', 'checkedInCount', 'walkInCount'
        ));
    }

    public function regenerateCode(Event $event)
    {
        Gate::authorize('manage-event-attendance', $event);
        $event->update(['checkin_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT)]);
        return back()->with('status', 'Check-in code regenerated.');
    }

    public function toggleOpen(Request $request, Event $event)
    {
        Gate::authorize('manage-event-attendance', $event);
        $event->update(['attendance_open' => (bool) $request->boolean('open')]);

        if ($event->attendance_open && ! $event->checkin_code) {
            $event->update(['checkin_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT)]);
        }

        return back()->with('status', $event->attendance_open ? 'Attendance is open.' : 'Attendance closed.');
    }

    public function store(Request $request, Event $event)
    {
        Gate::authorize('manage-event-attendance', $event);

        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'guest_name' => ['nullable', 'string', 'max:120'],
            'guest_count' => ['nullable', 'integer', 'min:0', 'max:5'],
        ]);

        if (! empty($data['user_id'])) {
            $existing = EventAttendance::where('event_id', $event->id)
                ->where('user_id', $data['user_id'])
                ->first();
            if ($existing) {
                return back()->with('status', 'Already marked.');
            }
        }

        EventAttendance::create([
            'event_id' => $event->id,
            'user_id' => $data['user_id'] ?? null,
            'guest_name' => $data['guest_name'] ?? null,
            'guest_count' => $data['guest_count'] ?? 0,
            'checked_in_at' => now(),
            'method' => 'organizer',
            'recorded_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Attendance recorded.');
    }

    public function destroy(Event $event, EventAttendance $attendance)
    {
        Gate::authorize('manage-event-attendance', $event);
        abort_unless($attendance->event_id === $event->id, 404);
        $attendance->delete();
        return back()->with('status', 'Attendance removed.');
    }

    public function export(Event $event): StreamedResponse
    {
        Gate::authorize('manage-event-attendance', $event);

        $filename = 'attendance-'.$event->slug.'-'.now()->toDateString().'.csv';

        return response()->streamDownload(function () use ($event) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'RSVP Guests', 'Checked In', 'Method', 'Checked In At', 'Note']);

            $rsvps = $event->rsvps()->with('user')->where('status', 'going')->get();
            $attendances = $event->attendances()->with('user')->get()->keyBy('user_id');

            foreach ($rsvps as $r) {
                $att = $attendances->get($r->user_id);
                fputcsv($out, [
                    self::csvSafe($r->user->name ?? ''),
                    $r->guest_count,
                    $att ? 'Yes' : 'No',
                    $att?->method ?? '',
                    $att?->checked_in_at?->toDateTimeString() ?? '',
                    self::csvSafe($r->note ?? ''),
                ]);
            }

            foreach ($event->attendances()->whereNull('user_id')->get() as $walk) {
                fputcsv($out, [
                    self::csvSafe($walk->guest_name ?: 'Guest'),
                    $walk->guest_count,
                    'Yes',
                    $walk->method,
                    $walk->checked_in_at?->toDateTimeString() ?? '',
                    '',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private static function csvSafe(string $value): string
    {
        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'".$value;
        }
        return $value;
    }
}
```

### Task M5-T03: Event model relations + scope

**Files:**
- Modify: `app/Models/Event.php`

- [ ] **Step 1: Add the new fillable + casts + relations**

In `$fillable` add: `'checkin_code', 'attendance_open', 'reminded_at'`.

In `$casts` add:
```php
'attendance_open' => 'boolean',
'reminded_at' => 'datetime',
```

Append these relations at the bottom of the model class:

```php
public function rsvps(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(\App\Models\EventRsvp::class);
}

public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(\App\Models\EventAttendance::class);
}
```

### Task M5-T04: Routes — admin attendance

**Files:**
- Modify: `routes/admin.php`

- [ ] **Step 1: Add inside the existing admin auth + permission group**:

```php
// M5 Attendance
Route::get('/events/{event}/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])
    ->name('events.attendance');
Route::post('/events/{event}/checkin-code/regenerate', [\App\Http\Controllers\Admin\AttendanceController::class, 'regenerateCode'])
    ->name('events.checkin-code.regenerate');
Route::put('/events/{event}/attendance-open', [\App\Http\Controllers\Admin\AttendanceController::class, 'toggleOpen'])
    ->name('events.attendance.toggle');
Route::post('/events/{event}/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])
    ->name('events.attendance.store');
Route::delete('/events/{event}/attendance/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'destroy'])
    ->name('events.attendance.destroy');
Route::get('/events/{event}/attendance/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'export'])
    ->name('events.attendance.export');
Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
```

- [ ] **Step 2: Remove** the Phase 2 stub route for attendance that pointed to `admin.stubs.phase3`. Search:
```powershell
php artisan route:list | findstr stubs
```
Update any sidebar links pointing to `attendance.index` stub to the new real route.

### Task M5-T05: Admin attendance show view (roster + controls)

**Files:**
- Create: `resources/views/admin/attendance/show.blade.php`

- [ ] **Step 1: View**

```blade
<x-admin.layout :title="'Attendance · '.$event->title">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.events.edit', $event) }}" class="text-sm text-brand-primary">← Back to event</a>
            <h1 class="font-serif text-2xl mt-1">{{ $event->title }} — Attendance</h1>
            <p class="text-sm text-ink-muted">{{ $event->starts_at?->format('D, M j · g:i A') }}</p>
        </div>
        <a href="{{ route('admin.events.attendance.export', $event) }}" class="btn-secondary text-sm">Export CSV</a>
    </div>

    @if(session('status'))
        <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('status') }}</div>
    @endif

    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Check-in code</p>
            <p class="font-mono text-3xl font-semibold mt-1">{{ $event->checkin_code ?: '—' }}</p>
            <form method="POST" action="{{ route('admin.events.checkin-code.regenerate', $event) }}" class="mt-2">
                @csrf
                <button class="text-xs text-brand-primary underline">Regenerate</button>
            </form>
        </div>

        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Check-in window</p>
            <form method="POST" action="{{ route('admin.events.attendance.toggle', $event) }}" class="mt-2 flex items-center gap-3">
                @csrf @method('PUT')
                <input type="hidden" name="open" value="{{ $event->attendance_open ? 0 : 1 }}">
                <button class="btn-{{ $event->attendance_open ? 'secondary' : 'primary' }} text-sm">
                    {{ $event->attendance_open ? 'Close check-in' : 'Open check-in' }}
                </button>
                <span class="text-sm {{ $event->attendance_open ? 'text-green-700' : 'text-ink-muted' }}">
                    {{ $event->attendance_open ? '● Open' : '○ Closed' }}
                </span>
            </form>
        </div>

        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Counts</p>
            <p class="text-sm mt-2">
                <strong>{{ $rsvpCount }}</strong> RSVPs ·
                <strong>{{ $checkedInCount }}</strong> checked in ·
                <strong>{{ $walkInCount }}</strong> walk-ins
            </p>
        </div>
    </div>

    <div class="card p-4" x-data="{ openModal: false }">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-serif text-lg">Roster</h2>
            <button @click="openModal = true" class="btn-secondary text-sm">+ Add walk-in</button>
        </div>

        <table class="w-full text-sm">
            <thead class="text-left text-ink-muted border-b border-[rgb(var(--border))]">
                <tr>
                    <th class="py-2 w-10">✓</th>
                    <th class="py-2">Name</th>
                    <th class="py-2">+Gst</th>
                    <th class="py-2">RSVP'd</th>
                    <th class="py-2">Method</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $attByUser = $attendances->whereNotNull('user_id')->keyBy('user_id');
                    $walkIns   = $attendances->whereNull('user_id');
                @endphp

                @foreach($rsvps as $r)
                    @php $att = $attByUser->get($r->user_id); @endphp
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2">
                            @if($att)
                                <form method="POST" action="{{ route('admin.events.attendance.destroy', [$event, $att]) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-green-700">✓</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.events.attendance.store', $event) }}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $r->user_id }}">
                                    <button class="text-ink-muted">☐</button>
                                </form>
                            @endif
                        </td>
                        <td class="py-2">{{ $r->user->name ?? '—' }}</td>
                        <td class="py-2">{{ $r->guest_count }}</td>
                        <td class="py-2">{{ $r->created_at->diffForHumans() }}</td>
                        <td class="py-2">{{ $att?->method ?? '—' }}</td>
                    </tr>
                @endforeach

                @foreach($walkIns as $w)
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2 text-green-700">✓</td>
                        <td class="py-2">{{ $w->guest_name ?: 'Guest' }} <span class="text-xs text-ink-muted">(walk-in)</span></td>
                        <td class="py-2">{{ $w->guest_count }}</td>
                        <td class="py-2">—</td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('admin.events.attendance.destroy', [$event, $w]) }}" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 underline">remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Walk-in modal --}}
        <div x-show="openModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" @click="openModal = false"></div>
            <div class="relative bg-white rounded-xl p-6 w-full max-w-md">
                <h3 class="font-serif text-lg mb-4">Add walk-in</h3>
                <form method="POST" action="{{ route('admin.events.attendance.store', $event) }}" class="flex flex-col gap-3">
                    @csrf
                    <label class="text-sm">
                        Name <span class="text-ink-muted">(optional)</span>
                        <input type="text" name="guest_name" class="border rounded p-2 w-full mt-1" placeholder="Guest">
                    </label>
                    <label class="text-sm">
                        Additional guests
                        <select name="guest_count" class="border rounded p-2 w-full mt-1">
                            @for($i = 0; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </label>
                    <div class="flex gap-2 justify-end mt-2">
                        <button type="button" @click="openModal = false" class="text-sm">Cancel</button>
                        <button class="btn-primary text-sm">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin.layout>
```

### Task M5-T06: Admin attendance index view

**Files:**
- Create: `resources/views/admin/attendance/index.blade.php`

- [ ] **Step 1: View**

```blade
<x-admin.layout title="Attendance">
    <h1 class="font-serif text-2xl mb-6">Attendance</h1>

    <div class="card p-4">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-muted border-b border-[rgb(var(--border))]">
                <tr>
                    <th class="py-2">Event</th>
                    <th class="py-2">Date</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Code</th>
                    <th class="py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $e)
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2">{{ $e->title }}</td>
                        <td class="py-2">{{ $e->starts_at?->format('D, M j · g:i A') }}</td>
                        <td class="py-2">
                            <span class="{{ $e->attendance_open ? 'text-green-700' : 'text-ink-muted' }}">
                                {{ $e->attendance_open ? '● Open' : '○ Closed' }}
                            </span>
                        </td>
                        <td class="py-2 font-mono">{{ $e->checkin_code ?: '—' }}</td>
                        <td class="py-2 text-right">
                            <a href="{{ route('admin.events.attendance', $e) }}" class="text-brand-primary text-sm">Manage →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-ink-muted">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $events->links() }}</div>
</x-admin.layout>
```

### Task M5-T07: Wire admin event-edit "Attendance" link

**Files:**
- Modify: `resources/views/admin/events/edit.blade.php` (or `_form.blade.php`)

- [ ] **Step 1: Add a tab/link** near the page heading:

```blade
<a href="{{ route('admin.events.attendance', $event) }}" class="text-sm text-brand-primary">Manage attendance →</a>
```

### Task M5-T08: Sidebar — wire real attendance link

**Files:**
- Modify: `resources/views/components/admin/sidebar.blade.php` (or wherever the menu lives)

- [ ] **Step 1: Replace** the existing "Attendance" stub link's `href="#"` with `href="{{ route('admin.attendance.index') }}"` and gate by `@can('view-events')` or whatever existing capability fits.

### Task M5-T09: M5 feature tests

**Files:**
- Create: `tests/Feature/Phase3/AdminAttendanceTest.php`

- [ ] **Step 1: Write tests**

```php
<?php

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRsvp;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    $this->siteAdmin = User::factory()->create(['is_admin' => true]);
    $this->siteAdmin->assignRole('Site Admin');

    $this->organizer = User::factory()->create(['is_admin' => true]);
    $this->organizer->assignRole('Event Organizer');

    $this->event = Event::factory()->create(['organizer_id' => $this->organizer->id]);
});

it('regenerates check-in code', function () {
    $this->actingAs($this->siteAdmin, 'admin')
        ->post(route('admin.events.checkin-code.regenerate', $this->event))
        ->assertRedirect();

    expect($this->event->fresh()->checkin_code)->toMatch('/^\d{4}$/');
});

it('toggles attendance open and assigns code automatically', function () {
    expect($this->event->checkin_code)->toBeNull();

    $this->actingAs($this->siteAdmin, 'admin')
        ->put(route('admin.events.attendance.toggle', $this->event), ['open' => 1]);

    $fresh = $this->event->fresh();
    expect($fresh->attendance_open)->toBeTrue()
        ->and($fresh->checkin_code)->toMatch('/^\d{4}$/');
});

it('marks a member attendance via organizer', function () {
    $member = User::factory()->create();
    EventRsvp::factory()->create(['event_id' => $this->event->id, 'user_id' => $member->id]);

    $this->actingAs($this->organizer, 'admin')
        ->post(route('admin.events.attendance.store', $this->event), ['user_id' => $member->id]);

    expect(EventAttendance::where('event_id', $this->event->id)
        ->where('user_id', $member->id)
        ->where('method', 'organizer')
        ->where('recorded_by', $this->organizer->id)
        ->exists())->toBeTrue();
});

it('records a walk-in', function () {
    $this->actingAs($this->organizer, 'admin')
        ->post(route('admin.events.attendance.store', $this->event), [
            'guest_name' => 'Jane Doe',
            'guest_count' => 2,
        ]);

    expect(EventAttendance::where('guest_name', 'Jane Doe')->where('guest_count', 2)->exists())->toBeTrue();
});

it('forbids organizer from managing someone else\'s event', function () {
    $other = User::factory()->create(['is_admin' => true]);
    $other->assignRole('Event Organizer');
    $otherEvent = Event::factory()->create(['organizer_id' => $other->id]);

    $this->actingAs($this->organizer, 'admin')
        ->get(route('admin.events.attendance', $otherEvent))
        ->assertForbidden();
});

it('exports CSV with header row', function () {
    EventAttendance::factory()->create(['event_id' => $this->event->id]);

    $resp = $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.events.attendance.export', $this->event));

    $resp->assertOk()
        ->assertHeader('content-type', 'text/csv');

    $content = $resp->streamedContent();
    expect($content)->toContain('Name,RSVP Guests,Checked In,Method,Checked In At,Note');
});

it('csv-safe prefixes injection characters', function () {
    $controller = new \App\Http\Controllers\Admin\AttendanceController();
    $ref = new ReflectionMethod($controller, 'csvSafe');
    $ref->setAccessible(true);
    expect($ref->invoke($controller, '=cmd|" /C calc"!A0'))->toStartWith("'=");
});
```

- [ ] **Step 2: Run**

```powershell
php artisan test --filter Phase3/AdminAttendanceTest
```

### Task M5-T10: **PAUSE — UX review checkpoint**

- [ ] **Step 1:** Start dev server and walk through:
  - Open `/admin/events/{id}/attendance` as Site Admin.
  - Toggle check-in open. Confirm code appears.
  - Add a walk-in. Confirm row appears.
  - Mark/unmark a member attendance. Confirm count badge updates.
  - Export CSV. Confirm file downloads with header.
  - Visit as Event Organizer of a different event → 403 expected.
- [ ] **Step 2:** Present screenshots to user. Wait for "approved" before moving to M6.

---

# Milestone 6 — Tithes CRUD + funds management

> **Goal of M6:** Site Admin can record gifts, see them in a sortable index, edit, delete, export, and manage tithe funds.

### Task M6-T01: Controller — `AdminTitheController`

**Files:**
- Create: `app/Http/Controllers/Admin/TitheController.php`

- [ ] **Step 1: Controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TitheController extends Controller
{
    public function index(Request $request)
    {
        $q = Tithe::with(['user', 'fund', 'recorder'])->latest('received_at');

        if ($fund = $request->input('fund')) $q->where('fund_id', $fund);
        if ($method = $request->input('method')) $q->where('method', $method);
        if ($search = $request->input('q')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('giver_name', 'like', "%$search%")
                   ->orWhere('reference', 'like', "%$search%")
                   ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$search%"));
            });
        }

        return view('admin.tithes.index', [
            'tithes' => $q->paginate(25)->withQueryString(),
            'funds' => TitheFund::active()->get(),
            'filters' => $request->only(['fund', 'method', 'q']),
        ]);
    }

    public function create()
    {
        return view('admin.tithes.create', [
            'funds' => TitheFund::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['amount_cents'] = (int) round(((float) $data['amount']) * 100);
        unset($data['amount']);

        if (! empty($data['user_id'])) {
            $data['giver_name'] = User::find($data['user_id'])?->name;
        }

        $data['note'] = $data['note'] ? Purifier::clean($data['note'], 'cms') : null;
        $data['recorded_by'] = $request->user()->id;

        Tithe::create($data);

        return redirect()->route('admin.tithes.index')->with('status', 'Gift recorded.');
    }

    public function edit(Tithe $tithe)
    {
        return view('admin.tithes.edit', [
            'tithe' => $tithe,
            'funds' => TitheFund::active()->get(),
        ]);
    }

    public function update(Request $request, Tithe $tithe)
    {
        $data = $this->validateData($request);
        $data['amount_cents'] = (int) round(((float) $data['amount']) * 100);
        unset($data['amount']);

        if (! empty($data['user_id'])) {
            $data['giver_name'] = User::find($data['user_id'])?->name;
        }

        $data['note'] = $data['note'] ? Purifier::clean($data['note'], 'cms') : null;

        $tithe->update($data);

        return redirect()->route('admin.tithes.index')->with('status', 'Gift updated.');
    }

    public function destroy(Tithe $tithe)
    {
        $tithe->delete();
        return back()->with('status', 'Gift deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $q = Tithe::with(['user', 'fund', 'recorder'])->latest('received_at');
        if ($fund = $request->input('fund')) $q->where('fund_id', $fund);

        $filename = 'tithes-'.now()->toDateString().'.csv';

        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Giver', 'Fund', 'Method', 'Amount', 'Reference', 'Recorded By']);
            foreach ($q->cursor() as $t) {
                fputcsv($out, [
                    $t->received_at?->toDateString(),
                    self::csvSafe($t->giverDisplayName()),
                    $t->fund->name,
                    $t->method,
                    number_format($t->amount_cents / 100, 2),
                    self::csvSafe((string) $t->reference),
                    self::csvSafe($t->recorder?->name ?? ''),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'giver_name' => ['nullable', 'string', 'max:160'],
            'fund_id' => ['required', 'integer', 'exists:tithe_funds,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,bank_transfer,cheque,other'],
            'received_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private static function csvSafe(string $value): string
    {
        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'".$value;
        }
        return $value;
    }
}
```

### Task M6-T02: Controller — `AdminTitheFundController`

**Files:**
- Create: `app/Http/Controllers/Admin/TitheFundController.php`

- [ ] **Step 1: Controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TitheFund;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TitheFundController extends Controller
{
    public function index()
    {
        return view('admin.tithes.funds.index', [
            'funds' => TitheFund::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.tithes.funds.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:tithe_funds,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        TitheFund::create($data);

        return redirect()->route('admin.tithes.funds.index')->with('status', 'Fund created.');
    }

    public function edit(TitheFund $fund)
    {
        return view('admin.tithes.funds.edit', compact('fund'));
    }

    public function update(Request $request, TitheFund $fund)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:tithe_funds,name,'.$fund->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $fund->update($data);

        return redirect()->route('admin.tithes.funds.index')->with('status', 'Fund updated.');
    }

    public function destroy(TitheFund $fund)
    {
        if ($fund->tithes()->exists()) {
            $fund->update(['is_active' => false]);
            return back()->with('status', 'Fund deactivated (preserves historical records).');
        }

        $fund->delete();
        return back()->with('status', 'Fund deleted.');
    }
}
```

### Task M6-T03: Member typeahead endpoint

**Files:**
- Modify: `app/Http/Controllers/Admin/TitheController.php` — add a `giverSearch` method
- Or create: `app/Http/Controllers/Admin/MemberLookupController.php`

- [ ] **Step 1: Controller (separate, simpler)**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberLookupController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) return response()->json([]);

        return response()->json(
            User::where('name', 'like', "%$q%")
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name'])
        );
    }
}
```

### Task M6-T04: Routes — tithes + funds + lookup

**Files:**
- Modify: `routes/admin.php`

- [ ] **Step 1: Add inside the admin auth group**:

```php
// M6 Tithes (Site Admin only)
Route::middleware('can:manage-tithes')->prefix('tithes')->name('tithes.')->group(function () {
    Route::resource('funds', \App\Http\Controllers\Admin\TitheFundController::class)->except(['show']);
    Route::get('export', [\App\Http\Controllers\Admin\TitheController::class, 'export'])->name('export');
    Route::get('member-lookup', [\App\Http\Controllers\Admin\MemberLookupController::class, 'search'])->name('member-lookup');
    Route::get('/',                 [\App\Http\Controllers\Admin\TitheController::class, 'index'])->name('index');
    Route::get('/create',           [\App\Http\Controllers\Admin\TitheController::class, 'create'])->name('create');
    Route::post('/',                [\App\Http\Controllers\Admin\TitheController::class, 'store'])->name('store');
    Route::get('/{tithe}/edit',     [\App\Http\Controllers\Admin\TitheController::class, 'edit'])->name('edit');
    Route::put('/{tithe}',          [\App\Http\Controllers\Admin\TitheController::class, 'update'])->name('update');
    Route::delete('/{tithe}',       [\App\Http\Controllers\Admin\TitheController::class, 'destroy'])->name('destroy');
});
```

### Task M6-T05: Tithes index view

**Files:**
- Create: `resources/views/admin/tithes/index.blade.php`

- [ ] **Step 1: View**

```blade
<x-admin.layout title="Tithes">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-2xl">Tithes</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.tithes.export', request()->only(['fund'])) }}" class="btn-secondary text-sm">Export CSV</a>
            <a href="{{ route('admin.tithes.create') }}" class="btn-primary text-sm">+ Record gift</a>
        </div>
    </div>

    @if(session('status'))
        <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('status') }}</div>
    @endif

    <form method="GET" class="card p-4 mb-4 grid sm:grid-cols-3 gap-3">
        <select name="fund" class="border rounded p-2">
            <option value="">All funds</option>
            @foreach($funds as $f)
                <option value="{{ $f->id }}" @selected(($filters['fund'] ?? null) == $f->id)>{{ $f->name }}</option>
            @endforeach
        </select>
        <select name="method" class="border rounded p-2">
            <option value="">All methods</option>
            @foreach(['cash', 'bank_transfer', 'cheque', 'other'] as $m)
                <option value="{{ $m }}" @selected(($filters['method'] ?? null) === $m)>{{ ucfirst(str_replace('_', ' ', $m)) }}</option>
            @endforeach
        </select>
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search giver or reference" class="border rounded p-2">
        <div class="sm:col-span-3"><button class="btn-secondary text-sm">Filter</button></div>
    </form>

    <div class="card p-0 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-muted border-b border-[rgb(var(--border))]">
                <tr>
                    <th class="py-2 px-3">Date</th>
                    <th class="py-2 px-3">Giver</th>
                    <th class="py-2 px-3">Fund</th>
                    <th class="py-2 px-3">Method</th>
                    <th class="py-2 px-3 text-right">Amount</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tithes as $t)
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2 px-3">{{ $t->received_at->format('M j, Y') }}</td>
                        <td class="py-2 px-3">
                            {{ $t->giverDisplayName() }}
                            @if($t->user_id)<span class="text-xs text-brand-secondary ml-1">(member)</span>@endif
                        </td>
                        <td class="py-2 px-3">{{ $t->fund->name }}</td>
                        <td class="py-2 px-3">{{ ucfirst(str_replace('_', ' ', $t->method)) }}</td>
                        <td class="py-2 px-3 text-right font-mono">{{ formatMoney($t->amount_cents) }}</td>
                        <td class="py-2 px-3 text-right">
                            <a href="{{ route('admin.tithes.edit', $t) }}" class="text-brand-primary text-xs">edit</a>
                            <form method="POST" action="{{ route('admin.tithes.destroy', $t) }}" class="inline" onsubmit="return confirm('Delete this gift record?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 text-xs ml-2">delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 px-3 text-ink-muted">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tithes->links() }}</div>
</x-admin.layout>
```

### Task M6-T06: Tithes create/edit form partial + views

**Files:**
- Create: `resources/views/admin/tithes/_form.blade.php`
- Create: `resources/views/admin/tithes/create.blade.php`
- Create: `resources/views/admin/tithes/edit.blade.php`

- [ ] **Step 1: Form partial**

```blade
@php $editing = isset($tithe); @endphp

<form method="POST" action="{{ $editing ? route('admin.tithes.update', $tithe) : route('admin.tithes.store') }}" class="card p-6 flex flex-col gap-4 max-w-xl">
    @csrf
    @if($editing) @method('PUT') @endif

    <div x-data="giverPicker(@js($editing ? ['id' => $tithe->user_id, 'name' => $tithe->giverDisplayName()] : null))" class="flex flex-col gap-1">
        <label class="text-sm font-medium">Giver</label>
        <input type="hidden" name="user_id" :value="picked.id || ''">
        <input type="hidden" name="giver_name" :value="picked.id ? '' : query">
        <input type="text" x-model="query" @input.debounce.300="search" placeholder="Search member or type a name" class="border rounded p-2">
        <ul x-show="results.length" class="border rounded mt-1 bg-white shadow text-sm">
            <template x-for="r in results" :key="r.id">
                <li @click="pick(r)" class="px-3 py-1 cursor-pointer hover:bg-surface" x-text="r.name"></li>
            </template>
        </ul>
        <p class="text-xs text-ink-muted">Members get linked automatically; free-text names are saved as-is.</p>
    </div>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium">Fund</span>
        <select name="fund_id" class="border rounded p-2" required>
            @foreach($funds as $f)
                <option value="{{ $f->id }}" @selected(($tithe->fund_id ?? '') === $f->id)>{{ $f->name }}</option>
            @endforeach
        </select>
    </label>

    <div class="grid sm:grid-cols-2 gap-4">
        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Amount ({{ settings('finance.currency_symbol', '$') }})</span>
            <input type="number" name="amount" step="0.01" min="0.01" value="{{ $editing ? number_format($tithe->amount_cents / 100, 2, '.', '') : old('amount') }}" class="border rounded p-2" required>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Method</span>
            <select name="method" class="border rounded p-2" required>
                @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank transfer', 'cheque' => 'Cheque', 'other' => 'Other'] as $k => $v)
                    <option value="{{ $k }}" @selected(($tithe->method ?? '') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Received at</span>
            <input type="date" name="received_at" value="{{ $editing ? $tithe->received_at->toDateString() : now()->toDateString() }}" class="border rounded p-2" required>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Reference (cheque #, bank ref)</span>
            <input type="text" name="reference" value="{{ $tithe->reference ?? '' }}" class="border rounded p-2">
        </label>
    </div>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium">Note</span>
        <textarea name="note" rows="3" class="border rounded p-2">{{ $tithe->note ?? '' }}</textarea>
    </label>

    <div class="flex gap-2 justify-end">
        <a href="{{ route('admin.tithes.index') }}" class="text-sm">Cancel</a>
        <button class="btn-primary text-sm">{{ $editing ? 'Save changes' : 'Record gift' }}</button>
    </div>
</form>

@push('scripts')
<script>
function giverPicker(initial) {
    return {
        query: initial?.name || '',
        picked: initial || { id: null, name: '' },
        results: [],
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            const r = await fetch(`{{ route('admin.tithes.member-lookup') }}?q=${encodeURIComponent(this.query)}`, { headers: { Accept: 'application/json' } });
            this.results = await r.json();
        },
        pick(r) { this.picked = r; this.query = r.name; this.results = []; }
    };
}
</script>
@endpush
```

- [ ] **Step 2: Create view**

```blade
<x-admin.layout title="Record gift">
    <h1 class="font-serif text-2xl mb-6">Record gift</h1>
    @include('admin.tithes._form')
</x-admin.layout>
```

- [ ] **Step 3: Edit view**

```blade
<x-admin.layout title="Edit gift">
    <h1 class="font-serif text-2xl mb-6">Edit gift</h1>
    @include('admin.tithes._form')
</x-admin.layout>
```

### Task M6-T07: Funds CRUD views

**Files:**
- Create: `resources/views/admin/tithes/funds/index.blade.php`
- Create: `resources/views/admin/tithes/funds/create.blade.php`
- Create: `resources/views/admin/tithes/funds/edit.blade.php`
- Create: `resources/views/admin/tithes/funds/_form.blade.php`

- [ ] **Step 1: Funds index**

```blade
<x-admin.layout title="Funds">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-2xl">Tithe funds</h1>
        <a href="{{ route('admin.tithes.funds.create') }}" class="btn-primary text-sm">+ New fund</a>
    </div>

    @if(session('status'))
        <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('status') }}</div>
    @endif

    <div class="card p-0 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-muted border-b border-[rgb(var(--border))]">
                <tr>
                    <th class="py-2 px-3">Name</th>
                    <th class="py-2 px-3">Slug</th>
                    <th class="py-2 px-3">Status</th>
                    <th class="py-2 px-3">Sort</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($funds as $f)
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2 px-3">{{ $f->name }}</td>
                        <td class="py-2 px-3 font-mono text-xs">{{ $f->slug }}</td>
                        <td class="py-2 px-3">{{ $f->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="py-2 px-3">{{ $f->sort_order }}</td>
                        <td class="py-2 px-3 text-right">
                            <a href="{{ route('admin.tithes.funds.edit', $f) }}" class="text-brand-primary text-xs">edit</a>
                            <form method="POST" action="{{ route('admin.tithes.funds.destroy', $f) }}" class="inline" onsubmit="return confirm('Delete or deactivate fund?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 text-xs ml-2">delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin.layout>
```

- [ ] **Step 2: Form partial**

```blade
@php $editing = isset($fund); @endphp

<form method="POST" action="{{ $editing ? route('admin.tithes.funds.update', $fund) : route('admin.tithes.funds.store') }}" class="card p-6 max-w-xl flex flex-col gap-4">
    @csrf
    @if($editing) @method('PUT') @endif

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium">Name</span>
        <input type="text" name="name" value="{{ $fund->name ?? old('name') }}" class="border rounded p-2" required>
    </label>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium">Description</span>
        <textarea name="description" rows="3" class="border rounded p-2">{{ $fund->description ?? '' }}</textarea>
    </label>

    <div class="grid sm:grid-cols-2 gap-4">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked($editing ? $fund->is_active : true)>
            Active
        </label>
        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Sort order</span>
            <input type="number" name="sort_order" value="{{ $fund->sort_order ?? 0 }}" class="border rounded p-2">
        </label>
    </div>

    <div class="flex gap-2 justify-end">
        <a href="{{ route('admin.tithes.funds.index') }}" class="text-sm">Cancel</a>
        <button class="btn-primary text-sm">{{ $editing ? 'Save' : 'Create' }}</button>
    </div>
</form>
```

- [ ] **Step 3: Create + Edit views**

```blade
{{-- create.blade.php --}}
<x-admin.layout title="New fund">
    <h1 class="font-serif text-2xl mb-6">New fund</h1>
    @include('admin.tithes.funds._form')
</x-admin.layout>

{{-- edit.blade.php --}}
<x-admin.layout title="Edit fund">
    <h1 class="font-serif text-2xl mb-6">Edit fund</h1>
    @include('admin.tithes.funds._form')
</x-admin.layout>
```

### Task M6-T08: Sidebar — wire Finance group

**Files:**
- Modify: `resources/views/components/admin/sidebar.blade.php`

- [ ] **Step 1: Add a Finance group visible only to `manage-tithes`**:

```blade
@can('manage-tithes')
    <div class="px-3 mt-6 text-xs uppercase text-ink-muted">Finance</div>
    <a href="{{ route('admin.tithes.index') }}" class="block px-3 py-2 hover:bg-surface rounded text-sm">Tithes</a>
    <a href="{{ route('admin.tithes.funds.index') }}" class="block px-3 py-2 hover:bg-surface rounded text-sm">Funds</a>
@endcan
```

### Task M6-T09: M6 feature tests

**Files:**
- Create: `tests/Feature/Phase3/AdminTitheTest.php`

- [ ] **Step 1: Write tests**

```php
<?php

use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    $this->seed(\Database\Seeders\TitheFundSeeder::class);

    $this->siteAdmin = User::factory()->create(['is_admin' => true]);
    $this->siteAdmin->assignRole('Site Admin');

    $this->organizer = User::factory()->create(['is_admin' => true]);
    $this->organizer->assignRole('Event Organizer');

    $this->fund = TitheFund::where('slug', 'general')->first();
});

it('records a member tithe and stores amount as cents', function () {
    $member = User::factory()->create(['name' => 'Sarah Chen']);

    $this->actingAs($this->siteAdmin, 'admin')
        ->post(route('admin.tithes.store'), [
            'user_id' => $member->id,
            'fund_id' => $this->fund->id,
            'amount' => '50.00',
            'method' => 'cash',
            'received_at' => now()->toDateString(),
        ])
        ->assertRedirect(route('admin.tithes.index'));

    $tithe = Tithe::first();
    expect($tithe->amount_cents)->toBe(5000)
        ->and($tithe->giver_name)->toBe('Sarah Chen')
        ->and($tithe->user_id)->toBe($member->id);
});

it('records an anonymous tithe with giver_name only', function () {
    $this->actingAs($this->siteAdmin, 'admin')
        ->post(route('admin.tithes.store'), [
            'user_id' => null,
            'giver_name' => 'Anonymous',
            'fund_id' => $this->fund->id,
            'amount' => '100',
            'method' => 'bank_transfer',
            'received_at' => now()->toDateString(),
            'reference' => 'BANK-001',
        ]);

    $tithe = Tithe::first();
    expect($tithe->user_id)->toBeNull()
        ->and($tithe->giver_name)->toBe('Anonymous')
        ->and($tithe->reference)->toBe('BANK-001');
});

it('forbids Event Organizer from accessing tithes', function () {
    $this->actingAs($this->organizer, 'admin')
        ->get(route('admin.tithes.index'))
        ->assertForbidden();
});

it('member lookup returns id+name only', function () {
    User::factory()->create(['name' => 'Sarah Chen', 'email' => 'sarah@example.com']);

    $resp = $this->actingAs($this->siteAdmin, 'admin')
        ->getJson(route('admin.tithes.member-lookup', ['q' => 'Sarah']));

    $resp->assertOk();
    $row = $resp->json(0);
    expect(array_keys($row))->toBe(['id', 'name']);
});

it('member lookup needs 2+ chars', function () {
    User::factory()->create(['name' => 'Aaron']);

    $this->actingAs($this->siteAdmin, 'admin')
        ->getJson(route('admin.tithes.member-lookup', ['q' => 'A']))
        ->assertOk()
        ->assertExactJson([]);
});

it('deactivates fund instead of deleting when tithes reference it', function () {
    Tithe::factory()->create(['fund_id' => $this->fund->id]);

    $this->actingAs($this->siteAdmin, 'admin')
        ->delete(route('admin.tithes.funds.destroy', $this->fund))
        ->assertRedirect();

    expect($this->fund->fresh()->is_active)->toBeFalse()
        ->and(TitheFund::find($this->fund->id))->not->toBeNull();
});

it('hard-deletes fund when no tithes reference it', function () {
    $fund = TitheFund::factory()->create();

    $this->actingAs($this->siteAdmin, 'admin')
        ->delete(route('admin.tithes.funds.destroy', $fund));

    expect(TitheFund::find($fund->id))->toBeNull();
});

it('purifies note input', function () {
    $this->actingAs($this->siteAdmin, 'admin')
        ->post(route('admin.tithes.store'), [
            'fund_id' => $this->fund->id,
            'amount' => '10',
            'method' => 'cash',
            'received_at' => now()->toDateString(),
            'giver_name' => 'Bob',
            'note' => '<script>alert("xss")</script><b>bold</b>',
        ]);

    expect(Tithe::first()->note)->not->toContain('<script>')->toContain('<b>bold</b>');
});
```

- [ ] **Step 2: Run**

```powershell
php artisan test --filter Phase3/AdminTitheTest
```

---

# Milestone 7 — Reports dashboard + CSV exports

> **Goal of M7:** `/admin/reports` renders KPI cards, sparkline, fund breakdown, top events/givers. Two CSV exports stream. Gated by `view-reports`.

### Task M7-T01: Controller — `AdminReportsController`

**Files:**
- Create: `app/Http/Controllers/Admin/ReportsController.php`

- [ ] **Step 1: Controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        $cacheKey = "reports.{$request->user()->id}.{$from->toDateString()}.{$to->toDateString()}";

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($from, $to) {
            $memberCount = User::where('is_admin', false)->count();
            $memberDelta = User::where('is_admin', false)
                ->whereBetween('created_at', [$from, $to])->count();

            $eventsHeld = Event::whereBetween('starts_at', [$from, $to])->count();
            $avgAttendance = (int) round(
                EventAttendance::whereHas('event', fn ($q) => $q->whereBetween('starts_at', [$from, $to]))
                    ->count() / max(1, $eventsHeld)
            );

            $givingTotal = (int) Tithe::whereBetween('received_at', [$from->toDateString(), $to->toDateString()])
                ->sum('amount_cents');

            $byFund = Tithe::selectRaw('fund_id, SUM(amount_cents) AS total')
                ->whereBetween('received_at', [$from->toDateString(), $to->toDateString()])
                ->groupBy('fund_id')
                ->with('fund')
                ->get()
                ->map(fn ($r) => ['name' => $r->fund?->name ?? '—', 'total' => (int) $r->total])
                ->sortByDesc('total')->values();

            $topEvents = Event::withCount('attendances')
                ->whereBetween('starts_at', [$from, $to])
                ->orderByDesc('attendances_count')
                ->limit(5)->get(['id', 'title', 'starts_at']);

            $donorCount = Tithe::whereBetween('received_at', [$from->toDateString(), $to->toDateString()])
                ->distinct()
                ->count('user_id');

            $perEventAttendance = Event::withCount('attendances')
                ->whereBetween('starts_at', [$from, $to])
                ->orderBy('starts_at')
                ->get(['id', 'title', 'starts_at'])
                ->map(fn ($e) => ['label' => $e->starts_at?->format('M j'), 'value' => $e->attendances_count])
                ->all();

            return compact(
                'memberCount', 'memberDelta', 'eventsHeld', 'avgAttendance',
                'givingTotal', 'byFund', 'topEvents', 'donorCount', 'perEventAttendance'
            );
        });

        return view('admin.reports.index', array_merge($data, [
            'from' => $from, 'to' => $to,
            'preset' => $request->input('preset', '30'),
        ]));
    }

    public function attendanceCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Event', 'Date', 'RSVPs', 'Checked In', 'Walk-ins']);
            foreach (Event::with(['rsvps', 'attendances'])
                ->whereBetween('starts_at', [$from, $to])
                ->orderBy('starts_at')->cursor() as $e) {
                fputcsv($out, [
                    $e->title,
                    $e->starts_at?->toDateString(),
                    $e->rsvps->where('status', 'going')->count(),
                    $e->attendances->whereNotNull('user_id')->count(),
                    $e->attendances->whereNull('user_id')->count(),
                ]);
            }
            fclose($out);
        }, "attendance-{$from->toDateString()}-{$to->toDateString()}.csv", ['Content-Type' => 'text/csv']);
    }

    public function givingCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Fund', 'Method', 'Amount']);
            foreach (Tithe::with('fund')
                ->whereBetween('received_at', [$from->toDateString(), $to->toDateString()])
                ->orderBy('received_at')->cursor() as $t) {
                fputcsv($out, [
                    $t->received_at->toDateString(),
                    $t->fund->name,
                    $t->method,
                    number_format($t->amount_cents / 100, 2),
                ]);
            }
            fclose($out);
        }, "giving-{$from->toDateString()}-{$to->toDateString()}.csv", ['Content-Type' => 'text/csv']);
    }

    private function range(Request $request): array
    {
        $preset = $request->input('preset', '30');
        $to = $request->filled('to') ? \Carbon\Carbon::parse($request->input('to'))->endOfDay() : now()->endOfDay();
        $from = $request->filled('from')
            ? \Carbon\Carbon::parse($request->input('from'))->startOfDay()
            : (match ($preset) {
                '7' => now()->subDays(7)->startOfDay(),
                '30' => now()->subDays(30)->startOfDay(),
                '90' => now()->subDays(90)->startOfDay(),
                'ytd' => now()->startOfYear(),
                default => now()->subDays(30)->startOfDay(),
            });

        return [$from, $to];
    }
}
```

### Task M7-T02: Routes — reports

**Files:**
- Modify: `routes/admin.php`

- [ ] **Step 1: Add inside the admin auth group**:

```php
// M7 Reports
Route::middleware('can:view-reports')->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
    Route::get('/attendance.csv', [\App\Http\Controllers\Admin\ReportsController::class, 'attendanceCsv'])->name('attendance.csv');
    Route::get('/giving.csv',     [\App\Http\Controllers\Admin\ReportsController::class, 'givingCsv'])->name('giving.csv');
});
```

### Task M7-T03: Sparkline component

**Files:**
- Create: `resources/views/components/admin/sparkline.blade.php`

- [ ] **Step 1: Component**

```blade
@props(['data' => []])

@php
    $values = collect($data)->pluck('value')->all() ?: [0];
    $max = max($values);
    $width = 200;
    $height = 40;
    $points = collect($values)->map(function ($v, $i) use ($values, $width, $height, $max) {
        $x = (count($values) > 1) ? ($i / (count($values) - 1)) * $width : 0;
        $y = $height - ($max > 0 ? ($v / $max) * $height : 0);
        return $x.','.$y;
    })->implode(' ');
@endphp

<svg viewBox="0 0 {{ $width }} {{ $height }}" class="w-full h-10">
    <polyline fill="none" stroke="currentColor" stroke-width="2" points="{{ $points }}" />
</svg>
```

### Task M7-T04: Reports index view

**Files:**
- Create: `resources/views/admin/reports/index.blade.php`

- [ ] **Step 1: View**

```blade
<x-admin.layout title="Reports">
    <h1 class="font-serif text-2xl mb-6">Reports</h1>

    <form method="GET" class="card p-4 mb-6 flex flex-wrap items-end gap-3">
        <label class="flex flex-col text-sm">
            Range
            <select name="preset" class="border rounded p-2">
                @foreach(['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days', 'ytd' => 'This year'] as $k => $v)
                    <option value="{{ $k }}" @selected($preset === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </label>
        <label class="flex flex-col text-sm">From <input type="date" name="from" value="{{ request('from') }}" class="border rounded p-2"></label>
        <label class="flex flex-col text-sm">To   <input type="date" name="to"   value="{{ request('to') }}"   class="border rounded p-2"></label>
        <button class="btn-secondary text-sm">Update</button>

        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.reports.attendance.csv', request()->only(['preset', 'from', 'to'])) }}" class="btn-secondary text-sm">Attendance CSV</a>
            <a href="{{ route('admin.reports.giving.csv',     request()->only(['preset', 'from', 'to'])) }}" class="btn-secondary text-sm">Giving CSV</a>
        </div>
    </form>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Members</p>
            <p class="text-2xl font-semibold">{{ $memberCount }}</p>
            <p class="text-xs text-green-700">+{{ $memberDelta }} new</p>
        </div>
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Events held</p>
            <p class="text-2xl font-semibold">{{ $eventsHeld }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Avg attendance</p>
            <p class="text-2xl font-semibold">{{ $avgAttendance }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Giving</p>
            <p class="text-2xl font-semibold">{{ formatMoney($givingTotal) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4 mb-6">
        <div class="card p-4">
            <h2 class="font-serif text-lg mb-2">Attendance trend</h2>
            <div class="text-brand-primary"><x-admin.sparkline :data="$perEventAttendance" /></div>
        </div>
        <div class="card p-4">
            <h2 class="font-serif text-lg mb-2">Giving by fund</h2>
            @php $maxFund = $byFund->max('total') ?: 1; @endphp
            <ul class="space-y-2">
                @foreach($byFund as $f)
                    <li class="text-sm">
                        <div class="flex justify-between mb-1"><span>{{ $f['name'] }}</span><span class="font-mono">{{ formatMoney($f['total']) }}</span></div>
                        <div class="h-2 bg-surface rounded">
                            <div class="h-2 bg-brand-secondary rounded" style="width: {{ round(($f['total'] / $maxFund) * 100) }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4">
        <div class="card p-4">
            <h2 class="font-serif text-lg mb-2">Top events by attendance</h2>
            <ol class="space-y-2 text-sm">
                @foreach($topEvents as $e)
                    <li class="flex justify-between"><span>{{ $e->title }}</span><span class="font-mono">{{ $e->attendances_count }}</span></li>
                @endforeach
            </ol>
        </div>
        <div class="card p-4">
            <h2 class="font-serif text-lg mb-2">Givers</h2>
            <p class="text-sm">Total donors: <strong>{{ $donorCount }}</strong></p>
            <p class="text-xs text-ink-muted mt-1">Names are intentionally hidden here. See <a href="{{ route('admin.tithes.index') }}" class="text-brand-primary">Tithes</a> for full records.</p>
        </div>
    </div>
</x-admin.layout>
```

### Task M7-T05: Sidebar — wire Reports group

**Files:**
- Modify: `resources/views/components/admin/sidebar.blade.php`

- [ ] **Step 1: Add**:

```blade
@can('view-reports')
    <div class="px-3 mt-6 text-xs uppercase text-ink-muted">Reports</div>
    <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 hover:bg-surface rounded text-sm">Overview</a>
@endcan
```

### Task M7-T06: M7 feature tests

**Files:**
- Create: `tests/Feature/Phase3/AdminReportsTest.php`

- [ ] **Step 1: Write tests**

```php
<?php

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    $this->seed(\Database\Seeders\TitheFundSeeder::class);

    $this->siteAdmin = User::factory()->create(['is_admin' => true]);
    $this->siteAdmin->assignRole('Site Admin');

    $this->organizer = User::factory()->create(['is_admin' => true]);
    $this->organizer->assignRole('Event Organizer'); // no view-reports
});

it('renders the reports dashboard for someone with view-reports', function () {
    $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Members')
        ->assertSee('Events held')
        ->assertSee('Giving');
});

it('forbids Event Organizer (no view-reports)', function () {
    $this->actingAs($this->organizer, 'admin')
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

it('counts giving total in cents from tithes within range', function () {
    $fund = TitheFund::where('slug', 'general')->first();
    Tithe::factory()->create([
        'fund_id' => $fund->id,
        'amount_cents' => 5000,
        'received_at' => now()->subDays(5),
    ]);
    Tithe::factory()->create([
        'fund_id' => $fund->id,
        'amount_cents' => 2500,
        'received_at' => now()->subDays(40), // outside default 30d range
    ]);

    $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.index'))
        ->assertSee('$50.00')
        ->assertDontSee('$75.00');
});

it('streams attendance CSV', function () {
    Event::factory()->create(['title' => 'Sunday Service', 'starts_at' => now()->subDay()]);

    $resp = $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.attendance.csv'));

    $resp->assertOk()->assertHeader('content-type', 'text/csv');
    expect($resp->streamedContent())->toContain('Event,Date,RSVPs,Checked In,Walk-ins');
});

it('streams giving CSV', function () {
    $resp = $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.giving.csv'));

    $resp->assertOk();
    expect($resp->streamedContent())->toContain('Date,Fund,Method,Amount');
});

it('respects custom date range', function () {
    $fund = TitheFund::where('slug', 'general')->first();
    Tithe::factory()->create([
        'fund_id' => $fund->id,
        'amount_cents' => 99900,
        'received_at' => now()->subDays(120),
    ]);

    $this->actingAs($this->siteAdmin, 'admin')
        ->get(route('admin.reports.index', [
            'from' => now()->subDays(130)->toDateString(),
            'to' => now()->subDays(100)->toDateString(),
        ]))
        ->assertSee('$999.00');
});
```

- [ ] **Step 2: Run**

```powershell
php artisan test --filter Phase3/AdminReportsTest
```

---

# Milestone 8 — Reminders engine — **PAUSE FOR REVIEW**

> **Goal of M8:** Three queued mailables, three scheduled jobs, settings tab, profile preferences. Idempotent. Tests use `Mail::fake()` and `Queue::fake()`.

### Task M8-T01: Mailable base — `BrandedMail`

**Files:**
- Create: `app/Mail/BrandedMail.php`
- Create: `resources/views/mail/_layout.blade.php`

- [ ] **Step 1: Base mailable**

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class BrandedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        $brandName = settings('brand.name', config('app.name', 'Our Church'));
        $fromEmail = settings('contact.email') ?: config('mail.from.address');

        return new \Illuminate\Mail\Mailables\Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromEmail, $brandName),
            replyTo: [new \Illuminate\Mail\Mailables\Address($fromEmail, $brandName)],
            subject: $this->subjectLine(),
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: $this->viewName(),
            with: $this->viewData() + [
                'brandName' => settings('brand.name', config('app.name')),
                'brandColor' => settings('brand.primary_color', '#7C3AED'),
                'address' => settings('contact.address', ''),
            ],
        );
    }

    abstract protected function subjectLine(): string;
    abstract protected function viewName(): string;
    abstract protected function viewData(): array;
}
```

- [ ] **Step 2: Layout partial**

```blade
{{-- resources/views/mail/_layout.blade.php --}}
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>{{ $brandName }}</title></head>
<body style="font-family: Inter, Arial, sans-serif; background:#f6f7f9; margin:0; padding:24px;">
    <div style="max-width:600px; margin:0 auto; background:white; border-radius:8px; overflow:hidden;">
        <div style="padding:20px; background:{{ $brandColor }}; color:white;">
            <h2 style="margin:0; font-family: Georgia, serif;">{{ $brandName }}</h2>
        </div>
        <div style="padding:24px; line-height:1.6; color:#222;">
            {{ $slot ?? '' }}
            @yield('body')
        </div>
        <div style="padding:16px 24px; background:#f6f7f9; color:#666; font-size:12px;">
            <p>{{ $address }}</p>
            <p><a href="{{ url('/member/profile').'#email-prefs' }}" style="color:{{ $brandColor }};">Manage email preferences</a></p>
        </div>
    </div>
</body>
</html>
```

### Task M8-T02: `EventReminderMail`

**Files:**
- Create: `app/Mail/EventReminderMail.php`
- Create: `resources/views/mail/event_reminder.blade.php`

- [ ] **Step 1: Mailable**

```php
<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class EventReminderMail extends BrandedMail
{
    public function __construct(public Event $event, public EventRsvp $rsvp, public User $user) {}

    protected function subjectLine(): string
    {
        return 'Reminder: '.$this->event->title.' tomorrow';
    }

    protected function viewName(): string
    {
        return 'mail.event_reminder';
    }

    protected function viewData(): array
    {
        return [
            'event' => $this->event,
            'rsvp' => $this->rsvp,
            'user' => $this->user,
            'cancelUrl' => URL::temporarySignedRoute(
                'member.events.cancel-rsvp',
                now()->addDays(7),
                ['event' => $this->event->slug]
            ),
        ];
    }
}
```

- [ ] **Step 2: View**

```blade
@extends('mail._layout')
@section('body')
    <p>Hi {{ $user->name }},</p>
    <p>This is a friendly reminder that <strong>{{ $event->title }}</strong> is happening tomorrow.</p>
    <p>
        <strong>When:</strong> {{ $event->starts_at?->format('l, F j · g:i A') }}<br>
        <strong>Where:</strong> {{ $event->location ?: '—' }}
    </p>
    @if($rsvp->guest_count > 0)
        <p>You're bringing {{ $rsvp->guest_count }} guest{{ $rsvp->guest_count === 1 ? '' : 's' }}.</p>
    @endif
    <p>Can't make it? <a href="{{ $cancelUrl }}">Cancel your RSVP</a>.</p>
@endsection
```

### Task M8-T03: `WeeklyDigestMail`

**Files:**
- Create: `app/Mail/WeeklyDigestMail.php`
- Create: `resources/views/mail/weekly_digest.blade.php`

- [ ] **Step 1: Mailable**

```php
<?php

namespace App\Mail;

use App\Models\User;

class WeeklyDigestMail extends BrandedMail
{
    public function __construct(public User $user, public array $payload) {}

    protected function subjectLine(): string
    {
        return 'This week at '.settings('brand.name', 'our church');
    }

    protected function viewName(): string
    {
        return 'mail.weekly_digest';
    }

    protected function viewData(): array
    {
        return [
            'user' => $this->user,
            'payload' => $this->payload,
        ];
    }
}
```

- [ ] **Step 2: View**

```blade
@extends('mail._layout')
@section('body')
    <p>Hi {{ $user->name }},</p>
    <p>Here's what's coming up this week.</p>

    @if(!empty($payload['events']))
        <h3>Upcoming events</h3>
        <ul>
            @foreach($payload['events'] as $e)
                <li><strong>{{ $e->title }}</strong> — {{ $e->starts_at?->format('D, M j') }}</li>
            @endforeach
        </ul>
    @endif

    @if(!empty($payload['prayer']))
        <h3>Prayer requests</h3>
        <ul>
            @foreach($payload['prayer'] as $p)
                <li>{{ \Illuminate\Support\Str::limit($p->title, 100) }}</li>
            @endforeach
        </ul>
    @endif

    @if(!empty($payload['feed']))
        <h3>From the community</h3>
        <ul>
            @foreach($payload['feed'] as $f)
                <li>{{ \Illuminate\Support\Str::limit($f->title, 100) }}</li>
            @endforeach
        </ul>
    @endif
@endsection
```

### Task M8-T04: `AdminDailyDigestMail`

**Files:**
- Create: `app/Mail/AdminDailyDigestMail.php`
- Create: `resources/views/mail/admin_daily_digest.blade.php`

- [ ] **Step 1: Mailable**

```php
<?php

namespace App\Mail;

use App\Models\User;

class AdminDailyDigestMail extends BrandedMail
{
    public function __construct(public User $admin, public array $payload) {}

    protected function subjectLine(): string
    {
        return 'Daily digest — '.settings('brand.name', 'admin');
    }

    protected function viewName(): string
    {
        return 'mail.admin_daily_digest';
    }

    protected function viewData(): array
    {
        return [
            'admin' => $this->admin,
            'payload' => $this->payload,
        ];
    }
}
```

- [ ] **Step 2: View**

```blade
@extends('mail._layout')
@section('body')
    <p>Hi {{ $admin->name }},</p>
    <p>Yesterday's activity and what's on for today.</p>

    <h3>New members ({{ count($payload['new_members'] ?? []) }})</h3>
    <h3>New prayer requests ({{ count($payload['new_prayer'] ?? []) }})</h3>
    <h3>Open care requests ({{ count($payload['open_care'] ?? []) }})</h3>
    <h3>Today's events with attendance open</h3>
    <ul>
        @foreach($payload['events_today'] ?? [] as $e)
            <li>{{ $e->title }} · {{ $e->starts_at?->format('g:i A') }} · code {{ $e->checkin_code }}</li>
        @endforeach
    </ul>
@endsection
```

### Task M8-T05: Job — `SendEventReminderEmails`

**Files:**
- Create: `app/Jobs/SendEventReminderEmails.php`

- [ ] **Step 1: Job**

```php
<?php

namespace App\Jobs;

use App\Mail\EventReminderMail;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEventReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        if (! settings('reminders.event_24h_enabled', true)) return;

        $events = Event::query()
            ->whereNull('reminded_at')
            ->whereBetween('starts_at', [now()->addHours(23), now()->addHours(25)])
            ->get();

        foreach ($events as $event) {
            $count = 0;
            $event->rsvps()
                ->where('status', 'going')
                ->with('user')
                ->chunk(200, function ($chunk) use ($event, &$count) {
                    foreach ($chunk as $rsvp) {
                        $user = $rsvp->user;
                        if (! $user || ! $user->email_verified_at) continue;
                        if (! $user->email_reminder_event_24h) continue;
                        Mail::to($user->email)->queue(new EventReminderMail($event, $rsvp, $user));
                        $count++;
                    }
                });

            $event->update(['reminded_at' => now()]);
            activity()->log("Sent {$count} event reminders for {$event->title}");
        }
    }
}
```

### Task M8-T06: Job — `SendWeeklyMemberDigest`

**Files:**
- Create: `app/Jobs/SendWeeklyMemberDigest.php`

- [ ] **Step 1: Job**

```php
<?php

namespace App\Jobs;

use App\Mail\WeeklyDigestMail;
use App\Models\Event;
use App\Models\FeedPost;
use App\Models\PrayerRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyMemberDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        if (! settings('reminders.weekly_digest_enabled', true)) return;

        $events = Event::where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addDays(7))
            ->orderBy('starts_at')
            ->limit(5)->get();

        $prayer = class_exists(PrayerRequest::class)
            ? PrayerRequest::latest()->limit(3)->get()
            : collect();

        $feed = class_exists(FeedPost::class)
            ? FeedPost::latest()->limit(3)->get()
            : collect();

        $total = 0;
        User::query()
            ->where('is_admin', false)
            ->where('email_weekly_digest', true)
            ->whereNotNull('email_verified_at')
            ->chunk(200, function ($chunk) use ($events, $prayer, $feed, &$total) {
                foreach ($chunk as $user) {
                    $payload = [
                        'events' => $events,
                        'prayer' => $prayer,
                        'feed' => $feed,
                    ];
                    if (empty($events) && $prayer->isEmpty() && $feed->isEmpty()) continue;
                    Mail::to($user->email)->queue(new WeeklyDigestMail($user, $payload));
                    $total++;
                }
            });

        activity()->log("Sent {$total} weekly digests");
    }
}
```

### Task M8-T07: Job — `SendAdminDailyDigest`

**Files:**
- Create: `app/Jobs/SendAdminDailyDigest.php`

- [ ] **Step 1: Job**

```php
<?php

namespace App\Jobs;

use App\Mail\AdminDailyDigestMail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdminDailyDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        if (! settings('reminders.admin_daily_digest_enabled', false)) return;

        $payload = [
            'new_members' => User::whereDate('created_at', today()->subDay())->where('is_admin', false)->get(),
            'new_prayer' => class_exists(\App\Models\PrayerRequest::class)
                ? \App\Models\PrayerRequest::whereDate('created_at', today()->subDay())->get()
                : collect(),
            'open_care' => class_exists(\App\Models\CareRequest::class)
                ? \App\Models\CareRequest::where('status', 'open')->get()
                : collect(),
            'events_today' => Event::whereDate('starts_at', today())
                ->where('attendance_open', true)->get(),
        ];

        $total = 0;
        User::where('is_admin', true)
            ->where('email_admin_daily_digest', true)
            ->each(function ($admin) use ($payload, &$total) {
                Mail::to($admin->email)->queue(new AdminDailyDigestMail($admin, $payload));
                $total++;
            });

        activity()->log("Sent {$total} admin daily digests");
    }
}
```

### Task M8-T08: Schedule the jobs

**Files:**
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Locate the existing `->withSchedule(...)` block or add one** at the end of the `Application::configure(...)` chain:

```php
->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
    $schedule->job(new \App\Jobs\SendEventReminderEmails)
        ->hourly()->onOneServer()->withoutOverlapping();

    $schedule->job(new \App\Jobs\SendWeeklyMemberDigest)
        ->weeklyOn(
            weekday(settings('reminders.weekly_digest_day', 'Saturday')),
            sprintf('%02d:00', (int) settings('reminders.weekly_digest_hour', 18))
        )
        ->onOneServer()->withoutOverlapping();

    $schedule->job(new \App\Jobs\SendAdminDailyDigest)
        ->dailyAt('08:00')->onOneServer()->withoutOverlapping();
})
```

- [ ] **Step 2: Verify schedule**

```powershell
php artisan schedule:list
```
Expected: 3 entries (hourly + weeklyOn + dailyAt).

### Task M8-T09: Reminders settings tab

**Files:**
- Modify: `resources/views/admin/settings/index.blade.php` (or the existing settings tab structure)

- [ ] **Step 1: Add a "Reminders" tab/section**

```blade
<section class="card p-6 mt-6">
    <h2 class="font-serif text-lg mb-4">Reminders</h2>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="flex flex-col gap-3">
        @csrf @method('PUT')

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="settings[reminders.event_24h_enabled]" value="1" @checked(settings('reminders.event_24h_enabled', true))>
            Event 24h reminders
        </label>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="settings[reminders.weekly_digest_enabled]" value="1" @checked(settings('reminders.weekly_digest_enabled', true))>
            Weekly digest
        </label>

        <div class="grid sm:grid-cols-2 gap-3">
            <label class="flex flex-col text-sm">
                Weekly digest day
                <select name="settings[reminders.weekly_digest_day]" class="border rounded p-2">
                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $d)
                        <option value="{{ $d }}" @selected(settings('reminders.weekly_digest_day') === $d)>{{ $d }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex flex-col text-sm">
                Weekly digest hour
                <select name="settings[reminders.weekly_digest_hour]" class="border rounded p-2">
                    @for($h = 0; $h < 24; $h++)
                        <option value="{{ $h }}" @selected((int) settings('reminders.weekly_digest_hour') === $h)>{{ sprintf('%02d:00', $h) }}</option>
                    @endfor
                </select>
            </label>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="settings[reminders.admin_daily_digest_enabled]" value="1" @checked(settings('reminders.admin_daily_digest_enabled', false))>
            Admin daily digest
        </label>

        <button class="btn-primary text-sm self-start">Save</button>
    </form>
</section>
```

- [ ] **Step 2:** Confirm the existing `SettingsController::update` accepts arbitrary `settings.*` keys. If not, extend the allowed list.

### Task M8-T10: Email preferences card on profile

**Files:**
- Modify: `resources/views/member/profile/edit.blade.php`
- Modify: `app/Http/Controllers/Member/ProfileController.php`

- [ ] **Step 1: View — add anchor + card**

```blade
<section id="email-prefs" class="card p-6 mt-6">
    <h2 class="font-serif text-lg mb-4">Email preferences</h2>
    <form method="POST" action="{{ route('member.profile.update') }}" class="flex flex-col gap-2">
        @csrf @method('PUT')
        <input type="hidden" name="_prefs_only" value="1">

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="email_reminder_event_24h" value="1" @checked(auth()->user()->email_reminder_event_24h)>
            Event reminders (24h before)
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="email_weekly_digest" value="1" @checked(auth()->user()->email_weekly_digest)>
            Weekly digest
        </label>

        <button class="btn-primary text-sm self-start mt-2">Save preferences</button>
    </form>
</section>
```

- [ ] **Step 2: Controller `update`** — when `_prefs_only=1`, only persist the email preference columns:

```php
public function update(Request $request)
{
    if ($request->boolean('_prefs_only')) {
        $request->user()->update([
            'email_reminder_event_24h' => $request->boolean('email_reminder_event_24h'),
            'email_weekly_digest' => $request->boolean('email_weekly_digest'),
        ]);
        return back()->with('status', 'Email preferences updated.');
    }

    // ... existing profile update logic ...
}
```

### Task M8-T11: M8 feature tests

**Files:**
- Create: `tests/Feature/Phase3/RemindersTest.php`

- [ ] **Step 1: Tests**

```php
<?php

use App\Jobs\SendAdminDailyDigest;
use App\Jobs\SendEventReminderEmails;
use App\Jobs\SendWeeklyMemberDigest;
use App\Mail\AdminDailyDigestMail;
use App\Mail\EventReminderMail;
use App\Mail\WeeklyDigestMail;
use App\Models\AppSetting;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
    AppSetting::firstOrCreate(['key' => 'reminders.event_24h_enabled'], ['value' => '1']);
    AppSetting::firstOrCreate(['key' => 'reminders.weekly_digest_enabled'], ['value' => '1']);
    AppSetting::firstOrCreate(['key' => 'reminders.admin_daily_digest_enabled'], ['value' => '1']);
});

it('dispatches event reminders for tomorrow and marks reminded_at', function () {
    $event = Event::factory()->create(['starts_at' => now()->addHours(24)]);
    $member = User::factory()->create(['email_verified_at' => now(), 'email_reminder_event_24h' => true]);
    EventRsvp::factory()->create(['event_id' => $event->id, 'user_id' => $member->id]);

    (new SendEventReminderEmails())->handle();

    Mail::assertQueued(EventReminderMail::class, 1);
    expect($event->fresh()->reminded_at)->not->toBeNull();
});

it('skips members who opted out of event reminders', function () {
    $event = Event::factory()->create(['starts_at' => now()->addHours(24)]);
    $member = User::factory()->create(['email_verified_at' => now(), 'email_reminder_event_24h' => false]);
    EventRsvp::factory()->create(['event_id' => $event->id, 'user_id' => $member->id]);

    (new SendEventReminderEmails())->handle();

    Mail::assertNotQueued(EventReminderMail::class);
});

it('does not re-send for events already reminded', function () {
    $event = Event::factory()->create([
        'starts_at' => now()->addHours(24),
        'reminded_at' => now()->subMinutes(30),
    ]);
    $member = User::factory()->create(['email_verified_at' => now()]);
    EventRsvp::factory()->create(['event_id' => $event->id, 'user_id' => $member->id]);

    (new SendEventReminderEmails())->handle();

    Mail::assertNotQueued(EventReminderMail::class);
});

it('respects global event reminders toggle', function () {
    AppSetting::where('key', 'reminders.event_24h_enabled')->update(['value' => '0']);

    $event = Event::factory()->create(['starts_at' => now()->addHours(24)]);
    $member = User::factory()->create(['email_verified_at' => now()]);
    EventRsvp::factory()->create(['event_id' => $event->id, 'user_id' => $member->id]);

    (new SendEventReminderEmails())->handle();

    Mail::assertNotQueued(EventReminderMail::class);
});

it('skips unverified members in weekly digest', function () {
    Event::factory()->create(['starts_at' => now()->addDays(3)]);

    User::factory()->create(['email_verified_at' => null, 'email_weekly_digest' => true]);
    User::factory()->create(['email_verified_at' => now(),  'email_weekly_digest' => true]);

    (new SendWeeklyMemberDigest())->handle();

    Mail::assertQueued(WeeklyDigestMail::class, 1);
});

it('skips weekly digest when payload empty', function () {
    User::factory()->create(['email_verified_at' => now(), 'email_weekly_digest' => true]);

    (new SendWeeklyMemberDigest())->handle();

    Mail::assertNotQueued(WeeklyDigestMail::class);
});

it('admin daily digest goes only to opted-in admins', function () {
    User::factory()->create(['is_admin' => true, 'email_admin_daily_digest' => true]);
    User::factory()->create(['is_admin' => true, 'email_admin_daily_digest' => false]);
    User::factory()->create(['is_admin' => false, 'email_admin_daily_digest' => true]);

    (new SendAdminDailyDigest())->handle();

    Mail::assertQueued(AdminDailyDigestMail::class, 1);
});

it('admin daily digest respects global toggle', function () {
    AppSetting::where('key', 'reminders.admin_daily_digest_enabled')->update(['value' => '0']);
    User::factory()->create(['is_admin' => true, 'email_admin_daily_digest' => true]);

    (new SendAdminDailyDigest())->handle();

    Mail::assertNotQueued(AdminDailyDigestMail::class);
});
```

- [ ] **Step 2: Run**

```powershell
php artisan test --filter Phase3/RemindersTest
```

### Task M8-T12: **PAUSE — confirm queue worker running**

- [ ] **Step 1:** Open a second PowerShell terminal:

```powershell
$env:Path = "D:\XAMPP\php;" + $env:Path
php artisan queue:work --queue=default --tries=3
```
Expected: "Processing jobs from the [default] queue."

- [ ] **Step 2:** In another terminal trigger the event-reminder job manually for verification:

```powershell
php artisan tinker --execute="(new App\Jobs\SendEventReminderEmails())->handle();"
```

Confirm the queue worker picks up the queued mail and writes it to `storage/logs/laravel.log` (because mail driver is `log`).

- [ ] **Step 3:** Present logs to user. Wait for "approved" before moving to M9.

---

# Milestone 9 — Polish, docs, end-to-end smoke

> **Goal of M9:** Three docs published; full smoke run; CSS rebuild if needed; full Pest suite green.

### Task M9-T01: Update `docs/member-onboarding.md`

**Files:**
- Modify: `docs/member-onboarding.md`

- [ ] **Step 1:** Append a new "RSVP and check-in" section explaining:
  - How to RSVP via the event page.
  - How to receive reminders (email-prefs link on profile).
  - How to use `/member/check-in` with the 4-digit code at the door.
  - That walk-ins are handled by organizers — no member action needed.

### Task M9-T02: Create `docs/organizer-attendance.md`

**Files:**
- Create: `docs/organizer-attendance.md`

- [ ] **Step 1:** Document the organizer flow:
  - Where to find the event in `/admin/events`.
  - How to open the Attendance tab.
  - How to open check-in (auto-generates code).
  - How to display the code at the door.
  - How to mark walk-ins.
  - How to export CSV.

### Task M9-T03: Create `docs/finance-tithes.md`

**Files:**
- Create: `docs/finance-tithes.md`

- [ ] **Step 1:** Document Site Admin finance workflow:
  - Where to find Tithes in the sidebar.
  - How to record a gift (member typeahead + free-text).
  - How funds work and how to deactivate vs. delete.
  - How the donate page connects (still bank-transfer only; gifts are recorded manually).
  - How to export CSV for accounting.

### Task M9-T04: CSS/JS rebuild

**Files:**
- (Vite build artifacts)

- [ ] **Step 1: Rebuild assets**

```powershell
npm run build
```

- [ ] **Step 2:** Confirm `public/build/manifest.json` updated.

### Task M9-T05: Full test suite

- [ ] **Step 1: Run full Pest suite**

```powershell
php artisan test
```
Expected: all green. Fix any flakiness before marking complete.

### Task M9-T06: End-to-end smoke (manual)

- [ ] **Step 1:** Start dev server + queue worker:

```powershell
php artisan serve
# in another terminal:
php artisan queue:work
```

- [ ] **Step 2: Member smoke**
  - Register or login as a member.
  - RSVP to an upcoming event.
  - Visit profile → confirm email-prefs card renders.
  - As Site Admin, manually run `SendEventReminderEmails::dispatch()` on an event 24h out.
  - Confirm mail logged in `storage/logs/laravel.log`.
  - Open the event in admin, open check-in. Note the code.
  - Visit `/member/check-in` as the member and enter code. Confirm thanks page.

- [ ] **Step 3: Admin smoke**
  - Open `/admin/events/{id}/attendance`. Mark walk-ins. Toggle a member checkbox. Confirm counts update.
  - Export CSV. Confirm filename and header.
  - Open `/admin/tithes`. Record one cash gift to a member, one anonymous bank transfer. Confirm both appear.
  - Open `/admin/reports`. Confirm KPIs include the gifts.
  - Export both report CSVs. Confirm contents.

- [ ] **Step 4: Capture screenshots** (member RSVP, check-in form, attendance roster, tithes index, reports dashboard) and attach to delivery summary.

---

## Done criteria for Phase 3

- All 9 milestones merged.
- `php artisan test` green.
- Manual smoke confirms member flow, organizer flow, and Site Admin finance flow.
- Three new/updated docs in `docs/`.
- Queue worker confirmed running for production (Supervisor / NSSM / `queue:work` daemon).
- Scheduler entry confirmed in cron: `* * * * * php artisan schedule:run`.
