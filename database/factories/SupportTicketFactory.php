<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'subject' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
