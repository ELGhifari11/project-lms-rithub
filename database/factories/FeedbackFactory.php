<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Feedback;
use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Feedback::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
            'class_id' => ClassModel::inRandomOrder()->first()->id,
            'rating' => $this->faker->randomFloat(1, 1, 5), // contoh: 4.5
            'comment' => $this->faker->realTextBetween(50, 200),
            'is_approved' => $this->faker->boolean(80),
            'anonymous' => $this->faker->boolean(20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
