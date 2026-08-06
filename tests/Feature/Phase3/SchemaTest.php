<?php

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRsvp;
use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
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
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

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
