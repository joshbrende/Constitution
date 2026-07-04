<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\JsonResponse;

/**
 * @group Public content
 *
 * CMS static pages by slug.
 *
 * @unauthenticated
 */
class StaticPagesController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $page = StaticPage::published()->where('slug', $slug)->first();

        if (! $page) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        return response()->json(['data' => $page]);
    }
}

