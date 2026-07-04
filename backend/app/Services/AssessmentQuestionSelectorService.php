<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Builds a balanced question subset for an assessment attempt:
 * module coverage, difficulty mix, and randomisation within constraints.
 */
class AssessmentQuestionSelectorService
{
    /**
     * @param  Collection<int, \App\Models\Question>  $questions
     * @return Collection<int, \App\Models\Question>
     */
    public function select(Collection $questions, ?int $perAttempt): Collection
    {
        $questions = $questions->values();
        if ($perAttempt === null || $perAttempt <= 0 || $questions->count() <= $perAttempt) {
            return $questions->shuffle()->values();
        }

        $config = config('academy.assessment_selection', []);
        $targets = $this->normalizedDifficultyTargets((int) $perAttempt, $config);
        $minPerModule = max(1, (int) ($config['min_per_module'] ?? 2));
        $ensureModules = (bool) ($config['ensure_module_coverage'] ?? true);

        $selected = collect();
        $counts = ['easy' => 0, 'medium' => 0, 'hard' => 0];

        if ($ensureModules) {
            foreach ($questions->groupBy('module_id') as $moduleQuestions) {
                $moduleQuestions = $moduleQuestions->values();
                $slots = min($minPerModule, $moduleQuestions->count(), (int) $perAttempt - $selected->count());
                for ($i = 0; $i < $slots; $i++) {
                    $picked = $this->pickOne($moduleQuestions, $selected, $targets, $counts);
                    if ($picked === null) {
                        break;
                    }
                    $selected->push($picked);
                    $counts[$this->difficultyOf($picked)]++;
                }
            }
        }

        while ($selected->count() < $perAttempt) {
            $picked = $this->pickOne($questions, $selected, $targets, $counts);
            if ($picked === null) {
                break;
            }
            $selected->push($picked);
            $counts[$this->difficultyOf($picked)]++;
        }

        return $selected->shuffle()->values();
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{easy: int, medium: int, hard: int}
     */
    public function normalizedDifficultyTargets(int $perAttempt, array $config): array
    {
        $ratios = $config['difficulty_ratios'] ?? [
            'easy' => 0.32,
            'medium' => 0.48,
            'hard' => 0.20,
        ];

        $easy = (int) round($perAttempt * (float) ($ratios['easy'] ?? 0.32));
        $medium = (int) round($perAttempt * (float) ($ratios['medium'] ?? 0.48));
        $hard = max(0, $perAttempt - $easy - $medium);

        return ['easy' => $easy, 'medium' => $medium, 'hard' => $hard];
    }

    /**
     * @param  Collection<int, \App\Models\Question>  $pool
     * @param  Collection<int, \App\Models\Question>  $selected
     * @param  array{easy: int, medium: int, hard: int}  $targets
     * @param  array{easy: int, medium: int, hard: int}  $counts
     */
    private function pickOne(Collection $pool, Collection $selected, array $targets, array $counts): ?object
    {
        $selectedIds = $selected->pluck('id')->map(fn ($id) => (int) $id)->all();
        $available = $pool->filter(fn ($q) => ! in_array((int) $q->id, $selectedIds, true))->values();
        if ($available->isEmpty()) {
            return null;
        }

        $deficits = [];
        foreach (['hard', 'medium', 'easy'] as $tier) {
            $deficits[$tier] = ($targets[$tier] ?? 0) - ($counts[$tier] ?? 0);
        }

        foreach (['hard', 'medium', 'easy'] as $tier) {
            if ($deficits[$tier] <= 0) {
                continue;
            }
            $tierPool = $available->filter(fn ($q) => $this->difficultyOf($q) === $tier)->values();
            if ($tierPool->isNotEmpty()) {
                return $tierPool->random();
            }
        }

        return $available->random();
    }

    private function difficultyOf(object $question): string
    {
        $difficulty = $question->difficulty ?? $question->getAttribute('difficulty') ?? 'medium';

        return in_array($difficulty, ['easy', 'medium', 'hard'], true) ? $difficulty : 'medium';
    }
}
