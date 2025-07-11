<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommissionEarning>
 */
class CommissionEarningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mentor_id' => User::all()->where('role', 'mentor')->random()->id,
            'order_id' => Order::all()->random()->id,
            'amount' => fake()->numberBetween(50000, 1000000),
            'status' => fake()->randomElement(['pending', 'paid', 'cancelled']),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now')
        ];
    }
}
