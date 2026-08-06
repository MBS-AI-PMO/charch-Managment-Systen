<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SiteAdminSeeder extends Seeder
{
    public function run(): void
    {
        $u = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@church.local')],
            [
                'name' => env('ADMIN_NAME', 'Site Admin'),
                'password' => env('ADMIN_PASSWORD', 'ChangeMe!2026'),
                'is_admin' => true,
                'email_verified_at' => now(),
                // M10-T06: fresh seeds force a password change on first login so
                // the published default credentials never linger in production.
                'password_change_required' => true,
            ]
        );

        $role = Role::where('name', 'Site Admin')->where('guard_name', 'admin')->firstOrFail();
        $u->syncRoles([$role]);
    }
}
