<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\JsonResponse;

class ProvinceController extends Controller
{
    /**
     * List all Zimbabwe provinces (for profile picker, etc.).
     */
    public function index(): JsonResponse
    {
        $provinces = Province::orderBy('sort_order')->get(['id', 'name', 'code']);

        return response()->json(['data' => $provinces]);
    }
}
