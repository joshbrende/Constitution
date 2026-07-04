<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AssessmentTimeLimitService
{
    public function graceSeconds(): int
    {
        return max(0, (int) config('academy.assessment_time_grace_seconds', 30));
    }

    public function deadlineFor(AssessmentAttempt $attempt, Assessment $assessment): ?Carbon
    {
        $minutes = (int) ($assessment->duration_minutes ?? 0);
        if ($minutes <= 0 || $attempt->started_at === null) {
            return null;
        }

        return $attempt->started_at->copy()->addMinutes($minutes);
    }

    public function secondsRemaining(AssessmentAttempt $attempt, Assessment $assessment, ?Carbon $at = null): ?int
    {
        $deadline = $this->deadlineFor($attempt, $assessment);
        if ($deadline === null) {
            return null;
        }

        $now = $at ?? now();

        return max(0, (int) $now->diffInSeconds($deadline, false));
    }

    public function hasExpired(AssessmentAttempt $attempt, Assessment $assessment, ?Carbon $at = null): bool
    {
        $deadline = $this->deadlineFor($attempt, $assessment);
        if ($deadline === null) {
            return false;
        }

        $now = $at ?? now();

        return $now->greaterThan($deadline->copy()->addSeconds($this->graceSeconds()));
    }

    public function expiredResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'The time limit for this assessment has expired. Your attempt cannot be submitted.',
            'code' => 'ASSESSMENT_TIME_EXPIRED',
        ], 422);
    }

    /**
     * @return array<string, mixed>
     */
    public function attemptMeta(AssessmentAttempt $attempt, Assessment $assessment): array
    {
        $deadline = $this->deadlineFor($attempt, $assessment);

        return array_filter([
            'deadline_at' => $deadline?->toIso8601String(),
            'seconds_remaining' => $this->secondsRemaining($attempt, $assessment),
            'duration_minutes' => $assessment->duration_minutes,
        ], fn ($value) => $value !== null);
    }

    public function questionSetCacheTtlMinutes(Assessment $assessment): int
    {
        $base = max(10, (int) config('academy.assessment_question_set_cache_minutes', 10));
        $duration = (int) ($assessment->duration_minutes ?? 0);
        $buffer = max(0, (int) config('academy.assessment_question_set_buffer_minutes', 5));

        if ($duration <= 0) {
            return $base;
        }

        return max($base, $duration + $buffer);
    }
}
