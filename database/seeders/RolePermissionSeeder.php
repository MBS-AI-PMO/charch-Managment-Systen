<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'manage-pages',
            'manage-blog',
            'manage-sermons',
            'manage-events',
            'manage-ministries',
            'manage-media',
            'manage-menus',
            'manage-settings',
            'manage-users',
            'manage-roles',
            'manage-messages',
            'view-reports',
            // Phase 2 additions
            'manage-prayer-requests',
            'manage-knock-help',
            'manage-community-feed',
            'manage-members',
            // Phase 3 additions
            'manage-tithes',
            'manage-certificates',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'admin']);
        }

        $siteAdmin = Role::firstOrCreate(['name' => 'Site Admin', 'guard_name' => 'admin']);
        $siteAdmin->syncPermissions($perms);

        $prayer = Role::firstOrCreate(['name' => 'Prayer Organizer', 'guard_name' => 'admin']);
        $prayer->syncPermissions(['manage-prayer-requests', 'manage-knock-help', 'manage-community-feed']);

        $event = Role::firstOrCreate(['name' => 'Event Organizer', 'guard_name' => 'admin']);
        $event->syncPermissions(['manage-events', 'manage-ministries']);
    }
}
