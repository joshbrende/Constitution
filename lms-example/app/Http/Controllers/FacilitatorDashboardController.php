<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\InstructorRequest;
use App\Models\CourseProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class FacilitatorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can access the instructor dashboard.');
        }

        $courses = $user->isAdmin()
            ? Course::with('instructor')->latest()->get()
            : Course::where('instructor_id', $user->id)->with('instructor')->latest()->get();

        $courseIds = $courses->pluck('id');
        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest('enrolled_at')
            ->take(8)
            ->get();

        $pendingSubmissionsCount = \App\Models\AssignmentSubmission::whereIn('course_id', $courseIds)
            ->whereNotIn('status', ['graded', 'returned'])
            ->count();

        // At-risk learners: progress < 50% and (no last activity or last activity ≥ 14 days ago)
        $enrollmentsForAtRisk = Enrollment::whereIn('course_id', $courseIds)
            ->select('id', 'course_id', 'user_id', 'progress_percentage')
            ->get();
        $atRiskTotal = 0;
        $atRiskByCourse = [];
        if ($enrollmentsForAtRisk->isNotEmpty()) {
            $userCoursePairs = $enrollmentsForAtRisk->map(fn ($e) => $e->user_id . '-' . $e->course_id)->unique()->values()->all();
            $progressRows = CourseProgress::whereIn('course_id', $courseIds)
                ->whereIn('user_id', $enrollmentsForAtRisk->pluck('user_id')->unique())
                ->get()
                ->keyBy(fn ($p) => $p->user_id . '-' . $p->course_id);
            $cutoff = now()->subDays(14);
            foreach ($enrollmentsForAtRisk as $e) {
                $pct = (int) ($e->progress_percentage ?? 0);
                $cp = $progressRows->get($e->user_id . '-' . $e->course_id);
                $lastActivity = $cp?->last_activity_at;
                $atRisk = $pct < 50 && (!$lastActivity || $lastActivity->lte($cutoff));
                if ($atRisk) {
                    $atRiskTotal++;
                    $atRiskByCourse[$e->course_id] = ($atRiskByCourse[$e->course_id] ?? 0) + 1;
                }
            }
        }

        // Courses with no facilitator – available for facilitators to request (exclude admins; they assign directly)
        $coursesAvailableForInstructing = collect();
        $pendingRequests = collect();
        $pendingRequestCourseIds = collect();
        if (!$user->isAdmin()) {
            $coursesAvailableForInstructing = Course::whereNull('instructor_id')
                ->latest()
                ->get();
            $pendingRequests = InstructorRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->with('course')
                ->latest()
                ->get();
            $pendingRequestCourseIds = $pendingRequests->pluck('course_id');
        }

        return view('facilitator.dashboard', compact(
            'courses', 'totalEnrollments', 'recentEnrollments', 'pendingSubmissionsCount',
            'atRiskTotal', 'atRiskByCourse',
            'coursesAvailableForInstructing', 'pendingRequests', 'pendingRequestCourseIds'
        ));
    }

    public function stats(Request $request)
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can access stats.');
        }

        $range = $request->get('range', 'all');
        $days = in_array($range, ['30', '90'], true) ? (int) $range : null;
        $since = $days ? now()->subDays($days) : null;

        $courses = $user->isAdmin()
            ? Course::with('instructor')->latest()->get()
            : Course::where('instructor_id', $user->id)->with('instructor')->latest()->get();

        $courseIds = $courses->pluck('id')->all();

        $enrolledQ = Enrollment::whereIn('course_id', $courseIds);
        if ($since) {
            $enrolledQ->where('enrolled_at', '>=', $since);
        }
        $enrolledCounts = $enrolledQ->selectRaw('course_id, count(*) as n')->groupBy('course_id')->get()->keyBy('course_id');

        $completedQ = Enrollment::whereIn('course_id', $courseIds)->where('progress_percentage', '>=', 100);
        if ($since) {
            $completedQ->where('completed_at', '>=', $since);
        }
        $completed = $completedQ->selectRaw('course_id, count(*) as n')->groupBy('course_id')->get()->keyBy('course_id');

        $quizQ = QuizAttempt::whereIn('course_id', $courseIds);
        if ($since) {
            $quizQ->where('completed_at', '>=', $since);
        }
        $quizAgg = $quizQ->selectRaw("course_id, count(*) as n, sum(case when status = 'passed' then 1 else 0 end) as p")
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $stats = [];
        foreach ($courses as $c) {
            $enrolled = (int) ($enrolledCounts->get($c->id)?->n ?? 0);
            $done = (int) ($completed->get($c->id)?->n ?? 0);
            $q = $quizAgg->get($c->id);
            $attempts = $q ? (int) $q->n : 0;
            $passed = $q ? (int) $q->p : 0;
            $stats[] = [
                'course' => $c,
                'enrollments' => $enrolled,
                'completed' => $done,
                'completion_rate' => $enrolled > 0 ? round(100 * $done / $enrolled, 1) : null,
                'quiz_attempts' => $attempts,
                'quiz_passed' => $passed,
                'quiz_pass_rate' => $attempts > 0 ? round(100 * $passed / $attempts, 1) : null,
            ];
        }

        $totalEnrolled = collect($stats)->sum('enrollments');
        $totalCompleted = collect($stats)->sum('completed');
        $totalQuizAttempts = collect($stats)->sum('quiz_attempts');
        $totalQuizPassed = collect($stats)->sum('quiz_passed');
        $summary = [
            'courses' => count($stats),
            'total_enrolled' => $totalEnrolled,
            'total_completed' => $totalCompleted,
            'overall_completion_rate' => $totalEnrolled > 0 ? round(100 * $totalCompleted / $totalEnrolled, 1) : null,
            'total_quiz_attempts' => $totalQuizAttempts,
            'total_quiz_passed' => $totalQuizPassed,
            'overall_quiz_pass_rate' => $totalQuizAttempts > 0 ? round(100 * $totalQuizPassed / $totalQuizAttempts, 1) : null,
        ];

        return view('facilitator.stats', compact('stats', 'summary', 'range'));
    }

    public function quizStats(Request $request)
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can access Knowledge Check stats.');
        }

        $courseIds = $user->isAdmin()
            ? Course::pluck('id')->all()
            : Course::where('instructor_id', $user->id)->pluck('id')->all();

        if (empty($courseIds)) {
            return view('facilitator.quiz-stats', ['quizStats' => [], 'range' => 'all']);
        }

        $range = $request->get('range', 'all');
        $days = in_array($range, ['30', '90'], true) ? (int) $range : null;
        $since = $days ? now()->subDays($days) : null;

        $quizzes = Quiz::whereIn('course_id', $courseIds)->with('course')->orderBy('title')->get();
        $quizIds = $quizzes->pluck('id')->all();

        $agg = collect();
        if (!empty($quizIds)) {
            $query = QuizAttempt::whereIn('quiz_id', $quizIds);
            if ($since) {
                $query->where('completed_at', '>=', $since);
            }
            $agg = $query->selectRaw("quiz_id, count(*) as attempts, sum(case when status = 'passed' then 1 else 0 end) as passed, round(avg(percentage), 2) as avg_pct")
                ->groupBy('quiz_id')
                ->get()
                ->keyBy('quiz_id');
        }

        $quizStats = $quizzes->map(function ($q) use ($agg) {
            $a = $agg->get($q->id);
            $attempts = $a ? (int) $a->attempts : 0;
            $passed = $a ? (int) $a->passed : 0;
            return [
                'quiz' => $q,
                'course' => $q->course,
                'attempts' => $attempts,
                'passed' => $passed,
                'pass_rate' => $attempts > 0 ? round(100 * $passed / $attempts, 1) : null,
                'avg_pct' => $a && $attempts > 0 ? (float) $a->avg_pct : null,
            ];
        })->sortBy(fn ($s) => ($s['course']->title ?? '') . '|' . ($s['quiz']->title ?? ''))->values()->all();

        return view('facilitator.quiz-stats', compact('quizStats', 'range'));
    }

    public function results(Request $request)
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can access results.');
        }

        $courses = $user->isAdmin()
            ? Course::with('instructor')->orderBy('title')->get()
            : Course::where('instructor_id', $user->id)->with('instructor')->orderBy('title')->get();

        $courseIds = $courses->pluck('id');
        $query = QuizAttempt::with(['user', 'quiz', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest('completed_at');

        $courseFilter = $request->integer('course');
        if ($courseFilter > 0) {
            $query->where('course_id', $courseFilter);
        }

        $attempts = $query->paginate(50)->withQueryString();

        return view('facilitator.results', compact('courses', 'attempts', 'courseFilter'));
    }

    public function exportResults(Request $request)
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can export results.');
        }

        $courseIds = $user->isAdmin()
            ? Course::pluck('id')
            : Course::where('instructor_id', $user->id)->pluck('id');

        $query = QuizAttempt::with(['user', 'quiz', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest('completed_at');

        $courseFilter = $request->integer('course');
        if ($courseFilter > 0) {
            $query->where('course_id', $courseFilter);
        }

        $attempts = $query->get();
        $filename = 'knowledge-check-results-' . now()->format('Y-m-d-His') . '.csv';
        $headers = ['Learner', 'Email', 'Course', 'Knowledge Check', 'Score %', 'Status', 'Completed at'];

        return response()->streamDownload(
            function () use ($attempts, $headers) {
                $out = fopen('php://output', 'w');
                fprintf($out, "\xEF\xBB\xBF");
                fputcsv($out, $headers);
                foreach ($attempts as $a) {
                    fputcsv($out, [
                        $a->user->name ?? 'User #' . $a->user_id,
                        $a->user->email ?? '',
                        $a->course?->title ?? '—',
                        $a->quiz?->title ?? 'Knowledge Check #' . $a->quiz_id,
                        $a->percentage ?? '',
                        $a->status === 'passed' ? 'Passed' : 'Failed',
                        $a->completed_at?->format('Y-m-d H:i') ?? '—',
                    ]);
                }
                fclose($out);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function learners(Request $request, Course $course)
    {
        $user = Auth::user();
        if (! $user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can access learner progress.');
        }
        if (! $user->isAdmin() && ! $user->canEditCourse($course)) {
            abort(403, 'You can only view learners for courses you instruct.');
        }

        $enrollments = Enrollment::where('course_id', $course->id)
            ->with('user')
            ->orderByDesc('enrolled_at')
            ->get();

        $userIds = $enrollments->pluck('user_id')->all();
        $progress = [];
        if (! empty($userIds)) {
            $progress = CourseProgress::where('course_id', $course->id)
                ->whereIn('user_id', $userIds)
                ->get()
                ->keyBy('user_id');
        }

        $rows = $enrollments->map(function (Enrollment $e) use ($progress) {
            $cp = $progress[$e->user_id] ?? null;
            $pct = (int) ($e->progress_percentage ?? 0);
            $lastActivity = $cp?->last_activity_at;
            $atRisk = $pct < 50 && (
                ! $lastActivity ||
                $lastActivity->lte(now()->subDays(14))
            );

            return [
                'enrollment' => $e,
                'progress' => $cp,
                'percentage' => $pct,
                'last_activity_at' => $lastActivity,
                'at_risk' => $atRisk,
            ];
        });

        $atRiskCount = $rows->where('at_risk', true)->count();

        $filter = $request->get('filter', '');
        if ($filter === 'at-risk') {
            $rows = $rows->filter(fn ($r) => $r['at_risk'])->values();
        }

        return view('facilitator.learners', [
            'course' => $course,
            'rows' => $rows,
            'filter' => $filter,
            'atRiskCount' => $atRiskCount,
        ]);
    }
}
