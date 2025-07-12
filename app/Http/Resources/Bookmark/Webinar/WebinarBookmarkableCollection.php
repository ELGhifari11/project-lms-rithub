<?php

namespace App\Http\Resources\Bookmark\Webinar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WebinarBookmarkableCollection extends ResourceCollection
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
                    'bookmarked_at' => $bookmark->created_at?->toISOString(),
                    'webinar' => new WebinarBookmarkableResource($bookmark->bookmarkable),
                ];
            }),
        ];
    }
}
