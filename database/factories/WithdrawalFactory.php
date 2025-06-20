<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get mentor ID once and cache it
        $mentor = User::query()
            ->where('role', 'mentor')
            ->inRandomOrder()
            ->first();

        if (!$mentor) {
            throw new \RuntimeException('No mentor found in database');
        }

        $mentorId = $mentor->id;

        // Create wallet if it doesn't exist
        // Create wallet for mentor if it doesn't exist
        $wallet = Wallet::firstOrCreate(
            ['mentor_id' => $mentorId],
            [
                'balance' => fake()->randomFloat(2, 1000, 1000000),
                'bank_name' => fake()->company(),
                'account_holder_name' => fake()->name(),
                'bank_account_number' => fake()->creditCardNumber(),
            ]
        );
        $now = now();
        $monthAgo = $now->copy()->subMonth();

        return [
            'mentor_id' => $mentorId,
            'wallet_id' => $wallet->id,
            'amount' => fake()->numberBetween(100000, 1000000),
            'status' => fake()->randomElement(['PENDING', 'APPROVED', 'REJECTED']),
            'note' => fake()->optional()->text(200),
            'requested_at' => fake()->dateTimeBetween($monthAgo, $now),
            'processed_at' => fake()->optional()->dateTimeBetween($monthAgo, $now),
            'created_at' => fake()->dateTimeBetween($monthAgo, $now),
            'updated_at' => fake()->dateTimeBetween($monthAgo, $now)
        ];
    }
}
