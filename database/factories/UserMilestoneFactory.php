<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Milestone;
use App\Models\UserMilestone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMilestone>
 */
class UserMilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = UserMilestone::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'milestone_id' => Milestone::inRandomOrder()->first()->id,
            'achieved_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
