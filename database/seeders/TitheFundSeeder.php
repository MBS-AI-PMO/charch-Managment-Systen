<?php

namespace Database\Seeders;

use App\Models\TitheFund;
use Illuminate\Database\Seeder;

class TitheFundSeeder extends Seeder
{
    public function run(): void
    {
        $funds = [
            ['slug' => 'general',  'name' => 'General Offering', 'description' => 'General tithes and offerings for the church.', 'sort_order' => 1],
            ['slug' => 'missions', 'name' => 'Missions Fund',    'description' => 'Supports missionary work and outreach.',         'sort_order' => 2],
            ['slug' => 'building', 'name' => 'Building Fund',    'description' => 'Maintenance and expansion of facilities.',        'sort_order' => 3],
        ];

        foreach ($funds as $row) {
            TitheFund::updateOrCreate(['slug' => $row['slug']], $row + ['is_active' => true]);
        }
    }
}
