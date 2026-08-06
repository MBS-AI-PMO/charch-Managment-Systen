<?php

use App\Models\User;

/*
 * Render the sidebar in isolation rather than hitting the dashboard. The
 * dashboard view has KPI cards titled "Pages" and "Blog" regardless of role,
 * which would defeat assertDontSee on the sidebar.
 */

it('shows phase-2 modules to a Prayer Organizer and hides editorial modules', function () {
    $u = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $u->assignRole('Prayer Organizer');

    auth('admin')->setUser($u);
    $html = view('components.admin.sidebar')->render();

    expect($html)->toContain('Prayer Requests')
        ->toContain('Knock for Help')
        ->toContain('Community Feed')
        ->not->toContain('Sermons')
        ->not->toContain('>Pages<')
        ->not->toContain('>Blog<');
});

it('shows event-management modules to an Event Organizer and hides prayer modules', function () {
    $u = User::factory()->create(['is_admin' => true, 'password_change_required' => false]);
    $u->assignRole('Event Organizer');

    auth('admin')->setUser($u);
    $html = view('components.admin.sidebar')->render();

    expect($html)->toContain('Events')
        ->toContain('Ministries')
        ->toContain('Attendance')
        ->toContain('Reminders')
        ->not->toContain('Prayer Requests')
        ->not->toContain('Knock for Help')
        ->not->toContain('Community Feed');
});

it('shows every module to a Site Admin', function () {
    $admin = makeAdmin();

    auth('admin')->setUser($admin);
    $html = view('components.admin.sidebar')->render();

    expect($html)->toContain('Pages')
        ->toContain('Blog')
        ->toContain('Prayer Requests')
        ->toContain('Knock for Help')
        ->toContain('Community Feed')
        ->toContain('Sermons')
        ->toContain('Events');
});
