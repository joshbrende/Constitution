<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEvaluation;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class CourseEvaluationController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('intended', route('courses.show', $course));
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment || (int) ($enrollment->progress_percentage ?? 0) < 100) {
            return redirect()
                ->route('courses.show', $course)
                ->with('message', 'Complete the course before submitting an evaluation.');
        }

        $valid = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'difficulty' => ['nullable', 'integer', 'min:1', 'max:5'],
            'would_recommend' => ['nullable', 'boolean'],
            'comments' => ['nullable', 'string', 'max:2000'],
        ]);

        CourseEvaluation::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'rating' => $valid['rating'],
                'difficulty' => $valid['difficulty'] ?? null,
                'would_recommend' => array_key_exists('would_recommend', $valid) ? (bool) $valid['would_recommend'] : true,
                'comments' => $valid['comments'] ?? null,
            ]
        );

        return redirect()
            ->route('courses.show', $course)
            ->with('message', 'Thank you for your feedback on this course.');
    }
}

