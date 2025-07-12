<?php

namespace Database\Factories;

use App\Models\Bundle;
use App\Models\Order;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSubscriptionFactory extends Factory
{
    protected $model = UserSubscription::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $endDate = (clone $startDate)->modify('+30 days');

        return [
            'user_id' => User::all()->where('role','student')->random()->id,
            'interval' => $this->faker->randomElement(['monthly', 'yearly']),
            'order_id' => Order::all()->random()->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['active', 'expired', 'canceled']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

}
