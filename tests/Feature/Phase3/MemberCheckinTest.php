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
