<?php

namespace Database\Factories;

use App\Models\Ministry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ministry>
 */
class MinistryFactory extends Factory
{
    protected $model = Ministry::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'summary' => fake()->sentence(),
            'body' => '<p>'.fake()->paragraph().'</p>',
            'leader_name' => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'sort_order' => 0,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
