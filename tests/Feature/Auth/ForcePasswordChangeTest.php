<?php

use Illuminate\Support\Facades\Hash;

it('redirects admins flagged for password change away from the dashboard', function () {
    $admin = makeAdmin([
        'password' => 'TempPass-123!',
        'password_change_required' => true,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.pages.index'))
        ->assertRedirect(route('admin.password.force'));
});

it('clears the password-change flag once the admin sets a new password', function () {
    $admin = makeAdmin([
        'password' => 'TempPass-123!',
        'password_change_required' => true,
    ]);

    $this->actingAs($admin, 'admin')
        ->post('/admin/password/force', [
            'current_password' => 'TempPass-123!',
            'password' => 'ChosenPass-Now-789!',
            'password_confirmation' => 'ChosenPass-Now-789!',
        ])
        ->assertRedirect(route('admin.dashboard'));

    $fresh = $admin->fresh();
    expect((bool) $fresh->password_change_required)->toBeFalse()
        ->and(Hash::check('ChosenPass-Now-789!', $fresh->password))->toBeTrue();
});
