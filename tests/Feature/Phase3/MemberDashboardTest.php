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
