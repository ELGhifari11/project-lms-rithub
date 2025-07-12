<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Certificate::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $class = \App\Models\ClassModel::inRandomOrder()->first();

        return [
            'user_id' => $user->id,
            'class_id' => $class->id,
            'certificate_code' => strtoupper(Str::random(10)),
            'certificate_url' => $this->faker->url,
        ];
    }
}
