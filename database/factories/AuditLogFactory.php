<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = AuditLog::class;

    public function definition(): array
    {

        $actions = [
            'login',
            'logout',
            'viewed_content',
            'updated_profile',
            'enrolled_class',
            'made_purchase',
            'submitted_feedback',
            'used_promo',
            'joined_event',
        ];

        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'action' => $this->faker->randomElement($actions),
            'details' => [
                'browser' => $this->faker->userAgent,
                'platform' => $this->faker->randomElement(['Windows', 'macOS', 'Linux', 'Android', 'iOS']),
                'description' => $this->faker->sentence(),
            ],
            'ip_address' => $this->faker->ipv4,
        ];
    }
}
