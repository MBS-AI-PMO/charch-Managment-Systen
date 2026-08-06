<?php

namespace Database\Factories;

use App\Models\HeroSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeroSlide>
 */
class HeroSlideFactory extends Factory
{
    protected $model = HeroSlide::class;

    public function definition(): array
    {
        return [
            'position' => fake()->numberBetween(0, 99),
            'image_path' => null,
            'eyebrow' => fake()->words(2, true),
            'heading' => fake()->sentence(4),
            'sub' => fake()->sentence(12),
            'primary_cta_url' => '/contact',
            'primary_cta_label' => 'Plan your visit',
            'secondary_cta_url' => '/sermons',
            'secondary_cta_label' => 'Watch a sermon',
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
