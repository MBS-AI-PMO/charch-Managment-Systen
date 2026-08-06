<?php

namespace Database\Factories;

use App\Models\PrayerRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrayerRequestFactory extends Factory
{
    protected $model = PrayerRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(3),
            'is_public' => fake()->boolean(60),
            'is_anonymous' => fake()->boolean(20),
            'status' => fake()->randomElement(['pending', 'praying', 'answered', 'closed']),
            'pray_count' => fake()->numberBetween(0, 25),
        ];
    }
}
