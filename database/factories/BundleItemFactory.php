<?php

namespace Database\Factories;

use App\Models\Bundle;
use App\Models\Category;
use App\Models\BundleItem;
use App\Models\ClassModel;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BundleItemFactory extends Factory
{

    protected $model = BundleItem::class;

    public function definition(): array
    {
        $itemType = $this->faker->randomElement(['class', 'category', 'sub_category', 'all']);

        $itemId = match ($itemType) {
            'class' => ClassModel::all()->random()->id,
            'category' => Category::all()->random()->id,
            'sub_category' => SubCategory::all()->random()->id,
            'all' => 0,
        };

        return [
            'bundle_id' => Bundle::all()->random()->id,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
