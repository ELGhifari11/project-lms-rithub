<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Category::class;

    public function definition(): array
    {
        $categories = [
            'Programmer' => [
                'name' => 'Programmer',
                'description' => 'Learn programming and software development skills',
                'thumbnail_path' => 'https://unsplash.com/photos/lines-of-html-codes-4hbJ-eymZ1o'
            ],
            'Designer' => [
                'name' => 'Designer',
                'description' => 'Master graphic design and visual communication',
                'thumbnail_path' => 'https://unsplash.com/photos/silver-imac-on-white-table-g-pKprPg5yw'
            ],
            'Digital Marketing' => [
                'name' => 'Digital Marketing',
                'description' => 'Explore digital marketing strategies and tools',
                'thumbnail_path' => 'https://unsplash.com/photos/laptop-computer-on-glass-top-table-hpjSkU2UYSU'
            ]
        ];

        $selectedCategory = $this->faker->unique()->randomElement($categories);

        return [
            'name' => $selectedCategory['name'],
            'description' => $selectedCategory['description'],
            'thumbnail_path' => $selectedCategory['thumbnail_path'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
