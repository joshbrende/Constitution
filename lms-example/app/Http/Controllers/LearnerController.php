<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Tag;
use App\Models\UnitCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LearnerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user()->load('badges');

        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('course')
            ->orderByDesc('enrolled_at')
            ->get();

        $inProgress = $enrollments->filter(fn ($e) => (int) ($e->progress_percentage ?? 0) < 100)->take(6);
        $completed = $enrollments->filter(fn ($e) => (int) ($e->progress_percentage ?? 0) >= 100)->take(6);
        $inProgressCount = $enrollments->filter(fn ($e) => (int) ($e->progress_percentage ?? 0) < 100)->count();

        $certificates = Certificate::where('user_id', $user->id)
            ->with('course')
            ->latest('issued_at')
            ->take(6)
            ->get();
        $certificateCount = Certificate::where('user_id', $user->id)->count();

        $recentCompletions = UnitCompletion::where('user_id', $user->id)
            ->with(['course', 'unit'])
            ->latest('completed_at')
            ->take(5)
            ->get();

        $recommendedCourses = collect();
        $completedCourseIds = $enrollments
            ->filter(fn ($e) => (int) ($e->progress_percentage ?? 0) >= 100)
            ->pluck('course_id')
            ->all();

        if (! empty($completedCourseIds)) {
            $completedCourses = Course::with('tags')
                ->whereIn('id', $completedCourseIds)
                ->get();

            $tagIds = $completedCourses->flatMap(fn ($c) => $c->tags->pluck('id'))->unique()->values();

            if ($tagIds->isNotEmpty()) {
                $alreadyEnrolledCourseIds = $enrollments->pluck('course_id')->all();
                $recommendedCourses = Course::query()
                    ->where('status', 'published')
                    ->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds))
                    ->whereNotIn('id', $alreadyEnrolledCourseIds)
                    ->with('instructor')
                    ->orderByDesc('created_at')
                    ->take(3)
                    ->get();
            }
        }

        $resumeEnrollment = null;
        $resumeCourse = null;
        $resumeUnit = null;
        if ($user->last_learn_course_id && $user->last_learn_unit_id) {
            $resumeEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $user->last_learn_course_id)
                ->first();
            if ($resumeEnrollment) {
                $resumeCourse = $resumeEnrollment->course;
                $resumeUnit = \App\Models\Unit::find($user->last_learn_unit_id);
            }
        }

        return view('learner.dashboard', [
            'user' => $user,
            'inProgress' => $inProgress,
            'inProgressCount' => $inProgressCount,
            'completed' => $completed,
            'certificates' => $certificates,
            'certificateCount' => $certificateCount,
            'recentCompletions' => $recentCompletions,
            'resumeEnrollment' => $resumeEnrollment,
            'resumeCourse' => $resumeCourse,
            'resumeUnit' => $resumeUnit,
            'recommendedCourses' => $recommendedCourses,
        ]);
    }
}
