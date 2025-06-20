<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\SubCategory;
use App\Models\WebinarRecording;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebinarRecording>
 */
class WebinarRecordingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = WebinarRecording::class;

    public function definition(): array
    {
        return [
            'mentor_id' => \App\Models\User::where('role', 'mentor')->inRandomOrder()->first()?->id ?? null,
            'sub_category_id' => SubCategory::inRandomOrder()->first()?->id ?? SubCategory::factory(),
            'is_preview' => $this->faker->boolean,
            'thumbnail_path' => $this->faker->imageUrl(),
            'title' => 'Webinar: ' . $this->faker->catchPhrase,
            'content_path' => $this->faker->url,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10000, 100000),
            'lifetime_price' => $this->faker->randomFloat(2, 10000, 100000),
            'views' => $this->faker->numberBetween(0, 10000),
            'duration' => $this->faker->numberBetween(15, 120),
            'status' => $this->faker->boolean,
            'recorded_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
