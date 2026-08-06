<?php

use App\Jobs\SendAdminDailyDigest;
use App\Jobs\SendEventReminderEmails;
use App\Jobs\SendWeeklyMemberDigest;
use App\Mail\AdminDailyDigestMail;
use App\Mail\EventReminderMail;
use App\Mail\WeeklyDigestMail;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\SettingsRepository;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
    SiteSetting::firstOrCreate(['key' => 'reminders.event_24h_enabled'], ['value' => '1']);
    SiteSetting::firstOrCreate(['key' => 'reminders.weekly_digest_enabled'], ['value' => '1']);
    SiteSetting::firstOrCreate(['key' => 'reminders.admin_daily_digest_enabled'], ['value' => '1']);
    app(SettingsRepository::class)->flush();
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
    SiteSetting::where('key', 'reminders.event_24h_enabled')->update(['value' => '0']);
    app(SettingsRepository::class)->flush();

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
    SiteSetting::where('key', 'reminders.admin_daily_digest_enabled')->update(['value' => '0']);
    app(SettingsRepository::class)->flush();
    User::factory()->create(['is_admin' => true, 'email_admin_daily_digest' => true]);

    (new SendAdminDailyDigest())->handle();

    Mail::assertNotQueued(AdminDailyDigestMail::class);
});
