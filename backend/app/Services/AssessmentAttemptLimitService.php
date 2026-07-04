<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AssessmentAttemptLimitService
{
    /**
     * @return array{
     *     can_start: bool,
     *     attempts_used: int,
     *     max_attempts: int|null,
     *     attempts_remaining: int|null,
     *     cooldown_hours: int,
     *     cooldown_ends_at: string|null,
     *     code?: string,
     *     message?: string
     * }
     */
    public function eligibility(User $user, Assessment $assessment): array
    {
        $maxAttempts = $this->maxAttempts();
        $cooldownHours = $this->cooldownHours();
        $passMark = (int) ($assessment->pass_mark ?? 70);

        $gradedAttempts = AssessmentAttempt::query()
            ->where('assessment_id', $assessment->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['graded', 'submitted'])
            ->orderByDesc('submitted_at')
            ->get();

        $attemptsUsed = $gradedAttempts->count();
        $passedAttempt = $gradedAttempts->first(
            fn (AssessmentAttempt $attempt) => $attempt->score !== null && (int) $attempt->score >= $passMark
        );

        $base = [
            'can_start' => true,
            'attempts_used' => $attemptsUsed,
            'max_attempts' => $maxAttempts > 0 ? $maxAttempts : null,
            'attempts_remaining' => $maxAttempts > 0 ? max(0, $maxAttempts - $attemptsUsed) : null,
            'cooldown_hours' => $cooldownHours,
            'cooldown_ends_at' => null,
        ];

        if ($passedAttempt) {
            return array_merge($base, [
                'can_start' => false,
                'code' => 'ASSESSMENT_ALREADY_PASSED',
                'message' => 'You have already passed this assessment.',
            ]);
        }

        if ($maxAttempts > 0 && $attemptsUsed >= $maxAttempts) {
            return array_merge($base, [
                'can_start' => false,
                'attempts_remaining' => 0,
                'code' => 'ASSESSMENT_MAX_ATTEMPTS_REACHED',
                'message' => sprintf(
                    'You have used all %d attempt(s) for this assessment. Contact your administrator if you need assistance.',
                    $maxAttempts
                ),
            ]);
        }

        $lastAttempt = $gradedAttempts->first();
        if ($lastAttempt?->submitted_at && $cooldownHours > 0) {
            $cooldownEnds = $lastAttempt->submitted_at->copy()->addHours($cooldownHours);
            if (now()->lt($cooldownEnds)) {
                return array_merge($base, [
                    'can_start' => false,
                    'cooldown_ends_at' => $cooldownEnds->toIso8601String(),
                    'code' => 'ASSESSMENT_COOLDOWN_ACTIVE',
                    'message' => sprintf(
                        'Please wait until %s before attempting this assessment again.',
                        $cooldownEnds->timezone(config('app.timezone'))->format('d M Y H:i')
                    ),
                ]);
            }
        }

        return $base;
    }

    public function assertCanStart(User $user, Assessment $assessment): ?JsonResponse
    {
        $eligibility = $this->eligibility($user, $assessment);
        if ($eligibility['can_start']) {
            return null;
        }

        return response()->json([
            'message' => $eligibility['message'] ?? 'You cannot start a new attempt at this time.',
            'code' => $eligibility['code'] ?? 'ASSESSMENT_START_BLOCKED',
            'details' => array_filter([
                'attempts_used' => $eligibility['attempts_used'],
                'max_attempts' => $eligibility['max_attempts'],
                'attempts_remaining' => $eligibility['attempts_remaining'],
                'cooldown_ends_at' => $eligibility['cooldown_ends_at'],
            ], fn ($value) => $value !== null),
        ], 422);
    }

    public function maxAttempts(): int
    {
        return max(0, (int) config('academy.assessment_max_attempts', 3));
    }

    public function cooldownHours(): int
    {
        return max(0, (int) config('academy.assessment_attempt_cooldown_hours', 24));
    }
}
