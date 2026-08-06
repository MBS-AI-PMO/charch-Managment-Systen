<?php

use App\Models\User;

it('shows the member login page', function () {
    $this->get('/login')->assertOk();
});

it('logs a member in with valid credentials', function () {
    $member = makeMember(['password' => 'member-pass-123!']);

    $this->post('/login', [
        'email' => $member->email,
        'password' => 'member-pass-123!',
    ])->assertRedirect(route('member.dashboard'));

    expect(auth('web')->check())->toBeTrue();
});

it('rejects admins on the member login page', function () {
    $admin = makeAdmin(['password' => 'admin-pass-123!']);

    $resp = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'admin-pass-123!',
    ]);

    $resp->assertSessionHasErrors('email');
    expect(session('errors')->get('email')[0] ?? '')->toContain('admin login');
    expect(auth('web')->check())->toBeFalse();
});

it('lets a member visit /member but blocks /admin', function () {
    $member = makeMember();

    $this->actingAs($member, 'web')
        ->get('/member')
        ->assertOk();

    // /admin redirects unauthenticated guests to the admin login page (not the
    // member portal) because the request path starts with /admin.
    $this->actingAs($member, 'web')
        ->get('/admin')
        ->assertRedirect(route('admin.login'));
});
