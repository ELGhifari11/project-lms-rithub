<?php

namespace App\Http\Resources\Bookmark;

use App\Http\Resources\Bookmark\Course\ClassBookmarkableResource;
use App\Http\Resources\Bookmark\Webinar\WebinarBookmarkableResource;
use App\Http\Resources\Bookmark\Mentor\UserBookmarkableResource;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\WebinarRecording;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
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
            'bookmarkable_id' => $this->bookmarkable_id,
            'bookmarkable_type' => $this->bookmarkable_type,
            'bookmarkable' => $this->whenLoaded('bookmarkable', function () {
                return $this->getBookmarkableResource();
            })
        ];
    }

    private function getBookmarkableResource()
    {
        $bookmarkable = $this->bookmarkable;

        if (!$bookmarkable) {
            return null;
        }

        return match (get_class($bookmarkable)) {
            ClassModel::class => new ClassBookmarkableResource($bookmarkable),
            WebinarRecording::class => new WebinarBookmarkableResource($bookmarkable),
            User::class => new UserBookmarkableResource($bookmarkable),
            default => null,
        };
    }
}
