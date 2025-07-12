<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Bundle;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\UserSubscription;
use App\Models\WebinarRecording;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Enrollment::class;

    public function definition(): array
    {
        $itemTypes = [
            ClassModel::class => ClassModel::inRandomOrder()->first(),
            Bundle::class => Bundle::inRandomOrder()->first(),
            WebinarRecording::class => WebinarRecording::inRandomOrder()->first(),
            User::class => User::inRandomOrder()->first()
        ];

        $selectedType = $this->faker->randomElement(array_keys($itemTypes));
        $itemType = $selectedType;
        $itemId = 1;

        if ($selectedType === User::class) {
            $itemId = User::all()->count();
        } if ($selectedType === ClassModel::class) {
            $itemId = ClassModel::all()->count();
        } if ($selectedType === Bundle::class) {
            $itemId = Bundle::all()->count();
        } if ($selectedType === WebinarRecording::class) {
            $itemId = WebinarRecording::all()->count();
        }

        return [
            'user_id' => User::inRandomOrder()->where('role', 'student')->first()->id,
            'enrollable_type' => $itemType,
            'enrollable_id' => $this->faker->numberBetween(1, $itemId),
            'subscription_id' => $this->faker->numberBetween(1, 4)
                ? UserSubscription::inRandomOrder()->first()?->id
                : null,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'canceled', 'completed']),
            'progress' => $this->faker->numberBetween(0, 100),
            'is_certificate_issued' => $this->faker->boolean(30),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
