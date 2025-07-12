<?php

namespace Database\Factories;

use App\Models\Bundle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bundle>
 */
class BundleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Bundle::class;

    public function definition(): array
    {
        return [
            'name' => 'Bundle ' . ucfirst($this->faker->unique()->words(2, true)),
            'description' => $this->faker->paragraph,
            'type' => $this->faker->randomElement(['single_class', 'category', 'sub_category', 'full_access', 'custom']),
            'total_price' => $this->faker->randomFloat(2, 200, 1000),
            'validity_days' => $this->faker->randomElement([7, 14, 30, 90, 180, 365]),
            'is_active' => $this->faker->boolean(90), // 90% chance of being true
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
