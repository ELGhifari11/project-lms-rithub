<?php

namespace App\Http\Resources\Bookmark\Webinar;

use App\Http\Resources\Bookmark\Supporting\MentorResource;
use App\Http\Resources\Bookmark\Supporting\SubCategoryResource;
use App\Models\WebinarRecording;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebinarBookmarkableResource extends JsonResource
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
            'item_type' => WebinarRecording::class,
            'is_preview' => $this->is_preview,
            'views' => $this->views,
            'title' => $this->title,
            'description' => $this-> description,
            'thumbnail_path' => config('app.url') . '/' . $this->thumbnail_path,
            'duration_minutes' => $this->duration_minutes,
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
