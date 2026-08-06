<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventAttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'guest_name' => null,
            'guest_count' => 0,
            'checked_in_at' => now(),
            'method' => 'self',
            'recorded_by' => null,
        ];
    }

    public function walkIn(?string $name = null): self
    {
        return $this->state(fn () => [
            'user_id' => null,
            'guest_name' => $name ?: 'Guest',
            'method' => 'organizer',
            'recorded_by' => User::factory(),
        ]);
    }

    public function organizer(): self
    {
        return $this->state(fn () => [
            'method' => 'organizer',
            'recorded_by' => User::factory(),
        ]);
    }
}
