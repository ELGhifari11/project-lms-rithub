<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Banner::class;

    public function definition(): array
    {

        $targetTypes = ['class', 'bundle', 'category', 'event', 'promo', 'external'];
        $targetType = $this->faker->randomElement($targetTypes);

        // Simulasi ID tergantung jenis target (bisa kamu sesuaikan dengan data yang sudah ada)
        $targetId = match ($targetType) {
            'class' => \App\Models\ClassModel::inRandomOrder()->value('id'),
            'bundle' => \App\Models\Bundle::inRandomOrder()->value('id'),
            'category' => \App\Models\Category::inRandomOrder()->value('id'),
            'event' => \App\Models\Event::inRandomOrder()->value('id'),
            'promo' => \App\Models\Promo::inRandomOrder()->value('id'),
            'external' => null,
        };

       return [
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->sentence(6),
            'image_url' => $this->faker->imageUrl(800, 400, 'business', true, 'Banner'),
            'target_type' => $targetType,
            'target_id' => $targetId,
            'link_url' => $targetType === 'external' ? $this->faker->url : '',  
            'start_date' => now()->subDays(rand(0, 10)),
            'end_date' => now()->addDays(rand(5, 30)),
            'is_active' => $this->faker->boolean(80),
            'order_index' => $this->faker->numberBetween(1, 50),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
