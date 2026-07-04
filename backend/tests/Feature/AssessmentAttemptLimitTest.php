<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Option;
use App\Models\Province;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentAttemptLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'academy.assessment_max_attempts' => 2,
            'academy.assessment_attempt_cooldown_hours' => 24,
        ]);
    }

    private function makeContext(): array
    {
        $province = Province::query()->firstOrCreate(
            ['code' => 'harare'],
            ['name' => 'Harare']
        );
        $user = User::factory()->create([
            'national_id' => '12-ABC123',
            'province_id' => $province->id,
        ]);

        $course = Course::create([
            'code' => 'LIMIT-TEST',
            'title' => 'Limit Course',
            'level' => 'basic',
            'status' => 'published',
        ]);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Limit Assessment',
            'pass_mark' => 70,
            'status' => 'published',
        ]);

        $question = Question::create([
            'assessment_id' => $assessment->id,
            'body' => 'Q1',
            'order' => 1,
            'marks' => 1,
        ]);
        Option::create(['question_id' => $question->id, 'body' => 'A', 'is_correct' => true]);

        $this->sanctumAs($user);

        return compact('user', 'assessment', 'question');
    }

    public function test_blocks_start_when_max_attempts_reached(): void
    {
        ['user' => $user, 'assessment' => $assessment, 'question' => $question] = $this->makeContext();

        foreach ([40, 50] as $score) {
            AssessmentAttempt::create([
                'assessment_id' => $assessment->id,
                'user_id' => $user->id,
                'question_ids' => [$question->id],
                'score' => $score,
                'status' => 'graded',
                'started_at' => now()->subHour(),
                'submitted_at' => now()->subMinutes(30),
            ]);
        }

        $response = $this->getJson("/api/v1/academy/assessments/{$assessment->id}/attempt-eligibility");

        $response->assertOk()
            ->assertJsonPath('data.can_start', false)
            ->assertJsonPath('data.code', 'ASSESSMENT_MAX_ATTEMPTS_REACHED');
    }

    public function test_blocks_start_during_cooldown_after_failed_attempt(): void
    {
        ['user' => $user, 'assessment' => $assessment, 'question' => $question] = $this->makeContext();

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [$question->id],
            'score' => 40,
            'status' => 'graded',
            'started_at' => now()->subHours(2),
            'submitted_at' => now()->subHour(),
        ]);

        $response = $this->postJson("/api/v1/academy/assessments/{$assessment->id}/attempts", [
            'question_set_token' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('code', 'ASSESSMENT_COOLDOWN_ACTIVE');
    }

    public function test_blocks_new_attempt_after_passing(): void
    {
        ['user' => $user, 'assessment' => $assessment, 'question' => $question] = $this->makeContext();

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [$question->id],
            'score' => 85,
            'status' => 'graded',
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
        ]);

        $response = $this->getJson("/api/v1/academy/assessments/{$assessment->id}");

        $response->assertStatus(422)
            ->assertJsonPath('code', 'ASSESSMENT_ALREADY_PASSED');
    }
}
