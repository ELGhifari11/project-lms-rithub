<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Point>
 */
class PointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Point::class;

    public function definition(): array
    {

        $sources = [
            'join_class' => 'Bergabung ke kelas',
            'testimoni' => 'Memberikan testimoni',
            'buy_bundle' => 'Membeli bundle',
            'event' => 'Ikut event',
            'other' => 'Aktivitas lainnya'
        ];

        $source = $this->faker->randomElement(array_keys($sources));

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'source' => $source,
            'description' => $sources[$source],
            'point_value' => $this->faker->numberBetween(5, 50),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
