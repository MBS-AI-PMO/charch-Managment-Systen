<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SiteAdminSeeder::class,
            SiteSettingsSeeder::class,
            MenusSeeder::class,
            TitheFundSeeder::class,
            HeroSlideSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call(DemoContentSeeder::class);
        }
    }
}
