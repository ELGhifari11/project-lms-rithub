<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
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
            'bank_name' => fake()->randomElement(['BCA', 'BNI', 'Mandiri', 'BRI']),
            'account_holder_name' => fake()->name(),
            'bank_account_number' => fake()->numerify('##########'),
            'balance' => fake()->numberBetween(0, 10000000)
        ];
        
    }
}
