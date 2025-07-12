<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Bookmark;
use App\Models\ClassModel;
use App\Models\WebinarRecording;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bookmark>
 */
class BookmarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Bookmark::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        // Tentukan tipe item yang akan dibookmark
        $bookmarkableTypes = [
            ClassModel::class,
            WebinarRecording::class,
        ];

        $type = $this->faker->randomElement($bookmarkableTypes);
        $item = $type::inRandomOrder()->first() ?? $type::factory()->create();

        return [
            'user_id' => $user->id,
            'bookmarkable_id' => $item->id,
            'bookmarkable_type' => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
