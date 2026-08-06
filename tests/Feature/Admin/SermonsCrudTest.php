<?php

use App\Models\Sermon;
use App\Models\User;

it('lets an admin view the sermons index', function () {
    $this->actingAs(makeAdmin(), 'admin')
        ->get(route('admin.sermons.index'))
        ->assertOk();
});

it('creates updates and deletes a sermon', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.sermons.store'), [
            'title' => 'A Test Sermon',
            'summary' => 'Short summary',
            'body' => '<p>Body</p>',
            'preached_on' => now()->subDay()->toDateString(),
            'is_published' => 1,
        ])->assertRedirect();

    $sermon = Sermon::where('title', 'A Test Sermon')->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.sermons.update', $sermon), [
            'title' => 'A Test Sermon (Updated)',
            'preached_on' => now()->subDay()->toDateString(),
            'is_published' => 1,
        ])->assertRedirect();

    expect($sermon->fresh()->title)->toBe('A Test Sermon (Updated)');

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.sermons.destroy', $sermon))
        ->assertRedirect();

    expect(Sermon::find($sermon->id))->toBeNull();
});

it('hides draft sermons from the public', function () {
    $sermon = Sermon::factory()->draft()->create();

    $this->get(route('site.sermons.show', $sermon))->assertNotFound();
});

it('denies non-sermon-admins', function () {
    $admin = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $admin->assignRole('Event Organizer');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.sermons.index'))
        ->assertStatus(403);
});
