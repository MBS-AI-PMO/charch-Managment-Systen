<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);
        $starts = now()->addDays(7);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'description' => '<p>'.fake()->paragraph().'</p>',
            'location' => fake()->city(),
            'starts_at' => $starts,
            'ends_at' => (clone $starts)->addHours(2),
            'is_published' => true,
            'is_featured' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }

    public function past(): static
    {
        $starts = now()->subDays(7);
        return $this->state(fn () => [
            'starts_at' => $starts,
            'ends_at' => (clone $starts)->addHours(2),
        ]);
    }
}
