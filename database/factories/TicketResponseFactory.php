<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\SupportTicket;
use App\Models\TicketResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketResponse>
 */
class TicketResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = TicketResponse::class;

    public function definition(): array
    {
        return [
            'ticket_id' => SupportTicket::inRandomOrder()->value('id'),
            'responder_id' => User::inRandomOrder()->value('id'),
            'response' => $this->faker->paragraph(3),
            'created_at' => now(),
        ];
    }
}
