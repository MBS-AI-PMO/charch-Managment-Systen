<?php

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    $this->member = User::factory()->create([
        'name' => 'Sarah Chen',
        'email_verified_at' => now(),
    ]);
    $this->event = Event::factory()->create(['starts_at' => now()->addDays(2)]);
    $this->rsvp = EventRsvp::factory()->create([
        'event_id' => $this->event->id,
        'user_id' => $this->member->id,
        'status' => 'going',
    ]);
});

function signedCancelUrl(Event $event, int $userId, ?\DateTimeInterface $expires = null): string
{
    return URL::temporarySignedRoute(
        'site.events.cancel-rsvp.public',
        $expires ?? now()->addDays(7),
        ['event' => $event->slug, 'user' => $userId],
    );
}

it('cancels RSVP via a valid signed link without authentication', function () {
    $url = signedCancelUrl($this->event, $this->member->id);

    $this->get($url)
        ->assertOk()
        ->assertSee('Sarah'); // friendly first-name greeting

    expect($this->rsvp->fresh()->status)->toBe('cancelled');
});

it('rejects a tampered signature with 403', function () {
    $url = signedCancelUrl($this->event, $this->member->id).'-tampered';

    $this->get($url)->assertForbidden();

    expect($this->rsvp->fresh()->status)->toBe('going');
});

it('returns 404 when no RSVP exists for the user/event pair', function () {
    $other = User::factory()->create();
    $url = signedCancelUrl($this->event, $other->id);

    $this->get($url)->assertNotFound();

    // Original RSVP untouched.
    expect($this->rsvp->fresh()->status)->toBe('going');
});

it('is idempotent for already-cancelled RSVPs', function () {
    $this->rsvp->update(['status' => 'cancelled']);

    $url = signedCancelUrl($this->event, $this->member->id);

    $this->get($url)->assertOk();

    expect($this->rsvp->fresh()->status)->toBe('cancelled');
});
