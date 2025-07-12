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
                'thumbnail_path' => 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            ],
            'Designer' => [
                'name' => 'Designer',
                'description' => 'Master graphic design and visual communication',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1609921212029-bb5a28e60960?q=80&w=2052&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            ],
            'Digital Marketing' => [
                'name' => 'Digital Marketing',
                'description' => 'Explore digital marketing strategies and tools',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=2015&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
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
