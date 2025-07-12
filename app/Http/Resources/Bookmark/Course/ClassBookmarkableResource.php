<?php

namespace App\Http\Resources\Bookmark\Course;

use App\Http\Resources\Bookmark\Supporting\MentorResource;
use App\Http\Resources\Bookmark\Supporting\SubCategoryResource;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassBookmarkableResource extends JsonResource
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
            'item_type' => ClassModel::class,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail_path' => config('app.url') . '/' . $this->thumbnail_path,
            'duration_minutes' => $this->duration_minutes,
            'total_videos' => $this->modules->sum(function ($module) {
                return $module->contents->count();
            }),
            'total_enrollments' => $this->enrollments->count(),
            'mentor' => $this->whenLoaded('mentor', function () {
                return new MentorResource($this->mentor);
            }),
            'sub_category' => $this->whenLoaded('subCategory', function () {
                return new SubCategoryResource($this->subCategory);
            }),
        ];
    }
}
