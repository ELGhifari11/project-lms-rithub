<?php

namespace App\Http\Resources\Mentor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListMentorsResource extends JsonResource
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
            'username' => $mentor->username,
            'profession' => $mentor->profession,
            'avatar_url' => config('app.url') . '/' . $mentor->avatar_url,
            'bio' => $mentor->bio,
            'is_verified' => $mentor->is_verified,
            'point' => $mentor->point ?? 0,
            'total_enrollment' => $this->resource['total_enrollment'],
            'total_classes' => $this->resource['total_classes'],
            'is_bought' => $this->resource['is_bought'],
            'item_type' => get_class($mentor),
        ];
    }
}
