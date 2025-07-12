<?php

namespace App\Http\Resources\Mentor;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $mentor = $this->resource['mentor'];

        return [
            'id' => $mentor->id,
            'name' => $mentor->name,
            'username' => $mentor->username ?? null,
            'bio' => $mentor->bio ?? null,
            'profession' => $mentor->profession,
            'avatar_url' => config('app.url') . '/' . $mentor->avatar_url,
            'banner_url' => config('app.url') . '/' . $mentor->cover_photo_url,
            'is_verified' => $mentor->is_verified,
            'point' => $mentor->point ?? 0,
            'price' => $mentor->price ?? 0,
            'lifetime_price' => $mentor->lifetime_price ?? 0,
            'social_media' => $mentor->social_media,
            'total_enrollment' => $this->resource['total_enrollment'],
            'is_bought' => $this->resource['is_bought'],
            'item_type' => get_class($mentor),
            'classes_taught' => CourseResource::collection($mentor->classesTaught),
        ];
    }
}
