<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassModel>
 */
class ClassModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = ClassModel::class;

    public function definition(): array
    {
        return [
            'title' => ucfirst($this->faker->unique()->sentence(3)),
            'description' => $this->faker->paragraph,
            'thumbnail_path' => asset('images/light.png'),
            'mentor_id' => User::where('role', 'mentor')->inRandomOrder()->first()?->id ?? User::factory()->state(['role' => 'mentor']),
            'sub_category_id' => SubCategory::inRandomOrder()->first()?->id ?? SubCategory::factory(),
            'duration_minutes' => $this->faker->numberBetween(30, 180),
            'price' => $this->faker->numberBetween(50000, 500000),
            'lifetime_price' => function (array $attributes) {
                $price = $attributes['price'] ?? 50000;
                return $this->faker->numberBetween($price + 10000, $price + 100000);
            },
            'status' => $this->faker->boolean,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
