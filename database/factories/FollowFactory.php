<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil follower (student)
        $follower = User::where('role', 'student')->inRandomOrder()->first();

        // Ambil followed (mentor), pastikan bukan dirinya sendiri
        do {
            $followed = User::where('role', 'mentor')->inRandomOrder()->first();
        } while ($follower && $followed && $follower->id === $followed->id);

        return [
            'follower_id' => $follower?->id ?? User::factory()->create(['role' => 'student'])->id,
            'followed_id' => $followed?->id ?? User::factory()->create(['role' => 'mentor'])->id,
            'created_at' => now(),
        ];
    }
}
