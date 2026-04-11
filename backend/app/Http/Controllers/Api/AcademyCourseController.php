<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Enrolment;
use App\Services\AuditLogger;
use App\Services\GovIdVerification\GovIdVerificationClient;
use App\Services\ProvinceStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AcademyCourseController extends Controller
{
    private const CACHE_TTL_MINUTES = 10;

    public function __construct(
        protected ProvinceStatsService $provinceStatsService,
        protected GovIdVerificationClient $govIdClient,
        protected AuditLogger $auditLogger,
    ) {}

    /**
     * List published courses (cached to smooth read demand).
     */
    public function index(): JsonResponse
    {
        $courses = Cache::remember('academy.courses', self::CACHE_TTL_MINUTES * 60, function () {
            return Course::where('status', 'published')
                ->withCount(['modules', 'enrolments'])
                ->orderBy('is_mandatory', 'desc')
                ->orderBy('title')
                ->get();
        });

        return response()->json(['data' => $courses]);
    }

    /**
     * Show a course with modules and lessons (cached per course).
     */
    public function show(Course $course): JsonResponse
    {
        if ($course->status !== 'published') {
            return response()->json(['message' => 'Course not found.'], 404);
        }

        $course = Cache::remember('academy.course.' . $course->id, self::CACHE_TTL_MINUTES * 60, function () use ($course) {
            return Course::where('id', $course->id)
                ->with([
                    'modules.lessons' => fn ($q) => $q->orderBy('order'),
                    'assessments' => fn ($q) => $q->where('status', 'published')->orderBy('title'),
                ])
                ->first();
        });

        return response()->json(['data' => $course]);
    }

    /**
     * Enrol the authenticated user in a course.
     */
    public function enrol(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->authorize('enrol', $course);

        if (! $user->national_id) {
            return response()->json([
                'message' => 'Zimbabwe ID number is required before you can take this course. Please update your profile.',
                'code' => 'NATIONAL_ID_REQUIRED',
            ], 422);
        }

        // Optional (gov internal): require verified national ID for membership-granting enrolments.
        if ((bool) config('gov_id.enforce_verification_for_membership', false) && (bool) $course->grants_membership) {
            if (! $user->hasVerifiedNationalId()) {
                $result = $this->govIdClient->verifyNationalId((string) $user->national_id);
                if ($result->verified) {
                    $user->forceFill([
                        'national_id_verified_at' => now(),
                        'national_id_verification_source' => 'gov_portal',
                        'national_id_verification_ref' => $result->reference,
                    ])->save();
                } else {
                    return response()->json([
                        'message' => 'National ID could not be verified. Please contact your administrator.',
                        'code' => 'NATIONAL_ID_NOT_VERIFIED',
                        'details' => [
                            'status' => $result->status,
                            'reason' => $result->reason,
                        ],
                    ], 422);
                }
            }
        }

        $enrolment = Enrolment::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            ['status' => 'enrolled']
        );

        $this->auditLogger->log(
            action: 'academy.enrolled',
            targetType: Enrolment::class,
            targetId: $enrolment->id,
            metadata: [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_title' => $course->title,
            ],
            request: $request
        );

        return response()->json(['data' => $enrolment], 201);
    }

    /**
     * Show user's enrolment status for a course.
     */
    public function enrolment(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $enrolment = Enrolment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return response()->json(['data' => $enrolment]);
    }

    /**
     * Get the membership course (cached for quick access).
     */
    public function membershipCourse(): JsonResponse
    {
        $course = Cache::remember('academy.membership', self::CACHE_TTL_MINUTES * 60, function () {
            return Course::where('grants_membership', true)
                ->where('status', 'published')
                ->with(['modules.lessons' => fn ($q) => $q->orderBy('order')])
                ->first();
        });

        if (! $course) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => $course]);
    }

    /**
     * Lightweight summary for the Overview screen.
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'data' => [
                    'total_courses' => 0,
                    'enrolled_courses' => 0,
                    'completed_courses' => 0,
                    'has_membership' => false,
                    'assessment_attempts_count' => 0,
                    'passed_attempts' => 0,
                    'average_score' => null,
                    'pass_rate' => null,
                ],
            ]);
        }

        $totalCourses = Course::where('status', 'published')->count();

        $enrolments = Enrolment::where('user_id', $user->id)->get(['course_id', 'status']);
        $enrolledCount = $enrolments->count();
        $completedCount = $enrolments->where('status', 'completed')->count();

        // Membership status for the overview is tied to actually holding
        // at least one certificate, not just having the "member" role.
        $hasMembership = $user->certificates()->exists();

        // Learner performance (graded attempts only)
        $gradedAttempts = AssessmentAttempt::where('user_id', $user->id)
            ->where('status', 'graded')
            ->whereNotNull('score')
            ->with('assessment:id,pass_mark')
            ->get();
        $attemptsCount = $gradedAttempts->count();
        $passedCount = $gradedAttempts->filter(fn ($a) => $a->score >= (($a->assessment?->pass_mark) ?? 70))->count();
        $avgScore = $attemptsCount > 0 ? round($gradedAttempts->avg('score'), 1) : null;
        $passRate = $attemptsCount > 0 ? round(($passedCount / $attemptsCount) * 100, 1) : null;

        // Province leaderboard context (when user has province set) – batched via service
        $provinceRank = null;
        $provinceName = null;
        $provincePassed = null;
        $provinceTotalWithActivity = null;
        if ($user->province_id) {
            $province = $user->province;
            if ($province) {
                $provinceName = $province->name;
                $ctx = $this->provinceStatsService->getProvinceRankContext($user->province_id);
                $provinceRank = $ctx['rank'];
                $provincePassed = $ctx['passed'];
                $provinceTotalWithActivity = $ctx['total_with_activity'];
            }
        }

        return response()->json([
            'data' => [
                'total_courses' => $totalCourses,
                'enrolled_courses' => $enrolledCount,
                'completed_courses' => $completedCount,
                'has_membership' => $hasMembership,
                'assessment_attempts_count' => $attemptsCount,
                'passed_attempts' => $passedCount,
                'average_score' => $avgScore,
                'pass_rate' => $passRate,
                'province_name' => $provinceName,
                'province_rank' => $provinceRank,
                'province_passed' => $provincePassed,
                'province_total_with_activity' => $provinceTotalWithActivity,
            ],
        ]);
    }
}
