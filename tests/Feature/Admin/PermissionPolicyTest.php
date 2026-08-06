<?php

use App\Models\User;

it('lets a Site Admin reach every resource index', function () {
    $admin = makeAdmin();

    $routes = [
        'admin.pages.index',
        'admin.blog.posts.index',
        'admin.sermons.index',
        'admin.events.index',
        'admin.ministries.index',
        'admin.users.index',
        'admin.settings.index',
    ];

    foreach ($routes as $name) {
        $this->actingAs($admin, 'admin')
            ->get(route($name))
            ->assertOk();
    }
});

it('denies an Event Organizer from /admin/pages', function () {
    $u = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $u->assignRole('Event Organizer');

    $this->actingAs($u, 'admin')
        ->get(route('admin.pages.index'))
        ->assertStatus(403);
});

it('denies a Prayer Organizer from /admin/events', function () {
    $u = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $u->assignRole('Prayer Organizer');

    $this->actingAs($u, 'admin')
        ->get(route('admin.events.index'))
        ->assertStatus(403);
});
