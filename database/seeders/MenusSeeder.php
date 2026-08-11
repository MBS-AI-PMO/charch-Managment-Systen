<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenusSeeder extends Seeder
{
    public function run(): void
    {
        $main = Menu::updateOrCreate(['slug' => 'main'], ['name' => 'Main Navigation']);

        $items = [
            ['label' => 'Home', 'link_type' => 'route', 'link_value' => 'site.home'],
            ['label' => 'About', 'link_type' => 'route', 'link_value' => 'site.about'],
            ['label' => 'Sermons', 'link_type' => 'route', 'link_value' => 'site.sermons.index'],
            ['label' => 'Events', 'link_type' => 'route', 'link_value' => 'site.events.index'],
            ['label' => 'Ministries', 'link_type' => 'route', 'link_value' => 'site.ministries.index'],
            ['label' => 'Our Churches', 'link_type' => 'route', 'link_value' => 'site.churches'],
            ['label' => 'Q&A', 'link_type' => 'route', 'link_value' => 'site.qa.index'],
            ['label' => 'Contact', 'link_type' => 'route', 'link_value' => 'site.contact'],
        ];

        // Remove News from the public main menu (Events covers gatherings).
        MenuItem::where('menu_id', $main->id)
            ->where(function ($q) {
                $q->where('label', 'News')
                    ->orWhere('link_value', 'site.blog.index')
                    ->orWhere('link_value', 'site.news.index');
            })
            ->delete();

        foreach ($items as $i => $row) {
            MenuItem::updateOrCreate(
                ['menu_id' => $main->id, 'link_type' => $row['link_type'], 'link_value' => $row['link_value']],
                $row + ['sort_order' => $i, 'parent_id' => null]
            );
        }

        Menu::updateOrCreate(['slug' => 'footer'], ['name' => 'Footer Quick Links']);
    }
}
