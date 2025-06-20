<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Event::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 days', '+2 weeks');
        $end = (clone $start)->modify('+'.rand(1, 3).' hours');

        $status = $this->faker->randomElement(['upcoming', 'ongoing', 'completed', 'canceled']);

        return [
            'sub_category_id' => SubCategory::inRandomOrder()->value('id'),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'start_time' => $start,
            'end_time' => $end,
            'max_participants' => $this->faker->numberBetween(10, 100),
            'price' => $this->faker->randomFloat(2, 0, 500000), // bisa Rp0 - Rp500.000
            'status' => $status,
            'thumbnail_path' => $this->faker->imageUrl(640, 480, 'event', true),
            'is_done' => in_array($status, ['completed', 'canceled']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
