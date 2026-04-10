<?php

namespace App\Http\Controllers;

use App\Models\FacilitatorRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class FacilitatorRatingController extends Controller
{
    /**
     * Facilitator (and admins who instruct): view own facilitator ratings.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can access this page.');
        }
        $ratings = FacilitatorRating::where('instructor_id', $user->id)
            ->with(['enrollment.user', 'enrollment.course'])
            ->latest()
            ->paginate(20);
        $avg = (float) FacilitatorRating::where('instructor_id', $user->id)->avg('rating');
        $count = FacilitatorRating::where('instructor_id', $user->id)->count();
        return view('facilitator.ratings', compact('ratings', 'avg', 'count'));
    }

    /**
     * Admin: view all facilitator ratings across the platform.
     */
    public function adminIndex()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $byFacilitator = FacilitatorRating::selectRaw('instructor_id, AVG(rating) as avg_rating, COUNT(*) as count')
            ->groupBy('instructor_id')
            ->with('instructor')
            ->orderByDesc('count')
            ->get();
        $ratings = FacilitatorRating::with(['enrollment.user', 'enrollment.course', 'instructor'])
            ->latest()
            ->paginate(30);
        return view('admin.facilitator-ratings', compact('byFacilitator', 'ratings'));
    }
}
