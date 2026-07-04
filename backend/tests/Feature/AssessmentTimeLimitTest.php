<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Option;
use App\Models\Question;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentTimeLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_rejected_after_duration_expires(): void
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
            'code' => 'TIME-TEST',
            'title' => 'Timed Course',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => false,
        ]);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Timed Assessment',
            'duration_minutes' => 45,
            'pass_mark' => 70,
            'status' => 'published',
        ]);

        $question = Question::create([
            'assessment_id' => $assessment->id,
            'body' => 'Q1',
            'order' => 1,
            'marks' => 1,
            'difficulty' => 'easy',
        ]);
        $correct = Option::create(['question_id' => $question->id, 'body' => 'Yes', 'is_correct' => true]);
        Option::create(['question_id' => $question->id, 'body' => 'No', 'is_correct' => false]);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [$question->id],
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(46),
        ]);

        $this->sanctumAs($user);

        $response = $this->postJson("/api/v1/academy/attempts/{$attempt->id}/submit", [
            'answers' => [
                ['question_id' => $question->id, 'option_id' => $correct->id],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('code', 'ASSESSMENT_TIME_EXPIRED');
    }

    public function test_start_attempt_includes_deadline_metadata(): void
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
            'code' => 'META-TEST',
            'title' => 'Meta Course',
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
            'title' => 'Meta Assessment',
            'duration_minutes' => 30,
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

        $assessmentResponse = $this->getJson("/api/v1/academy/assessments/{$assessment->id}");
        $assessmentResponse->assertOk();
        $token = $assessmentResponse->json('data.question_set_token');

        $startResponse = $this->postJson("/api/v1/academy/assessments/{$assessment->id}/attempts", [
            'question_set_token' => $token,
        ]);

        $startResponse->assertCreated()
            ->assertJsonPath('data.duration_minutes', 30)
            ->assertJsonStructure(['data' => ['deadline_at', 'seconds_remaining']]);
    }
}
