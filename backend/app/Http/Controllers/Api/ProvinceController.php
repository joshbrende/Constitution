<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\JsonResponse;

/**
 * @group Public content
 *
 * Zimbabwe provinces for registration and profile pickers.
 *
 * @unauthenticated
 */
class ProvinceController extends Controller
{
    /**
     * List all Zimbabwe provinces
     *
     * Returns provinces in constitution sort order (Bulawayo id=1, Harare id=2, …).
     *
     * @response 200 {"data":[{"id":1,"name":"Bulawayo","code":"bulawayo"},{"id":2,"name":"Harare","code":"harare"}]}
     */
    public function index(): JsonResponse
    {
        $provinces = Province::orderBy('sort_order')->get(['id', 'name', 'code']);

        return response()->json(['data' => $provinces]);
    }
}
