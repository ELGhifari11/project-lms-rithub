<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Badge>
 */
class BadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Badge::class;

    public function definition(): array
    {
        $rules = [
            'classes_joined' => 'Mengikuti sejumlah kelas',
            'feedbacks_given' => 'Memberi sejumlah feedback',
            'points_reached' => 'Mengumpulkan sejumlah poin',
        ];

        $ruleType = $this->faker->randomElement(array_keys($rules));

        return [
            'name' => 'Badge ' . $this->faker->unique()->word(),
            'description' => $rules[$ruleType],
            'icon' => $this->faker->imageUrl(100, 100, 'badges'),
            'rule_type' => $ruleType,
            'rule_value' => $this->faker->numberBetween(1, 10),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
