<?php

namespace App\Http\Resources\Bookmark\Mentor;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBookmarkableResource extends JsonResource
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
            'item_type' => User::class,
            'name' => $this->name,
            'avatar_url' => config('app.url') . '/' . $this->avatar_url,
            'profession' => $this->profession,
            'classes_taught' => $this->classesTaught->count(),
            'is_verified' => $this->is_verified,
        ];
    }
}
