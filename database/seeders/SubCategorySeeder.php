<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubCategorySeeder extends Seeder
{
    public function run(): void
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

        foreach ($subCategories as $categoryName => $subcategoryNames) {
            $category = Category::where('name', $categoryName)->first();

            if (!$category) {
                throw new \Exception("Category {$categoryName} not found in database");
            }

            foreach ($subcategoryNames as $name) {
                $exists = SubCategory::where('category_id', $category->id)
                    ->where('name', $name)
                    ->exists();

                if (!$exists) {
                    SubCategory::factory()->create([
                        'category_id' => $category->id,
                        'name' => $name
                    ]);
                }
            }
        }
    }
}
