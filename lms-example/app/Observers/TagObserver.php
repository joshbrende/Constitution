<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Tag;

class TagObserver
{
    /**
     * Invalidate tags cache when tag data changes.
     */
    private function invalidateTagsCache(): void
    {
        CacheHelper::clearTagsCache();
    }

    public function created(Tag $tag): void
    {
        $this->invalidateTagsCache();
    }

    public function updated(Tag $tag): void
    {
        $this->invalidateTagsCache();
    }

    public function deleted(Tag $tag): void
    {
        $this->invalidateTagsCache();
    }

    public function restored(Tag $tag): void
    {
        $this->invalidateTagsCache();
    }

    public function forceDeleted(Tag $tag): void
    {
        $this->invalidateTagsCache();
    }
}
