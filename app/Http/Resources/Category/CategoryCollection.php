<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'class_count' => $category->subCategories->sum(fn ($sub) => $sub->classes->count()),
                    'thumbnail_path' => config('app.url') . '/' . $category->thumbnail_path,
                ];
            })
        ];
    }
}
