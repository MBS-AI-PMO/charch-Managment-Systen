<?php

namespace Database\Factories;

use App\Models\FeedPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedPostFactory extends Factory
{
    protected $model = FeedPost::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory()->state(['is_admin' => true]),
            'title' => fake()->sentence(5),
            'body' => '<p>'.fake()->paragraph(3).'</p>',
            'pinned' => false,
            'published_at' => now(),
        ];
    }
}
