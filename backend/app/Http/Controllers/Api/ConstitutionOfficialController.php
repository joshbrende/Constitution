<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AmendmentOfficialPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConstitutionOfficialController extends Controller
{
    /**
     * Public metadata for the official Amendment Bill PDF (mobile + web).
     * URL uses the request host so physical devices (e.g. 192.168.x.x:8080) open the PDF correctly.
     */
    public function amendment3(Request $request): JsonResponse
    {
        if (! AmendmentOfficialPdfService::exists()) {
            return response()->json([
                'available' => false,
                'title' => config('constitution.amendment3_chapter_title'),
            ]);
        }

        return response()->json([
            'available' => true,
            'title' => config('constitution.amendment3_chapter_title'),
            'url' => AmendmentOfficialPdfService::urlForRequest($request),
        ]);
    }
}
