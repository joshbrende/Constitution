<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademyBadge;
use App\Models\AcademyUserBadge;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Certificate;
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
                // Compute progress and unlock if achieved
                if ($b->rule_type === 'enrolled_n') {
                    $enrolledCount = $course->enrolments()->where('user_id', $user->id)->whereIn('status', ['enrolled', 'completed'])->count();
                    $progress = $b->target_value > 0 ? min(100, (int) round(($enrolledCount / $b->target_value) * 100)) : 0;
                    if ($enrolledCount >= $b->target_value && ! $isUnlocked) {
                        $this->unlock($user->id, $b->id);
                        $isUnlocked = true;
                    }
                } elseif ($b->rule_type === 'completed_n') {
                    $completedCount = $course->enrolments()->where('user_id', $user->id)->where('status', 'completed')->count();
                    $progress = $b->target_value > 0 ? min(100, (int) round(($completedCount / $b->target_value) * 100)) : 0;
                    if ($completedCount >= $b->target_value && ! $isUnlocked) {
                        $this->unlock($user->id, $b->id);
                        $isUnlocked = true;
                    }
                } elseif ($b->rule_type === 'pass_score_at_least') {
                    $latestPassScore = AssessmentAttempt::whereHas('assessment', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })->where('user_id', $user->id)
                        ->whereNotNull('score')
                        ->where('status', 'graded')
                        ->orderByDesc('submitted_at')
                        ->value('score');

                    $score = $latestPassScore ?? 0;
                    $progress = $b->target_value > 0 ? min(100, (int) round(($score / $b->target_value) * 100)) : 0;
                    if ($score >= $b->target_value && ! $isUnlocked) {
                        $this->unlock($user->id, $b->id);
                        $isUnlocked = true;
                    }
                } elseif ($b->rule_type === 'assessment_started_n') {
                    $startedCount = AssessmentAttempt::where('user_id', $user->id)
                        ->whereHas('assessment', function ($q) use ($course) {
                            $q->where('course_id', $course->id);
                        })
                        ->where('status', 'in_progress')
                        ->count();

                    // If attempts are already submitted, they may not be in progress.
                    $progress = $b->target_value > 0 ? min(100, (int) round(($startedCount / $b->target_value) * 100)) : 0;
                    if ($startedCount >= $b->target_value && ! $isUnlocked) {
                        $this->unlock($user->id, $b->id);
                        $isUnlocked = true;
                    }
                } elseif ($b->rule_type === 'assessment_submitted_n') {
                    $submittedCount = AssessmentAttempt::where('user_id', $user->id)
                        ->whereHas('assessment', function ($q) use ($course) {
                            $q->where('course_id', $course->id);
                        })
                        ->where('status', 'graded')
                        ->count();

                    $progress = $b->target_value > 0 ? min(100, (int) round(($submittedCount / $b->target_value) * 100)) : 0;
                    if ($submittedCount >= $b->target_value && ! $isUnlocked) {
                        $this->unlock($user->id, $b->id);
                        $isUnlocked = true;
                    }
                } elseif ($b->rule_type === 'membership_granted') {
                    // Membership is tied to actually holding the membership certificate.
                    $hasMembership = Certificate::where('user_id', $user->id)
                        ->where('course_id', $course->id)
                        ->exists();
                    $progress = $hasMembership ? 100 : 0;
                    if ($hasMembership && ! $isUnlocked) {
                        $this->unlock($user->id, $b->id);
                        $isUnlocked = true;
                    }
                } elseif ($b->rule_type === 'certificate_issued') {
                    $hasCertificate = Certificate::where('user_id', $user->id)
                        ->where('course_id', $course->id)
                        ->exists();
                    $progress = $hasCertificate ? 100 : 0;
                    if ($hasCertificate && ! $isUnlocked) {
                        $this->unlock($user->id, $b->id);
                        $isUnlocked = true;
                    }
                } elseif ($b->rule_type === 'perfect_attempt') {
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
                }
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

