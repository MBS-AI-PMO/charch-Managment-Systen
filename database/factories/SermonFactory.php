<?php

namespace Database\Factories;

use App\Models\Sermon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Sermon>
 */
class SermonFactory extends Factory
{
    protected $model = Sermon::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'summary' => fake()->sentence(),
            'body' => '<p>'.fake()->paragraph().'</p>',
            'scripture_reference' => 'John 3:16',
            'preached_on' => now()->subDays(7)->toDateString(),
            'is_published' => true,
            'downloads_enabled' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
