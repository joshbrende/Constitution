<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AcademyAssessmentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_option_mapping_rejects_submit_without_partial_answer_writes(): void
    {
        $user = User::factory()->create([
            'surname' => 'Learner',
            'national_id' => '12-ABC123',
        ]);
        Sanctum::actingAs($user);

        $course = Course::create([
            'code' => 'MEM-201',
            'title' => 'Assessment Integrity',
            'description' => 'Test',
            'level' => 'basic',
            'status' => 'published',
            'is_mandatory' => false,
            'grants_membership' => false,
        ]);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Final Assessment',
            'pass_mark' => 70,
            'status' => 'published',
        ]);

        $q1 = Question::create([
            'assessment_id' => $assessment->id,
            'body' => 'Q1',
            'order' => 1,
            'marks' => 1,
        ]);
        $q2 = Question::create([
            'assessment_id' => $assessment->id,
            'body' => 'Q2',
            'order' => 2,
            'marks' => 1,
        ]);

        Option::create(['question_id' => $q1->id, 'body' => 'A1', 'is_correct' => true]);
        $q2Option = Option::create(['question_id' => $q2->id, 'body' => 'B1', 'is_correct' => true]);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [$q1->id, $q2->id],
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Intentionally map q1 -> option that belongs to q2.
        $response = $this->postJson("/api/v1/academy/attempts/{$attempt->id}/submit", [
            'answers' => [
                ['question_id' => $q1->id, 'option_id' => $q2Option->id],
                ['question_id' => $q2->id, 'option_id' => $q2Option->id],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Invalid option for question.']);

        $this->assertDatabaseCount('assessment_answers', 0);
        $attempt->refresh();
        $this->assertSame('in_progress', $attempt->status);
        $this->assertNull($attempt->score);
    }

    public function test_unique_attempt_question_constraint_prevents_duplicate_answers(): void
    {
        $user = User::factory()->create(['surname' => 'Learner']);
        $course = Course::create([
            'code' => 'MEM-202',
            'title' => 'Unique Constraint',
            'description' => 'Test',
            'level' => 'basic',
            'status' => 'published',
            'is_mandatory' => false,
            'grants_membership' => false,
        ]);
        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'A1',
            'pass_mark' => 70,
            'status' => 'published',
        ]);
        $question = Question::create([
            'assessment_id' => $assessment->id,
            'body' => 'Q',
            'order' => 1,
            'marks' => 1,
        ]);
        $option = Option::create([
            'question_id' => $question->id,
            'body' => 'O',
            'is_correct' => true,
        ]);
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [$question->id],
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        AssessmentAnswer::create([
            'assessment_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'option_id' => $option->id,
            'is_correct' => true,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        AssessmentAnswer::create([
            'assessment_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'option_id' => $option->id,
            'is_correct' => true,
        ]);
    }
}

