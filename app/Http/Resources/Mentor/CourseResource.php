<?php

namespace App\Http\Resources\Mentor;

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
            'sub_category_id' => optional($this->subCategory)->id,
            'category_id' => optional(optional($this->subCategory)->category)->id,
            'title' => $this->title,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'status' => $this->status,
            'thumbnail_path' => config('app.url') . '/' . $this->thumbnail_path,
            'total_video' => $this->modules ? $this->modules->sum(fn($module) => $module->contents->count()) : 0,
            'total_enrollment' => $this->enrollments()->count(),
            'created_at' => $this->created_at,
            'is_bookmarked' => $this->is_bookmarked ?? false,
            'item_type' => get_class($this->resource),
        ];
    }
}
