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
        ->assertSee("You're going", false);
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
