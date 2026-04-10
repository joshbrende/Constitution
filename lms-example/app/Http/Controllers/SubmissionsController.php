<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\UnitCompletion;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class SubmissionsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can access submissions.');
        }

        $courses = $user->isAdmin()
            ? Course::with('instructor')->orderBy('title')->get()
            : Course::where('instructor_id', $user->id)->with('instructor')->orderBy('title')->get();

        $courseIds = $courses->pluck('id');
        $query = AssignmentSubmission::with(['user', 'assignment', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest('submitted_at');

        $courseFilter = $request->integer('course');
        if ($courseFilter > 0) {
            $query->where('course_id', $courseFilter);
        }

        $statusFilter = $request->get('status', '');
        if ($statusFilter === 'pending') {
            $query->whereNotIn('status', ['graded', 'returned']);
        } elseif ($statusFilter === 'graded') {
            $query->whereIn('status', ['graded', 'returned']);
        }

        $submissions = $query->paginate(20)->withQueryString();

        return view('facilitator.submissions', compact('courses', 'submissions', 'courseFilter', 'statusFilter'));
    }

    public function grade(AssignmentSubmission $submission)
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403);
        }

        $courseIds = $user->isAdmin()
            ? Course::pluck('id')
            : Course::where('instructor_id', $user->id)->pluck('id');

        if (!$courseIds->contains($submission->course_id)) {
            abort(404);
        }

        $submission->load(['user', 'assignment', 'course']);

        return view('facilitator.submission-grade', compact('submission'));
    }

    public function update(Request $request, AssignmentSubmission $submission)
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403);
        }

        $courseIds = $user->isAdmin()
            ? Course::pluck('id')
            : Course::where('instructor_id', $user->id)->pluck('id');

        if (!$courseIds->contains($submission->course_id)) {
            abort(404);
        }

        $valid = $request->validate([
            'score' => 'required|integer|min:0|max:' . ($submission->max_points ?: 100),
            'instructor_feedback' => 'nullable|string|max:5000',
        ]);

        $submission->update([
            'score' => $valid['score'],
            'instructor_feedback' => $valid['instructor_feedback'] ?? null,
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => $user->id,
        ]);

        $submission->load('user');
        $submission->user?->notify(new \App\Notifications\AssignmentGradedNotification($submission));

        $assignment = $submission->assignment;
        $unit = $assignment?->unit;
        if ($unit) {
            $enrollment = Enrollment::where('user_id', $submission->user_id)->where('course_id', $submission->course_id)->first();
            if ($enrollment) {
                UnitCompletion::firstOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'unit_id' => $unit->id,
                    ],
                    [
                        'user_id' => $submission->user_id,
                        'course_id' => $submission->course_id,
                        'completed_at' => now(),
                    ]
                );
                $this->recalculateProgress($enrollment, $submission->course);
            }
        }

        return redirect()
            ->route('instructor.submissions.index', array_filter(['course' => $submission->course_id]))
            ->with('message', 'Submission graded.');
    }

    private function recalculateProgress(Enrollment $enrollment, \App\Models\Course $course): void
    {
        $course->load('units');
        $total = $course->units->count();
        if ($total === 0) {
            return;
        }
        $unitIds = $course->units->pluck('id');
        $done = $enrollment->unitCompletions()->whereIn('unit_id', $unitIds)->count();
        $progress = (int) round(100 * $done / $total);

        $enrollment->update([
            'progress_percentage' => $progress,
            'progress_status' => $progress >= 100 ? 'completed' : 'in_progress',
            'started_at' => $enrollment->started_at ?? now(),
            'completed_at' => $progress >= 100 ? now() : null,
        ]);

        if ($progress >= 100) {
            $cert = Certificate::ensureForUserAndCourse($enrollment->user_id, $course->id);
            if ($cert->wasRecentlyCreated ?? false) {
                $u = \App\Models\User::find($enrollment->user_id);
                if ($u) {
                    $svc = app(GamificationService::class);
                    $svc->awardPoints($u, 50);
                    $svc->ensureBadge($u, 'course-complete');
                }
            }
        }

        $cp = CourseProgress::firstOrCreate(
            ['user_id' => $enrollment->user_id, 'course_id' => $course->id],
            [
                'units_completed' => 0,
                'total_units' => $total,
                'quizzes_completed' => 0,
                'total_quizzes' => 0,
                'assignments_completed' => 0,
                'total_assignments' => 0,
                'overall_progress' => 0,
            ]
        );
        $cp->update([
            'units_completed' => $done,
            'total_units' => $total,
            'overall_progress' => $progress,
            'last_activity_at' => now(),
        ]);
    }
}
