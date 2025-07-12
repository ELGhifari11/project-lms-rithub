<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\ClassModel;
use App\Models\ModuleOfCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Milestone>
 */
class MilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Milestone::class;

    public function definition(): array
    {
        return [
            'class_id' => ClassModel::inRandomOrder()->first()->id,
            'title' => 'Milestone: ' . $this->faker->catchPhrase,
            'description' => $this->faker->paragraph(3),
            'learning_objectives' => $this->faker->paragraphs(2, true),
            'requirements' => $this->faker->paragraphs(1, true),
            'required_progress_percentage' => $this->faker->randomElement([25, 50, 75, 100]),
            'estimated_hours' => $this->faker->numberBetween(1, 20),
            'resources' => [
                [
                    'title' => 'Resource: ' . $this->faker->catchPhrase(),
                    'url' => $this->faker->url(),
                ],
            ],
            'difficulty_level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'is_mandatory' => $this->faker->boolean(80), // 80% chance of being mandatory
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
