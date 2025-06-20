<?php

namespace Database\Factories;

use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleOfCourse>
 */
class ModuleOfCourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_id' => ClassModel::inRandomOrder()->first()->id,
            'title' => fake()->sentence(3),
            'order_index' => fake()->numberBetween(0, 100),
            'description' => fake()->paragraph(3),
            'order_index' => fake()->numberBetween(0, 100),
        ];
    }
}
