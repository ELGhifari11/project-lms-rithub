<?php

namespace Database\Factories;

use App\Models\PromoUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromoUsage>
 */
class PromoUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     public function definition(): array
     {
         $promo = \App\Models\Promo::inRandomOrder()->first();
         $user = \App\Models\User::inRandomOrder()->first();
         $order = \App\Models\Order::where('user_id', $user->id)->inRandomOrder()->first();

         // Kalkulasi diskon sesuai tipe
         $baseAmount = $order->total_amount ?? 100000;
         $discount = $promo->discount_type === 'fixed'
             ? $promo->discount_value
             : round($baseAmount * ($promo->discount_value / 100));

         $discount = min($discount, $promo->max_discount ?? $discount);

         return [
             'user_id' => $user->id,
             'promo_id' => $promo->id,
             'order_id' => $order?->id,
             'discount_amount' => $discount,
             'used_at' => $this->faker->dateTimeBetween('-10 days', 'now'),
             'created_at' => now(),
             'updated_at' => now(),
         ];
     }
}
