<?php

namespace Tests\Unit;

use App\Models\Question;
use App\Services\AssessmentQuestionSelectorService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AssessmentQuestionSelectorServiceTest extends TestCase
{
    public function test_selects_module_balanced_subset_with_difficulty_mix(): void
    {
        $questions = collect();
        $id = 0;
        foreach (range(1, 10) as $moduleId) {
            foreach (['easy', 'medium', 'hard'] as $difficulty) {
                foreach (range(1, 2) as $i) {
                    $id++;
                    $question = new Question;
                    $question->forceFill([
                        'id' => $id,
                        'module_id' => $moduleId,
                        'difficulty' => $difficulty,
                    ]);
                    $questions->push($question);
                }
            }
        }

        $selected = app(AssessmentQuestionSelectorService::class)->select($questions, 25);

        $this->assertCount(25, $selected);
        $this->assertSame(25, $selected->pluck('id')->unique()->count());

        $moduleCounts = $selected->groupBy('module_id')->map->count();
        $this->assertGreaterThanOrEqual(2, $moduleCounts->min());

        $difficultyCounts = $selected->countBy('difficulty');
        $this->assertGreaterThan(0, $difficultyCounts->get('easy', 0));
        $this->assertGreaterThan(0, $difficultyCounts->get('medium', 0));
        $this->assertGreaterThan(0, $difficultyCounts->get('hard', 0));
    }

    public function test_returns_all_questions_when_pool_smaller_than_per_attempt(): void
    {
        $questions = Collection::make([
            new Question(['id' => 1, 'module_id' => 1, 'difficulty' => 'easy']),
            new Question(['id' => 2, 'module_id' => 1, 'difficulty' => 'medium']),
        ]);

        $selected = app(AssessmentQuestionSelectorService::class)->select($questions, 25);

        $this->assertCount(2, $selected);
    }
}
