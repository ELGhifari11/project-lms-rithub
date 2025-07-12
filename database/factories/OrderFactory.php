<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Promo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Order::class;

     public function definition(): array
    {
        $promo = Promo::inRandomOrder()->first(); // bisa null
        $totalAmount = $this->faker->numberBetween(100000, 500000);
        $discount = $promo ? $this->faker->numberBetween(10000, 50000) : 0;
        $finalAmount = $totalAmount - $discount;

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'total_amount' => $totalAmount,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
            'payment_method' => $this->faker->randomElement(['credit_card', 'gopay', 'qris', 'bank_transfer']),
            'payment_provider' => $this->faker->randomElement(['midtrans', 'xendit']),
            'promo_id' => $promo?->id,
            'status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'canceled']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
