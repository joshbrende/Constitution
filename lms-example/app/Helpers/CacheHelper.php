<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use App\Models\Course;
use App\Models\Tag;
use App\Models\User;

class CacheHelper
{
    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 60;

    /**
     * Get cached courses list
     */
    public static function getCourses($key = 'courses_list', $callback = null)
    {
        return Cache::remember($key, now()->addMinutes(self::CACHE_DURATION), function () use ($callback) {
            return $callback ? $callback() : Course::with(['instructor', 'tags'])->where('status', 'published')->get();
        });
    }

    /**
     * Get cached tags
     */
    public static function getTags()
    {
        return Cache::remember('tags_list', now()->addMinutes(self::CACHE_DURATION * 24), function () {
            return Tag::orderBy('name')->get();
        });
    }

    /**
     * Clear courses cache
     */
    public static function clearCoursesCache()
    {
        Cache::forget('courses_list');
    }

    /**
     * Clear tags cache
     */
    public static function clearTagsCache()
    {
        Cache::forget('tags_list');
    }

    /**
     * Clear all cache
     */
    public static function clearAll()
    {
        Cache::flush();
    }
}
