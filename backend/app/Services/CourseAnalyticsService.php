<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Enrolment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CourseAnalyticsService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function perCourseStats(): Collection
    {
        $courses = Course::query()
            ->orderBy('title')
            ->get(['id', 'code', 'title', 'status', 'audience', 'grants_membership']);

        if ($courses->isEmpty()) {
            return collect();
        }

        $courseIds = $courses->pluck('id');

        $enrolmentCounts = Enrolment::query()
            ->select('course_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed'))
            ->whereIn('course_id', $courseIds)
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $attemptStats = DB::table('assessment_attempts')
            ->join('assessments', 'assessment_attempts.assessment_id', '=', 'assessments.id')
            ->whereIn('assessments.course_id', $courseIds)
            ->where('assessment_attempts.status', 'graded')
            ->whereNotNull('assessment_attempts.score')
            ->select(
                'assessments.course_id',
                DB::raw('COUNT(*) as attempts'),
                DB::raw('SUM(CASE WHEN assessment_attempts.score >= COALESCE(assessments.pass_mark, 70) THEN 1 ELSE 0 END) as passed'),
                DB::raw('ROUND(AVG(assessment_attempts.score), 1) as avg_score')
            )
            ->groupBy('assessments.course_id')
            ->get()
            ->keyBy('course_id');

        return $courses->map(function (Course $course) use ($enrolmentCounts, $attemptStats) {
            $enrol = $enrolmentCounts->get($course->id);
            $attempts = $attemptStats->get($course->id);
            $attemptTotal = (int) ($attempts->attempts ?? 0);
            $passed = (int) ($attempts->passed ?? 0);

            return [
                'id' => $course->id,
                'code' => $course->code,
                'title' => $course->title,
                'status' => $course->status,
                'audience' => $course->audience,
                'grants_membership' => (bool) $course->grants_membership,
                'enrolments' => (int) ($enrol->total ?? 0),
                'completions' => (int) ($enrol->completed ?? 0),
                'attempts' => $attemptTotal,
                'passed_attempts' => $passed,
                'pass_rate' => $attemptTotal > 0 ? round(($passed / $attemptTotal) * 100, 1) : null,
                'avg_score' => $attempts->avg_score ?? null,
            ];
        });
    }
}
