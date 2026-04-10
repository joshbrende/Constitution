<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AdminAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Only admins can view analytics.');
        }

        $courses = Course::with(['instructor'])
            ->withCount('enrollments')
            ->orderBy('title')
            ->get();

        $courseIds = $courses->pluck('id')->all();

        $completed = [];
        if (! empty($courseIds)) {
            $rows = Enrollment::whereIn('course_id', $courseIds)
                ->where('progress_percentage', '>=', 100)
                ->selectRaw('course_id, count(*) as n')
                ->groupBy('course_id')
                ->get();
            foreach ($rows as $row) {
                $completed[$row->course_id] = (int) $row->n;
            }
        }

        $quizAgg = [];
        if (! empty($courseIds)) {
            $rows = QuizAttempt::whereIn('course_id', $courseIds)
                ->selectRaw("course_id, count(*) as attempts, sum(case when status = 'passed' then 1 else 0 end) as passed, round(avg(percentage), 2) as avg_pct")
                ->groupBy('course_id')
                ->get();
            foreach ($rows as $row) {
                $quizAgg[$row->course_id] = [
                    'attempts' => (int) $row->attempts,
                    'passed' => (int) $row->passed,
                    'avg_pct' => $row->avg_pct !== null ? (float) $row->avg_pct : null,
                ];
            }
        }

        $items = $courses->map(function (Course $course) use ($completed, $quizAgg) {
            $enrolled = (int) $course->enrollments_count;
            $done = $completed[$course->id] ?? 0;
            $completionRate = $enrolled > 0 ? round(100 * $done / $enrolled, 1) : null;
            $qa = $quizAgg[$course->id] ?? ['attempts' => 0, 'passed' => 0, 'avg_pct' => null];
            $passRate = $qa['attempts'] > 0
                ? round(100 * $qa['passed'] / $qa['attempts'], 1)
                : null;

            return [
                'course' => $course,
                'enrolled' => $enrolled,
                'completed' => $done,
                'completion_rate' => $completionRate,
                'quiz_attempts' => $qa['attempts'],
                'quiz_passed' => $qa['passed'],
                'quiz_pass_rate' => $passRate,
                'avg_quiz_pct' => $qa['avg_pct'],
            ];
        });

        $totalEnrolled = $items->sum('enrolled');
        $totalCompleted = $items->sum('completed');
        $totalQuizAttempts = $items->sum('quiz_attempts');
        $totalQuizPassed = $items->sum('quiz_passed');
        $overallCompletionRate = $totalEnrolled > 0
            ? round(100 * $totalCompleted / $totalEnrolled, 1)
            : null;
        $overallQuizPassRate = $totalQuizAttempts > 0
            ? round(100 * $totalQuizPassed / $totalQuizAttempts, 1)
            : null;
        $avgQuizPctAll = $items->filter(fn ($r) => $r['avg_quiz_pct'] !== null)->avg('avg_quiz_pct');
        $overallAvgQuizPct = $avgQuizPctAll !== null ? round((float) $avgQuizPctAll, 1) : null;

        return view('admin.analytics', [
            'items' => $items,
            'summary' => [
                'courses' => $courses->count(),
                'total_enrolled' => $totalEnrolled,
                'total_completed' => $totalCompleted,
                'overall_completion_rate' => $overallCompletionRate,
                'total_quiz_attempts' => $totalQuizAttempts,
                'total_quiz_passed' => $totalQuizPassed,
                'overall_quiz_pass_rate' => $overallQuizPassRate,
                'overall_avg_quiz_pct' => $overallAvgQuizPct,
            ],
        ]);
    }
}

