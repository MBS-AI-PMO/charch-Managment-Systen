<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('sends a reset link to an admin', function () {
    Notification::fake();

    $admin = makeAdmin();

    $this->post('/admin/forgot', ['email' => $admin->email])
        ->assertRedirect();

    Notification::assertSentTo($admin, ResetPassword::class);
});

it('resets the admin password with a valid token', function () {
    $admin = makeAdmin(['password' => 'OldPass-123!']);

    $token = Password::broker('admins')->createToken($admin);

    $this->post('/admin/reset', [
        'token' => $token,
        'email' => $admin->email,
        'password' => 'BrandNew-Pass-456!',
        'password_confirmation' => 'BrandNew-Pass-456!',
    ])->assertRedirect(route('admin.login'));

    expect(Hash::check('BrandNew-Pass-456!', $admin->fresh()->password))->toBeTrue();
});

it('rejects an invalid reset token', function () {
    $admin = makeAdmin(['password' => 'OldPass-123!']);

    $this->post('/admin/reset', [
        'token' => 'this-is-not-a-real-token',
        'email' => $admin->email,
        'password' => 'BrandNew-Pass-456!',
        'password_confirmation' => 'BrandNew-Pass-456!',
    ])->assertSessionHasErrors('email');

    expect(Hash::check('BrandNew-Pass-456!', $admin->fresh()->password))->toBeFalse();
});
