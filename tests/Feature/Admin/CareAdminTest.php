<?php

use App\Models\CareRequest;
use App\Models\User;

it('sets responder_id when status moves from open to responding', function () {
    $admin = makeAdmin();
    $owner = makeMember();
    $care = CareRequest::factory()->create([
        'user_id' => $owner->id,
        'status' => 'open',
        'responder_id' => null,
    ]);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.care.update', $care), [
            'status' => 'responding',
        ])->assertRedirect();

    $fresh = $care->fresh();
    expect($fresh->status)->toBe('responding')
        ->and($fresh->responder_id)->toBe($admin->id);
});

it('sets closed_at when status moves from responding to closed', function () {
    $admin = makeAdmin();
    $owner = makeMember();
    $care = CareRequest::factory()->create([
        'user_id' => $owner->id,
        'status' => 'responding',
        'responder_id' => $admin->id,
        'closed_at' => null,
    ]);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.care.update', $care), [
            'status' => 'closed',
        ])->assertRedirect();

    $fresh = $care->fresh();
    expect($fresh->status)->toBe('closed')
        ->and($fresh->closed_at)->not->toBeNull();
});

it('denies an Event Organizer from the care admin', function () {
    $u = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $u->assignRole('Event Organizer');

    $this->actingAs($u, 'admin')
        ->get(route('admin.care.index'))
        ->assertStatus(403);
});
