<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\DialogueMessage;
use App\Models\Enrolment;
use App\Models\PriorityProject;
use App\Models\Province;
use App\Models\User;
use App\Services\ProvinceStatsService;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAnalyticsController extends Controller
{
    public function __construct(
        protected ProvinceStatsService $provinceStatsService
    ) {}

    public function index(): View
    {
        $now = Carbon::now();

        return view('admin.analytics.index', $this->buildAnalyticsViewData($now));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAnalyticsViewData(Carbon $now): array
    {
        $monthAgo = $now->copy()->subMonth();
        $firstPassedByUser = $this->loadFirstPassedByUser();

        $totalMembers = $firstPassedByUser->count();
        $newMembersLast30 = $firstPassedByUser->filter(fn ($r) => $r->first_passed_at && Carbon::parse($r->first_passed_at)->gte($monthAgo))->count();

        $totalCourses = Course::count();
        $publishedCourses = Course::where('status', 'published')->count();
        $membershipCourse = Course::where('grants_membership', true)->where('status', 'published')->first();

        $membershipCourseEnrolments = 0;
        $membershipCourseCompletions = 0;
        if ($membershipCourse) {
            $membershipCourseEnrolments = $membershipCourse->enrolments()->count();
            $membershipCourseCompletions = $membershipCourse->enrolments()->whereNotNull('completed_at')->count();
        }

        $totalEnrolments = Enrolment::count();
        $completedEnrolments = Enrolment::whereNotNull('completed_at')->count();
        $avgCompletionPct = $totalEnrolments > 0 ? round(($completedEnrolments / $totalEnrolments) * 100, 1) : null;

        $totalCertificates = Certificate::count();
        $certificatesLast30 = Certificate::where('issued_at', '>=', $monthAgo)->count();

        $priorPeriodStart = $monthAgo->copy()->subMonth();
        $membersPrev30 = $firstPassedByUser->filter(fn ($r) => $r->first_passed_at && Carbon::parse($r->first_passed_at)->gte($priorPeriodStart) && Carbon::parse($r->first_passed_at)->lt($monthAgo))->count();
        $membersCurr30 = $newMembersLast30;
        $membersImprovement = $this->periodImprovementPercent($membersPrev30, $membersCurr30);

        $certificatesPrev30 = Certificate::where('issued_at', '>=', $priorPeriodStart)->where('issued_at', '<', $monthAgo)->count();
        $certificatesCurr30 = $certificatesLast30;
        $certificatesImprovement = $this->periodImprovementPercent($certificatesPrev30, $certificatesCurr30);

        $completionsPrev30 = Enrolment::whereNotNull('completed_at')
            ->where('completed_at', '>=', $priorPeriodStart)
            ->where('completed_at', '<', $monthAgo)
            ->count();
        $completionsCurr30 = Enrolment::whereNotNull('completed_at')->where('completed_at', '>=', $monthAgo)->count();
        $completionsImprovement = $this->periodImprovementPercent($completionsPrev30, $completionsCurr30);

        $totalDialogueMessages = DialogueMessage::count();
        $dialogueMessagesLast30 = DialogueMessage::where('created_at', '>=', $monthAgo)->count();

        $publishedProjects = PriorityProject::published()->count();
        $totalProjectLikes = PriorityProject::sum('likes_count');

        $certificatesByMonth = Certificate::selectRaw('DATE_FORMAT(issued_at, "%Y-%m") as ym, COUNT(*) as total')
            ->where('issued_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $assessmentBlock = $this->buildAssessmentMetricsBlock($now);

        $membersByMonth = collect($firstPassedByUser)
            ->filter(fn ($r) => $r->first_passed_at && Carbon::parse($r->first_passed_at)->gte($now->copy()->subMonths(5)->startOfMonth()))
            ->groupBy(fn ($r) => Carbon::parse($r->first_passed_at)->format('Y-m'))
            ->map->count()
            ->sortKeys();

        $activeUsersByDay = AuditLog::whereIn('action', ['auth.web.logged_in', 'auth.api.logged_in'])
            ->where('created_at', '>=', $now->copy()->subDays(7)->startOfDay())
            ->selectRaw('DATE(created_at) as dt, COUNT(DISTINCT actor_user_id) as total')
            ->groupBy('dt')
            ->orderBy('dt')
            ->pluck('total', 'dt');

        $inactiveUsersCount = $this->countInactiveUsers($now);

        $recentActivity = AuditLog::with('actor:id,name,email')
            ->latest()
            ->limit(20)
            ->get();

        $activityByDate = $this->buildActivityByDate($now);

        $provinceStats = $this->provinceStatsService->getStatsForAllProvinces();
        $provinceLeaderboard = $provinceStats
            ->filter(fn ($s) => $s['passed'] > 0 || $s['attempts'] > 0)
            ->sortByDesc('passed')
            ->values()
            ->map(fn ($s, $i) => array_merge($s, ['rank' => $i + 1]))
            ->values();

        return array_merge([
            'totalMembers' => $totalMembers,
            'newMembersLast30' => $newMembersLast30,
            'totalCourses' => $totalCourses,
            'publishedCourses' => $publishedCourses,
            'membershipCourse' => $membershipCourse,
            'membershipCourseEnrolments' => $membershipCourseEnrolments,
            'membershipCourseCompletions' => $membershipCourseCompletions,
            'totalEnrolments' => $totalEnrolments,
            'completedEnrolments' => $completedEnrolments,
            'avgCompletionPct' => $avgCompletionPct,
            'totalCertificates' => $totalCertificates,
            'certificatesLast30' => $certificatesLast30,
            'membersPrev30' => $membersPrev30,
            'membersCurr30' => $membersCurr30,
            'membersImprovement' => $membersImprovement,
            'certificatesPrev30' => $certificatesPrev30,
            'certificatesCurr30' => $certificatesCurr30,
            'certificatesImprovement' => $certificatesImprovement,
            'completionsPrev30' => $completionsPrev30,
            'completionsCurr30' => $completionsCurr30,
            'completionsImprovement' => $completionsImprovement,
            'totalDialogueMessages' => $totalDialogueMessages,
            'dialogueMessagesLast30' => $dialogueMessagesLast30,
            'publishedProjects' => $publishedProjects,
            'totalProjectLikes' => $totalProjectLikes,
            'certificatesByMonth' => $certificatesByMonth,
            'membersByMonth' => $membersByMonth,
            'activeUsersByDay' => $activeUsersByDay,
            'inactiveUsersCount' => $inactiveUsersCount,
            'recentActivity' => $recentActivity,
            'activityByDate' => $activityByDate,
            'provinceStats' => $provinceStats,
            'provinceLeaderboard' => $provinceLeaderboard,
        ], $assessmentBlock);
    }

    /**
     * @return Collection<int, object>
     */
    private function loadFirstPassedByUser(): Collection
    {
        return DB::table('assessment_attempts')
            ->join('assessments', 'assessment_attempts.assessment_id', '=', 'assessments.id')
            ->where('assessment_attempts.status', 'graded')
            ->whereNotNull('assessment_attempts.score')
            ->whereRaw('assessment_attempts.score >= assessments.pass_mark')
            ->select('assessment_attempts.user_id', DB::raw('MIN(assessment_attempts.submitted_at) as first_passed_at'))
            ->groupBy('assessment_attempts.user_id')
            ->get();
    }

    private function periodImprovementPercent(int $previousPeriodCount, int $currentPeriodCount): float
    {
        if ($previousPeriodCount > 0) {
            return round((($currentPeriodCount - $previousPeriodCount) / $previousPeriodCount) * 100, 1);
        }

        return $currentPeriodCount > 0 ? 100.0 : 0.0;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAssessmentMetricsBlock(Carbon $now): array
    {
        $totalAttempts = AssessmentAttempt::where('status', 'graded')->whereNotNull('score')->count();
        $passedAttempts = AssessmentAttempt::where('assessment_attempts.status', 'graded')
            ->whereNotNull('assessment_attempts.score')
            ->join('assessments', 'assessment_attempts.assessment_id', '=', 'assessments.id')
            ->whereRaw('assessment_attempts.score >= assessments.pass_mark')
            ->count();
        $avgScore = AssessmentAttempt::where('status', 'graded')->whereNotNull('score')->avg('score');
        $passRate = $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : null;
        $attemptsByMonth = AssessmentAttempt::selectRaw('DATE_FORMAT(submitted_at, "%Y-%m") as ym, COUNT(*) as total')
            ->where('status', 'graded')
            ->where('submitted_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');
        $failedAttempts = $totalAttempts - $passedAttempts;

        return [
            'totalAttempts' => $totalAttempts,
            'passedAttempts' => $passedAttempts,
            'failedAttempts' => $failedAttempts,
            'avgScore' => $avgScore,
            'passRate' => $passRate,
            'attemptsByMonth' => $attemptsByMonth,
        ];
    }

    private function countInactiveUsers(Carbon $now): int
    {
        $activeUserIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $now->copy()->subDays(30)->timestamp)
            ->distinct()
            ->pluck('user_id')
            ->values();

        if ($activeUserIds->isEmpty()) {
            return User::count();
        }

        return User::whereNotIn('id', $activeUserIds)->count();
    }

    /**
     * @return \Illuminate\Support\Collection<string, int>
     */
    private function buildActivityByDate(Carbon $now): Collection
    {
        $activityStart = $now->copy()->subDays(34)->startOfDay();
        $loginsByDate = AuditLog::whereIn('action', ['auth.web.logged_in', 'auth.api.logged_in'])
            ->where('created_at', '>=', $activityStart)
            ->selectRaw('DATE(created_at) as dt, COUNT(*) as cnt')
            ->groupBy('dt')
            ->pluck('cnt', 'dt');
        $completionsByDate = Enrolment::whereNotNull('completed_at')
            ->where('completed_at', '>=', $activityStart)
            ->selectRaw('DATE(completed_at) as dt, COUNT(*) as cnt')
            ->groupBy('dt')
            ->pluck('cnt', 'dt');
        $certificatesByDate = Certificate::where('issued_at', '>=', $activityStart)
            ->selectRaw('DATE(issued_at) as dt, COUNT(*) as cnt')
            ->groupBy('dt')
            ->pluck('cnt', 'dt');

        $activityByDate = collect();
        for ($i = 0; $i < 35; $i++) {
            $d = $activityStart->copy()->addDays($i)->format('Y-m-d');
            $activityByDate[$d] = ($loginsByDate[$d] ?? 0) + ($completionsByDate[$d] ?? 0) + ($certificatesByDate[$d] ?? 0);
        }

        return $activityByDate;
    }

    public function exportEnrolments(): Response
    {
        $this->authorize('admin.section', 'analytics');
        $enrolments = Enrolment::with(['user:id,email', 'course:id,title'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = "user_id,email,course_id,course_title,status,enrolled_at,completed_at\n";
        foreach ($enrolments as $e) {
            $csv .= sprintf(
                "%s,\"%s\",%s,\"%s\",%s,%s,%s\n",
                $e->user_id,
                str_replace('"', '""', $e->user?->email ?? ''),
                $e->course_id,
                str_replace('"', '""', $e->course?->title ?? ''),
                $e->status,
                $e->created_at?->toIso8601String() ?? '',
                $e->completed_at?->toIso8601String() ?? ''
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="enrolments-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function exportAttempts(): Response
    {
        $this->authorize('admin.section', 'analytics');
        $attempts = AssessmentAttempt::with(['user:id,email', 'assessment.course:id,title'])
            ->where('status', 'graded')
            ->whereNotNull('score')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $csv = "attempt_id,user_id,email,assessment_id,assessment_title,course_title,score,passed,started_at,submitted_at\n";
        foreach ($attempts as $a) {
            $passMark = $a->assessment?->pass_mark ?? 70;
            $passed = $a->score >= $passMark ? 'yes' : 'no';
            $csv .= sprintf(
                "%s,%s,\"%s\",%s,\"%s\",\"%s\",%s,%s,%s,%s\n",
                $a->id,
                $a->user_id,
                str_replace('"', '""', $a->user?->email ?? ''),
                $a->assessment_id,
                str_replace('"', '""', $a->assessment?->title ?? ''),
                str_replace('"', '""', $a->assessment?->course?->title ?? ''),
                $a->score ?? '',
                $passed,
                $a->started_at?->toIso8601String() ?? '',
                $a->submitted_at?->toIso8601String() ?? ''
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="assessment-attempts-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
