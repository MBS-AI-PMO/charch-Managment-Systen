<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TitheFundFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->randomElement(['General Offering', 'Missions Fund', 'Building Fund', 'Outreach']);
        return [
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 9999),
            'name' => $name,
            'description' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
