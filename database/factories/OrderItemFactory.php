<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kelas = ClassModel::class;
        $kelasId = $kelas::pluck('id')->random();
        $orderId = Order::pluck('id')->random();

        return [
            'order_id' => $orderId,
            'item_type' => $kelas,
            'item_id' => $kelasId,
            'price' => $this->faker->numberBetween(10000, 500000),
            'interval' => $this->faker->randomElement(['monthly', 'yearly', 'lifetime']),
            'admin_fee' => $this->faker->numberBetween(1000, 10000),
            'created_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
