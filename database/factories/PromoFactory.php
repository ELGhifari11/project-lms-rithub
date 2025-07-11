<?php

namespace Database\Factories;

use App\Models\Promo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promo>
 */
class PromoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Promo::class;

    public function definition(): array
    {

        $discountType = $this->faker->randomElement(['fixed', 'percentage']);
        $discountValue = $discountType === 'fixed'
            ? $this->faker->numberBetween(10000, 50000)
            : $this->faker->numberBetween(5, 30);

        $appliesTo = $this->faker->randomElement(['class', 'bundle', 'recording', 'all']);
        $targetId = $appliesTo === 'class'
            ? \App\Models\ClassModel::inRandomOrder()->first()->id
            : ($appliesTo === 'bundle'
                ? \App\Models\Bundle::inRandomOrder()->first()->id
                : 0); // 0 for 'all' or 'recording'

      return [
            'name' => $this->faker->catchPhrase(),
            'code' => strtoupper(Str::random(6)),
            'description' => $this->faker->sentence(),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'applies_to' => $appliesTo,
            'target_id' => $targetId,
            'min_purchase' => $this->faker->randomElement([0, 50000, 100000]),
            'max_discount' => $discountType === 'percentage' ? 75000 : null,
            'max_usage_total' => $this->faker->numberBetween(10, 100),
            'max_usage_per_user' => $this->faker->numberBetween(1, 5),
            'start_date' => now()->subDays($this->faker->numberBetween(0, 10)),
            'end_date' => now()->addDays($this->faker->numberBetween(5, 30)),
            'is_active' => true,
        ];
    }
}
