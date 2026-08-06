<?php

namespace Database\Factories;

use App\Models\TitheFund;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TitheFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'giver_name' => $this->faker->name(),
            'fund_id' => TitheFund::factory(),
            'amount_cents' => $this->faker->numberBetween(500, 50000),
            'received_at' => now()->toDateString(),
            'method' => $this->faker->randomElement(['cash', 'bank_transfer', 'cheque', 'other']),
            'reference' => null,
            'note' => null,
            'recorded_by' => User::factory(),
        ];
    }
}
