<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use Illuminate\Http\JsonResponse;

class HomeBannersController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = HomeBanner::where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json(['data' => $banners]);
    }
}

