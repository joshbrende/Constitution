<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentAnswer;
use App\Models\Option;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcademyAssessmentController extends Controller
{
    private const CACHE_TTL_MINUTES = 10;

    public function __construct(
        protected MembershipService $membershipService,
        protected AuditLogger $auditLogger
    ) {}

    /**
     * Show assessment with questions and options (for taking).
     * Anti-cheat:
     * 1) Random subset + order randomisation
     * 2) Subset is bound to an in-progress attempt via `question_set_token` + persisted `question_ids`
     */
    public function assessment(Request $request, Assessment $assessment): JsonResponse
    {
        $gate = $this->gateNationalId($request);
        if ($gate !== null) {
            return $gate;
        }

        $user = $request->user();
        $this->authorize('take', $assessment);

        $assessment->load(['questions.options' => fn ($q) => $q->orderBy('id')]);

        $inProgress = AssessmentAttempt::where('assessment_id', $assessment->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        $storedQuestionIds = is_array($inProgress?->question_ids) ? $inProgress?->question_ids : null;
        if ($inProgress && is_array($storedQuestionIds) && count($storedQuestionIds) > 0) {
            return $this->jsonAssessmentFromStoredSubset($assessment, $storedQuestionIds);
        }

        return $this->jsonAssessmentWithNewQuestionSet($request->user(), $assessment);
    }

    /**
     * Start an assessment attempt.
     */
    public function startAttempt(Request $request, Assessment $assessment): JsonResponse
    {
        $gate = $this->gateNationalIdAndProvince($request);
        if ($gate !== null) {
            return $gate;
        }

        $user = $request->user();
        $this->authorize('take', $assessment);

        $inProgress = AssessmentAttempt::where('assessment_id', $assessment->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($inProgress) {
            return response()->json(['data' => $inProgress]);
        }

        $questionSetToken = $request->input('question_set_token');
        if (! is_string($questionSetToken) || $questionSetToken === '') {
            return response()->json([
                'message' => 'Missing question set token. Please reload the assessment screen.',
                'code' => 'QUESTION_SET_TOKEN_REQUIRED',
            ], 422);
        }

        $cacheKey = "academy.assessment_question_set.{$user->id}.{$assessment->id}.{$questionSetToken}";
        $cached = Cache::get($cacheKey);
        $questionIds = $this->normalizedQuestionIdsFromCache($cached);
        if ($questionIds === null) {
            return response()->json([
                'message' => 'This question set has expired or is invalid. Please reload the assessment screen.',
                'code' => 'QUESTION_SET_TOKEN_INVALID',
            ], 422);
        }

        Cache::forget($cacheKey);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => $questionIds,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->auditLogger->log(
            action: 'academy.attempt_started',
            targetType: AssessmentAttempt::class,
            targetId: $attempt->id,
            metadata: [
                'assessment_id' => $assessment->id,
                'course_id' => $assessment->course_id,
                'user_id' => $user->id,
            ],
            request: $request
        );

        return response()->json(['data' => $attempt], 201);
    }

    /**
     * Submit an assessment attempt with answers.
     */
    public function submitAttempt(Request $request, AssessmentAttempt $attempt): JsonResponse
    {
        $user = $request->user();
        if (! $user || $attempt->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->authorize('submit', $attempt);

        $assessment = $attempt->assessment()->with('questions')->first();
        if (! $assessment) {
            return response()->json(['message' => 'Assessment not found.'], 404);
        }

        $data = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'distinct', 'exists:questions,id'],
            'answers.*.option_id' => ['nullable', 'exists:options,id'],
        ]);

        $expectedQuestionIds = $this->resolveExpectedQuestionIds($attempt, $assessment);
        if ($expectedQuestionIds instanceof JsonResponse) {
            return $expectedQuestionIds;
        }

        $expectedCount = count($expectedQuestionIds);
        $setMismatch = $this->validateAnswerSetMatchesExpected($data['answers'], $expectedQuestionIds, $expectedCount);
        if ($setMismatch !== null) {
            return $setMismatch;
        }

        $prepared = $this->buildPreparedAnswers($data['answers'], $attempt, $expectedQuestionIds);
        if ($prepared instanceof JsonResponse) {
            return $prepared;
        }

        ['rows' => $preparedAnswers, 'correct' => $correct] = $prepared;
        $score = (int) round(($correct / $expectedCount) * 100);

        DB::transaction(function () use ($attempt, $preparedAnswers, $score): void {
            AssessmentAnswer::where('assessment_attempt_id', $attempt->id)->delete();
            foreach ($preparedAnswers as $row) {
                AssessmentAnswer::create($row);
            }

            $attempt->update([
                'score' => $score,
                'status' => 'graded',
                'submitted_at' => now(),
            ]);

            $this->membershipService->grantMembershipIfPassed($attempt);
        });

        $this->auditLogger->log(
            action: 'academy.attempt_submitted',
            targetType: AssessmentAttempt::class,
            targetId: $attempt->id,
            metadata: [
                'assessment_id' => $assessment->id,
                'course_id' => $assessment->course_id,
                'user_id' => $user->id,
                'score' => $score,
                'passed' => $score >= ($assessment->pass_mark ?? 70),
            ],
            request: $request
        );

        return response()->json([
            'data' => $attempt->fresh(),
            'score' => $score,
            'passed' => $score >= ($assessment->pass_mark ?? 70),
        ]);
    }

    private function gateNationalId(Request $request): ?JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! $user->national_id) {
            return response()->json([
                'message' => 'Zimbabwe ID number is required before you can start assessments. Please update your profile.',
                'code' => 'NATIONAL_ID_REQUIRED',
            ], 422);
        }

        return null;
    }

    private function gateNationalIdAndProvince(Request $request): ?JsonResponse
    {
        $gate = $this->gateNationalId($request);
        if ($gate !== null) {
            return $gate;
        }

        $user = $request->user();
        if (! $user->province_id) {
            return response()->json([
                'message' => 'Your province is required before you can take assessments. Please set your province in Profile.',
                'code' => 'PROVINCE_REQUIRED',
            ], 422);
        }

        return null;
    }

    /**
     * @param  array<int, mixed>  $storedQuestionIds
     */
    private function jsonAssessmentFromStoredSubset(Assessment $assessment, array $storedQuestionIds): JsonResponse
    {
        $storedQuestionIds = collect($storedQuestionIds)->map(fn ($v) => (int) $v)->values()->all();
        $questionsById = $assessment->questions->keyBy('id');
        $subsetQuestions = collect($storedQuestionIds)
            ->map(fn ($qid) => $questionsById->get($qid))
            ->filter()
            ->values();

        foreach ($subsetQuestions as $question) {
            $question->setRelation('options', $question->options->shuffle()->values());
        }

        $assessment->setRelation('questions', $subsetQuestions);
        $assessment->setAttribute('question_set_token', null);

        return response()->json(['data' => $assessment]);
    }

    private function jsonAssessmentWithNewQuestionSet(User $user, Assessment $assessment): JsonResponse
    {
        $questions = $assessment->questions->shuffle();
        $perAttempt = $assessment->questions_per_attempt;
        if ($perAttempt !== null && (int) $perAttempt > 0 && $questions->count() > (int) $perAttempt) {
            $questions = $questions->take((int) $perAttempt);
        }

        $questions = $questions->values();
        $questionIds = $questions->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

        $token = Str::random(48);
        $cacheKey = "academy.assessment_question_set.{$user->id}.{$assessment->id}.{$token}";
        Cache::put($cacheKey, ['question_ids' => $questionIds], now()->addMinutes(self::CACHE_TTL_MINUTES));

        $assessment->setRelation('questions', $questions);
        foreach ($assessment->questions as $question) {
            $question->setRelation('options', $question->options->shuffle()->values());
        }
        $assessment->setAttribute('question_set_token', $token);

        return response()->json(['data' => $assessment]);
    }

    /**
     * @return list<int>|null
     */
    private function normalizedQuestionIdsFromCache(mixed $cached): ?array
    {
        if (! is_array($cached)) {
            return null;
        }
        $ids = $cached['question_ids'] ?? null;
        if (! is_array($ids) || count($ids) === 0) {
            return null;
        }

        return collect($ids)->map(fn ($v) => (int) $v)->values()->all();
    }

    /**
     * @return list<int>|JsonResponse
     */
    private function resolveExpectedQuestionIds(AssessmentAttempt $attempt, Assessment $assessment): array|JsonResponse
    {
        $expectedQuestionIds = is_array($attempt->question_ids) ? $attempt->question_ids : null;
        if (! is_array($expectedQuestionIds) || count($expectedQuestionIds) === 0) {
            $assessmentQuestionIds = $assessment->questions()->pluck('id')->all();
            if (count($assessmentQuestionIds) === 0) {
                return response()->json(['message' => 'Assessment has no questions.'], 422);
            }
            $expectedQuestionIds = $assessmentQuestionIds;
            $perAttempt = $assessment->questions_per_attempt;
            if ($perAttempt !== null && (int) $perAttempt > 0) {
                $expectedQuestionIds = array_slice($expectedQuestionIds, 0, min(count($assessmentQuestionIds), (int) $perAttempt));
            }
        }

        $expectedQuestionIds = collect($expectedQuestionIds)->map(fn ($v) => (int) $v)->values()->all();
        if (count($expectedQuestionIds) === 0) {
            return response()->json(['message' => 'Assessment has no questions.'], 422);
        }

        return $expectedQuestionIds;
    }

    /**
     * @param  array<int, array{question_id: mixed, option_id?: mixed}>  $answers
     * @param  list<int>  $expectedQuestionIds
     */
    private function validateAnswerSetMatchesExpected(array $answers, array $expectedQuestionIds, int $expectedCount): ?JsonResponse
    {
        $submitted = collect($answers);
        $submittedQuestionIds = $submitted->pluck('question_id')->map(fn ($v) => (int) $v);

        if ($submittedQuestionIds->count() !== $expectedCount) {
            return response()->json([
                'message' => "You must answer exactly {$expectedCount} question(s).",
            ], 422);
        }

        if ($submittedQuestionIds->unique()->count() !== $expectedCount) {
            return response()->json(['message' => 'Duplicate questions are not allowed.'], 422);
        }

        $expectedSet = collect($expectedQuestionIds)->unique()->values()->all();
        $missing = collect($expectedSet)->diff($submittedQuestionIds->unique()->values()->all());
        if ($missing->count() > 0) {
            return response()->json(['message' => 'Submitted answers do not match the question set.'], 422);
        }

        $extra = $submittedQuestionIds->unique()->values()->diff($expectedSet);
        if ($extra->count() > 0) {
            return response()->json(['message' => 'Submitted answers do not match the question set.'], 422);
        }

        return null;
    }

    /**
     * @param  array<int, array{question_id: mixed, option_id?: mixed}>  $answers
     * @param  list<int>  $expectedQuestionIds
     * @return array{rows: list<array<string, mixed>>, correct: int}|JsonResponse
     */
    private function buildPreparedAnswers(array $answers, AssessmentAttempt $attempt, array $expectedQuestionIds): array|JsonResponse
    {
        $expectedSet = collect($expectedQuestionIds)->unique()->values()->all();
        $questionIdSet = array_fill_keys($expectedSet, true);
        $submitted = collect($answers);
        $optionIds = $submitted->pluck('option_id')
            ->filter(fn ($v) => $v !== null)
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        $optionsById = $optionIds ? Option::whereIn('id', $optionIds)->get()->keyBy('id') : collect();

        $preparedAnswers = [];
        $correct = 0;
        foreach ($answers as $a) {
            $questionId = (int) $a['question_id'];
            if (! isset($questionIdSet[$questionId])) {
                return response()->json(['message' => 'Invalid question for this assessment.'], 422);
            }

            $optionId = $a['option_id'] !== null ? (int) $a['option_id'] : null;
            if ($optionId === null) {
                $preparedAnswers[] = [
                    'assessment_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'option_id' => null,
                    'is_correct' => false,
                ];
                continue;
            }

            $option = $optionsById->get($optionId);
            if (! $option || (int) $option->question_id !== $questionId) {
                return response()->json(['message' => 'Invalid option for question.'], 422);
            }

            $isCorrect = (bool) $option->is_correct;
            if ($isCorrect) {
                $correct++;
            }

            $preparedAnswers[] = [
                'assessment_attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'option_id' => $optionId,
                'is_correct' => $isCorrect,
            ];
        }

        return ['rows' => $preparedAnswers, 'correct' => $correct];
    }
}
