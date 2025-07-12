<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'class_count' => $this->subCategories->sum(fn ($sub) => $sub->classes->count()),
            'icon_path' => config('app.url') . '/' . $this->thumbnail_path,
        ];
    }
}
