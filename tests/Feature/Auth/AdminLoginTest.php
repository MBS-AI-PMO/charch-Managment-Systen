<?php

use App\Models\User;

it('shows the admin login page', function () {
    $this->get('/admin/login')
        ->assertOk();
});

it('redirects guests away from every admin dashboard url', function (string $url) {
    $this->get($url)->assertRedirect(route('admin.login'));
})->with([
    '/admin',
    '/admin/pages',
    '/admin/users',
    '/admin/settings',
    '/admin/certificates',
    '/admin/preview',
    '/admin/preview/pages',
    '/admin/preview/settings',
]);

it('logs an admin in with valid credentials', function () {
    $admin = makeAdmin(['password' => 'secret-pass-123!']);

    $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'secret-pass-123!',
    ])->assertRedirect(route('admin.dashboard'));

    expect(auth('admin')->check())->toBeTrue();
});

it('rejects the wrong password', function () {
    $admin = makeAdmin(['password' => 'secret-pass-123!']);

    $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    expect(auth('admin')->check())->toBeFalse();
});

it('rejects a non-admin user attempting admin login', function () {
    $user = User::factory()->create([
        'is_admin' => false,
        'password' => 'secret-pass-123!',
    ]);

    $this->post('/admin/login', [
        'email' => $user->email,
        'password' => 'secret-pass-123!',
    ])->assertSessionHasErrors('email');

    expect(auth('admin')->check())->toBeFalse();
});

it('redirects already-authenticated admins away from the login page', function () {
    $admin = makeAdmin();

    $this->actingAs($admin, 'admin')
        ->get('/admin/login')
        ->assertRedirect();
});
