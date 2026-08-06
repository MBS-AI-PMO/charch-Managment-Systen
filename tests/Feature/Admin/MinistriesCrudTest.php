<?php

use App\Models\Ministry;
use App\Models\User;

it('lets an admin view the ministries index', function () {
    $this->actingAs(makeAdmin(), 'admin')
        ->get(route('admin.ministries.index'))
        ->assertOk();
});

it('creates updates and deletes a ministry', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.ministries.store'), [
            'name' => 'Test Ministry',
            'summary' => 'A short summary',
            'body' => '<p>Body</p>',
            'is_published' => 1,
            'sort_order' => 0,
        ])->assertRedirect();

    $ministry = Ministry::where('name', 'Test Ministry')->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.ministries.update', $ministry), [
            'name' => 'Test Ministry (Updated)',
            'is_published' => 1,
        ])->assertRedirect();

    expect($ministry->fresh()->name)->toBe('Test Ministry (Updated)');

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.ministries.destroy', $ministry))
        ->assertRedirect();

    expect(Ministry::find($ministry->id))->toBeNull();
});

it('hides draft ministries from the public', function () {
    $ministry = Ministry::factory()->draft()->create();

    $this->get(route('site.ministries.show', $ministry))->assertNotFound();
});

it('denies users without manage-ministries permission', function () {
    $admin = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $admin->assignRole('Prayer Organizer');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.ministries.index'))
        ->assertStatus(403);
});
