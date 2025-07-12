<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserBadge>
 */
class UserBadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = UserBadge::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'badge_id' => Badge::inRandomOrder()->first()->id,
            'earned_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
