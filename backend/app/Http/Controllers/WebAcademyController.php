<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\AdminAccessService;
use Illuminate\View\View;

class WebAcademyController extends Controller
{
    /**
     * Academy course list for authenticated members (dashboard "Academy").
     */
    public function home(AdminAccessService $adminAccess): View
    {
        $courses = Course::withCount(['modules', 'assessments'])
            ->orderByRaw("CASE status WHEN 'published' THEN 0 WHEN 'draft' THEN 1 ELSE 2 END")
            ->orderBy('is_mandatory', 'desc')
            ->orderBy('title')
            ->get();

        $canManage = $adminAccess->canAccessSection(auth()->user(), 'academy');

        return view('sections.academy', compact('courses', 'canManage'));
    }
}
