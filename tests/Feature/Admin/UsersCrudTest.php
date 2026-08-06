<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates a user with a role', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.users.store'), [
            'name' => 'New Editor',
            'email' => 'editor@example.com',
            'password' => 'StrongPass-123!',
            'password_confirmation' => 'StrongPass-123!',
            'roles' => ['Event Organizer'],
            'is_admin' => 1,
        ])->assertRedirect();

    $created = User::where('email', 'editor@example.com')->firstOrFail();
    expect($created->hasRole('Event Organizer'))->toBeTrue();
});

it('rejects a weak password when creating a user', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), [
            'name' => 'Weak Pass',
            'email' => 'weak@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

    expect(User::where('email', 'weak@example.com')->exists())->toBeFalse();
});

it('updates a user', function () {
    $admin = makeAdmin();
    $target = User::factory()->create(['is_admin' => true, 'name' => 'Old Name']);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.users.update', $target), [
            'name' => 'Renamed',
            'email' => $target->email,
        ])->assertRedirect();

    expect($target->fresh()->name)->toBe('Renamed');
});

it('refuses to delete the bootstrap site admin (id=1)', function () {
    // Force a user row with id=1 — AUTO_INCREMENT may have advanced from
    // earlier transactions in the same suite, so we insert directly.
    User::query()->insert([
        'id' => 1,
        'name' => 'Bootstrap Admin',
        'email' => 'bootstrap@example.com',
        'password' => bcrypt('whatever-pass-1234'),
        'is_admin' => true,
        'password_change_required' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $bootstrap = User::find(1);
    $actor = makeAdmin();

    $this->actingAs($actor, 'admin')
        ->delete(route('admin.users.destroy', $bootstrap))
        ->assertSessionHasErrors('user');

    expect(User::find(1))->not->toBeNull();
});
