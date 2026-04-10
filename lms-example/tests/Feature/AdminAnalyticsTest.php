<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'], ['guard_name' => 'web']);

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $user->roles()->sync([$role->id]);

        return $user;
    }

    public function test_non_admin_cannot_view_analytics(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->actingAs($user)->get(route('admin.analytics.index'));
        $response->assertStatus(403);
    }

    public function test_admin_sees_course_analytics(): void
    {
        $admin = $this->createAdminUser();

        $instructor = User::create([
            'name' => 'Instructor',
            'email' => 'instr@example.com',
            'password' => 'password',
        ]);

        $course = Course::create([
            'title' => 'Analytics Course',
            'slug' => 'analytics-course',
            'status' => 'published',
            'instructor_id' => $instructor->id,
            'enrollment_count' => 0,
        ]);

        $learner = User::create([
            'name' => 'Learner',
            'email' => 'learner@example.com',
            'password' => 'password',
        ]);

        Enrollment::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_status' => 'completed',
            'progress_percentage' => 100,
            'enrolled_at' => now(),
            'completed_at' => now(),
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Quiz 1',
            'slug' => 'quiz-1',
            'pass_percentage' => 70,
            'max_attempts' => 5,
            'randomize_questions' => false,
            'show_results' => true,
            'show_correct_answers' => true,
            'total_points' => 10,
            'grading_type' => 'auto',
            'assessment_type' => 'summative',
        ]);

        QuizAttempt::create([
            'user_id' => $learner->id,
            'quiz_id' => $quiz->id,
            'course_id' => $course->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'completed_at' => now(),
            'time_taken' => 0,
            'answers' => [],
            'score' => 8,
            'total_points' => 10,
            'percentage' => 80,
            'status' => 'passed',
            'grading_status' => 'graded',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics.index'));
        $response->assertStatus(200);
        $response->assertSee('Analytics Course', false);
    }
}

