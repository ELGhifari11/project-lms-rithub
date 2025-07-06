<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition(): array
    {
        $subCategories = [
            "Programmer" => [
                'Web Developer', 'Mobile Developer', 'Data Scientist',
                'DevOps Engineer', 'Game Developer', 'Blockchain Developer', 'Cloud Architect',
                'DevSecOps Engineer', 'Full Stack Developer', 'Software Engineer', 'Backend Developer',
                'Frontend Developer', 'QA Engineer', 'System Architect'
            ],
            'Designer' => [
                'Graphic Designer', 'UI/UX Designer', 'Motion Designer', 'Product Designer',
                'Visual Designer', 'Typography Designer', 'Brand Designer', 'Illustration Designer',
                'Animation Designer', 'Fashion Designer', 'Industrial Designer', '3D Designer',
                'Print Designer', 'Package Designer'
            ],
            'Digital Marketing' => [
                'Digital Marketing', 'Social Media Marketing', 'Content Marketing',
                'Email Marketing', 'SEO Marketing', 'Pay-Per-Click Marketing',
                'Local SEO Marketing', 'Content Strategy', 'Affiliate Marketing', 'Brand Marketing',
                'Influencer Marketing', 'Marketing Analytics', 'Growth Marketing',
                'Marketing Automation'
            ]
        ];

        // Get category from database that matches the subcategory key
        $categoryName = $this->faker->randomElement(array_keys($subCategories));
        $category = Category::where('name', $categoryName)->first();

        if (!$category) {
            throw new \Exception("Category {$categoryName} not found in database");
        }

        // Get existing combinations of category-name from database
        $existingCombinations = SubCategory::select('category_id', 'name')
            ->get()
            ->map(function ($item) {
                return $item->category_id . '-' . $item->name;
            })
            ->toArray();

        // Try to find an available combination
        $found = false;
        $selectedName = null;

        // Get all possible subcategory names for this category
        $possibleNames = $subCategories[$categoryName];
        foreach ($possibleNames as $name) {
            $combination = $category->id . '-' . $name;
            if (!in_array($combination, $existingCombinations)) {
                $selectedName = $name;
                $found = true;
                break;
            }
        }

        // If no combination is available, create a dynamic name
        if (!$found) {
            $timestamp = now()->timestamp;
            $selectedName = "Custom Subcategory " . $timestamp;
        }

        return [
            'category_id' => $category->id,
            'name' => $selectedName,
            'description' => $this->faker->sentence(8),
            'thumbnail_path' => $this->faker->url,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
