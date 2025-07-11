<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventAttendee>
 */
class EventAttendeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = EventAttendee::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::inRandomOrder()->value('id'),
            'user_id' => User::inRandomOrder()->value('id'),
            'status' => $this->faker->randomElement(['attending', 'interested', 'not_interested']),
        ];
    }
}
