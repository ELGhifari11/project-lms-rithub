<?php

namespace Database\Factories;

use App\Models\Bundle;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\WebinarRecording;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommissionSetting>
 */
class CommissionSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $itemType = [ClassModel::class,WebinarRecording::class,Bundle::class,User::class];
        return [
            'item_type' => fake()->randomElement($itemType),
            'interval' => fake()->randomElement(['monthly', 'yearly', 'lifetime']),
            'is_percentage' => fake()->boolean(),
            'is_active' => fake()->boolean(),
            'fixed_commission' => fake()->numberBetween(10000, 1000000),
            'platform_share' => fake()->numberBetween(1, 100),
        ];
    }
}
