<?php

namespace App\Http\Resources\Bookmark\Supporting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'thumbnail_path' => config('app.url') . '/' . $this->thumbnail_path,
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
        ];
    }
}
