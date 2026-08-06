<?php

use App\Models\Page;
use App\Models\User;

it('lets an admin view the pages index', function () {
    $this->actingAs(makeAdmin(), 'admin')
        ->get(route('admin.pages.index'))
        ->assertOk();
});

it('updates a page and persists the change', function () {
    $page = Page::factory()->create(['slug' => 'home', 'title' => 'Old Title']);

    $this->actingAs(makeAdmin(), 'admin')
        ->put(route('admin.pages.update', $page), [
            'title' => 'A Brand New Title',
            'slug' => 'home',
            'body' => '<p>Fresh body.</p>',
            'is_published' => 1,
        ])->assertRedirect();

    expect($page->fresh()->title)->toBe('A Brand New Title')
        ->and($page->fresh()->body)->toContain('Fresh body');
});

it('purifies a <script> tag out of a page body', function () {
    $page = Page::factory()->create(['slug' => 'home']);

    $this->actingAs(makeAdmin(), 'admin')
        ->put(route('admin.pages.update', $page), [
            'title' => 'Home',
            'slug' => 'home',
            'body' => '<script>alert(1)</script><p>Hello</p>',
            'is_published' => 1,
        ])->assertRedirect();

    $body = $page->fresh()->body;
    expect($body)->not->toContain('<script>')
        ->and($body)->toContain('Hello');
});

it('forbids a non-admin member from the pages index', function () {
    $member = User::factory()->create([
        'is_admin' => false,
        'password_change_required' => false,
    ]);

    // Member is on the web guard, so admin middleware will see no admin user
    // and redirect to the admin login page (or 403).
    $resp = $this->actingAs($member, 'web')
        ->get(route('admin.pages.index'));

    expect($resp->getStatusCode())->toBeIn([302, 403]);
});

it('forbids an admin without the manage-pages permission', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'password_change_required' => false,
    ]);
    $admin->assignRole('Event Organizer');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.pages.index'))
        ->assertStatus(403);
});
