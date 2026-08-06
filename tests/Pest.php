<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
| Apply RefreshDatabase to every Feature test so each test runs against a
| clean schema. Unit tests do not extend Tests\TestCase so they bypass the
| framework boot entirely.
*/

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        // Ensure Spatie permissions are seeded for every Feature test —
        // assignRole('Site Admin') would fail without these rows.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Build an admin user already attached to the Site Admin role on the admin
 * guard. Pass `password_change_required => false` (the default) to skip the
 * force-change redirect for most tests.
 */
function makeAdmin(array $attrs = []): User
{
    // `+` keeps existing keys from the left operand, so caller overrides
    // need to be the LEFT side of the union.
    $user = User::factory()->create($attrs + [
        'is_admin' => true,
        'password_change_required' => false,
    ]);

    $user->assignRole('Site Admin');

    return $user;
}

/**
 * Build a non-admin "member" user (web guard). Mirrors makeAdmin() but
 * leaves the user without the admin flag and without any role.
 */
function makeMember(array $attrs = []): User
{
    return User::factory()->create($attrs + [
        'is_admin' => false,
        'password_change_required' => false,
    ]);
}

function something()
{
    // ..
}
