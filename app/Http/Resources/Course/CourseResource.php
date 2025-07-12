<?php

namespace App\Http\Resources\Course;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'mentor_id' => $this->mentor_id,
            'mentor_name' => optional($this->mentor)->name,
            'cover_class' => $this->thumbnail_path,
            'category' => optional(optional($this->subCategory)->category)->name,
            'sub_category' => optional($this->subCategory)->name,
            'title' => $this->title,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'content_count' => $this->modules ? $this->modules->sum(fn($module) => $module->contents->count()) : 0,
            'price' => $this->price,
            'item_type' => ClassModel::class,
        ];
    }
}
