<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademyBadge;
use App\Models\AcademyUserBadge;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AcademyAchievementsController extends Controller
{
    /**
     * Returns badges for the specified course (or current membership course),
     * along with locked/unlocked state and progress.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $course = Course::where('grants_membership', true)->where('status', 'published')->first();

        if (! $course) {
            return response()->json(['data' => []]);
        }

        $badges = AcademyBadge::where('course_id', $course->id)->orderBy('id')->get();

        $unlocked = [];
        if ($user) {
            $rows = AcademyUserBadge::where('user_id', $user->id)
                ->whereIn('academy_badge_id', $badges->pluck('id'))
                ->get();
            foreach ($rows as $r) {
                $unlocked[(int) $r->academy_badge_id] = $r->unlocked_at;
            }
        }

        $data = $badges->map(function (AcademyBadge $b) use ($user, $course, $unlocked) {
            $isUnlocked = isset($unlocked[$b->id]);
            $progress = 0;

            if ($user) {
                $state = $this->badgeProgressForRule($b, $user, $course, $isUnlocked);
                $progress = $state['progress'];
                $isUnlocked = $state['unlocked'];
            }

            return [
                'id' => $b->id,
                'slug' => $b->slug,
                'title' => $b->title,
                'description' => $b->description,
                'icon' => $b->icon,
                'unlocked' => $isUnlocked,
                'progress_percent' => $progress,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressForRule(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        return match ($b->rule_type) {
            'enrolled_n' => $this->badgeProgressEnrolledN($b, $user, $course, $isUnlocked),
            'completed_n' => $this->badgeProgressCompletedN($b, $user, $course, $isUnlocked),
            'pass_score_at_least' => $this->badgeProgressPassScoreAtLeast($b, $user, $course, $isUnlocked),
            'assessment_started_n' => $this->badgeProgressAssessmentStartedN($b, $user, $course, $isUnlocked),
            'assessment_submitted_n' => $this->badgeProgressAssessmentSubmittedN($b, $user, $course, $isUnlocked),
            'membership_granted' => $this->badgeProgressMembershipGranted($b, $user, $course, $isUnlocked),
            'certificate_issued' => $this->badgeProgressCertificateIssued($b, $user, $course, $isUnlocked),
            'perfect_attempt' => $this->badgeProgressPerfectAttempt($b, $user, $course, $isUnlocked),
            default => ['progress' => 0, 'unlocked' => $isUnlocked],
        };
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressEnrolledN(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        $enrolledCount = $course->enrolments()->where('user_id', $user->id)->whereIn('status', ['enrolled', 'completed'])->count();
        $progress = $this->ratioToProgressPercent($enrolledCount, $b->target_value);
        if ($enrolledCount >= $b->target_value && ! $isUnlocked) {
            $this->unlock($user->id, $b->id);
            $isUnlocked = true;
        }

        return ['progress' => $progress, 'unlocked' => $isUnlocked];
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressCompletedN(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        $completedCount = $course->enrolments()->where('user_id', $user->id)->where('status', 'completed')->count();
        $progress = $this->ratioToProgressPercent($completedCount, $b->target_value);
        if ($completedCount >= $b->target_value && ! $isUnlocked) {
            $this->unlock($user->id, $b->id);
            $isUnlocked = true;
        }

        return ['progress' => $progress, 'unlocked' => $isUnlocked];
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressPassScoreAtLeast(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        $latestPassScore = AssessmentAttempt::whereHas('assessment', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->where('user_id', $user->id)
            ->whereNotNull('score')
            ->where('status', 'graded')
            ->orderByDesc('submitted_at')
            ->value('score');

        $score = $latestPassScore ?? 0;
        $progress = $this->ratioToProgressPercent($score, $b->target_value);
        if ($score >= $b->target_value && ! $isUnlocked) {
            $this->unlock($user->id, $b->id);
            $isUnlocked = true;
        }

        return ['progress' => $progress, 'unlocked' => $isUnlocked];
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressAssessmentStartedN(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        $startedCount = AssessmentAttempt::where('user_id', $user->id)
            ->whereHas('assessment', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->where('status', 'in_progress')
            ->count();

        $progress = $this->ratioToProgressPercent($startedCount, $b->target_value);
        if ($startedCount >= $b->target_value && ! $isUnlocked) {
            $this->unlock($user->id, $b->id);
            $isUnlocked = true;
        }

        return ['progress' => $progress, 'unlocked' => $isUnlocked];
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressAssessmentSubmittedN(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        $submittedCount = AssessmentAttempt::where('user_id', $user->id)
            ->whereHas('assessment', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->where('status', 'graded')
            ->count();

        $progress = $this->ratioToProgressPercent($submittedCount, $b->target_value);
        if ($submittedCount >= $b->target_value && ! $isUnlocked) {
            $this->unlock($user->id, $b->id);
            $isUnlocked = true;
        }

        return ['progress' => $progress, 'unlocked' => $isUnlocked];
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressMembershipGranted(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        $hasMembership = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
        $progress = $hasMembership ? 100 : 0;
        if ($hasMembership && ! $isUnlocked) {
            $this->unlock($user->id, $b->id);
            $isUnlocked = true;
        }

        return ['progress' => $progress, 'unlocked' => $isUnlocked];
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressCertificateIssued(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        $hasCertificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
        $progress = $hasCertificate ? 100 : 0;
        if ($hasCertificate && ! $isUnlocked) {
            $this->unlock($user->id, $b->id);
            $isUnlocked = true;
        }

        return ['progress' => $progress, 'unlocked' => $isUnlocked];
    }

    /**
     * @return array{progress: int, unlocked: bool}
     */
    private function badgeProgressPerfectAttempt(AcademyBadge $b, User $user, Course $course, bool $isUnlocked): array
    {
        $perfect = AssessmentAttempt::where('user_id', $user->id)
            ->whereHas('assessment', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->where('status', 'graded')
            ->where('score', 100)
            ->exists();
        $progress = $perfect ? 100 : 0;
        if ($perfect && ! $isUnlocked) {
            $this->unlock($user->id, $b->id);
            $isUnlocked = true;
        }

        return ['progress' => $progress, 'unlocked' => $isUnlocked];
    }

    private function ratioToProgressPercent(int $numerator, int $denominator): int
    {
        if ($denominator <= 0) {
            return 0;
        }

        return min(100, (int) round(($numerator / $denominator) * 100));
    }

    private function unlock(int $userId, int $badgeId): void
    {
        $row = AcademyUserBadge::firstOrCreate(
            ['user_id' => $userId, 'academy_badge_id' => $badgeId],
            ['unlocked_at' => Carbon::now()]
        );

        if ($row->unlocked_at === null) {
            $row->unlocked_at = Carbon::now();
            $row->save();
        }
    }
}
