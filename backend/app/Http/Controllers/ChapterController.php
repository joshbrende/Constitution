<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;

/**
 * @group Constitution
 *
 * Chapters within a constitution document.
 *
 * @unauthenticated
 */
class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::with('part')
            ->orderBy('order')
            ->get();

        return response()->json($chapters);
    }

    public function show(Chapter $chapter)
    {
        $chapter->load(['part', 'sections']);

        return response()->json($chapter);
    }
}
