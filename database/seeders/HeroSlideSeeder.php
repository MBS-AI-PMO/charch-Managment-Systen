<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'position' => 1,
                'image_path' => null,
                'eyebrow' => 'Welcome home',
                'heading' => 'A place to belong, a faith to grow.',
                'sub' => 'Wherever you are on your spiritual journey, you are welcome here. Join us in worship, community, and service.',
                'primary_cta_url' => '/contact',
                'primary_cta_label' => 'Plan your visit',
                'secondary_cta_url' => '/about',
                'secondary_cta_label' => 'About us',
                'is_active' => true,
            ],
            [
                'position' => 2,
                'image_path' => null,
                'eyebrow' => 'This week',
                'heading' => 'From the pulpit, straight to your heart.',
                'sub' => 'Catch the latest message from our teaching team and revisit the series shaping our church.',
                'primary_cta_url' => '/sermons',
                'primary_cta_label' => 'Watch a sermon',
                'secondary_cta_url' => '/news',
                'secondary_cta_label' => 'Read the news',
                'is_active' => true,
            ],
            [
                'position' => 3,
                'image_path' => null,
                'eyebrow' => 'Coming up',
                'heading' => 'Gather. Worship. Serve. Together.',
                'sub' => 'Events, retreats, and small groups for every season of life. Find your next step.',
                'primary_cta_url' => '/events',
                'primary_cta_label' => 'See events',
                'secondary_cta_url' => '/ministries',
                'secondary_cta_label' => 'Find a ministry',
                'is_active' => true,
            ],
        ];

        foreach ($slides as $row) {
            HeroSlide::updateOrCreate(
                ['position' => $row['position']],
                $row,
            );
        }
    }
}
