<?php

namespace App\Http\Resources\Bookmark\Mentor;

use App\Http\Resources\Bookmark\UserBookmarkableResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MentorBookmarkableCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            $this->collection->map(function ($bookmark) {
                return [
                    'bookmark_id' => $bookmark->id,
                    'bookmarked_at' => $bookmark->created_at->toISOString(),
                    'mentor' => new UserBookmarkableResource($bookmark->bookmarkable)
                ];
            })
        ];
    }
}
