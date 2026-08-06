<?php

use App\Models\Event;
use App\Models\User;

it('lets an admin view the events index', function () {
    $this->actingAs(makeAdmin(), 'admin')
        ->get(route('admin.events.index'))
        ->assertOk();
});

it('creates updates and deletes an event', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.events.store'), [
            'title' => 'Test Picnic',
            'description' => 'Bring food',
            'location' => 'Park',
            'starts_at' => now()->addDays(5)->toDateTimeString(),
            'is_published' => 1,
        ])->assertRedirect();

    $event = Event::where('title', 'Test Picnic')->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.events.update', $event), [
            'title' => 'Test Picnic (Updated)',
            'starts_at' => now()->addDays(5)->toDateTimeString(),
            'is_published' => 1,
        ])->assertRedirect();

    expect($event->fresh()->title)->toBe('Test Picnic (Updated)');

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.events.destroy', $event))
        ->assertRedirect();

    expect(Event::find($event->id))->toBeNull();
});

it('hides draft events from the public detail page', function () {
    $event = Event::factory()->draft()->create();

    $this->get(route('site.events.show', $event))->assertNotFound();
});

it('denies non-event-admins', function () {
    $admin = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $admin->assignRole('Prayer Organizer');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.events.index'))
        ->assertStatus(403);
});
