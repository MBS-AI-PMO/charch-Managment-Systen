<?php

namespace Database\Factories;

use App\Models\CareRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CareRequestFactory extends Factory
{
    protected $model = CareRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category' => fake()->randomElement(['illness', 'grief', 'financial', 'food', 'other']),
            'message' => fake()->paragraph(),
            'share_with_team' => fake()->boolean(),
            'status' => 'open',
        ];
    }
}
