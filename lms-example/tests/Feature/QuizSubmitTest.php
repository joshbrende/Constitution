<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizSubmitTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_quiz(): void
    {
        $instructor = User::create([
            'name' => 'Instructor',
            'email' => 'instructor@example.com',
            'password' => 'password',
        ]);

        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'status' => 'published',
            'access' => 'free',
            'instructor_id' => $instructor->id,
            'enrollment_count' => 0,
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Module 1 Quiz',
            'slug' => 'module-1-quiz',
            'pass_percentage' => 70,
            'randomize_questions' => false,
            'total_points' => 10,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Module 1: Quiz',
            'slug' => 'module-1-quiz-unit',
            'unit_type' => 'quiz',
            'order' => 1,
            'quiz_id' => $quiz->id,
        ]);

        $response = $this->post(route('learn.quiz.submit', [$course, $unit]), [
            'answers' => [],
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_student_can_pass_quiz_and_create_attempt_and_completion(): void
    {
        $role = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web'], ['guard_name' => 'web']);

        $instructor = User::create([
            'name' => 'Instructor',
            'email' => 'instructor@example.com',
            'password' => 'password',
        ]);

        $student = User::create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => 'password',
        ]);
        $student->roles()->sync([$role->id]);

        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'status' => 'published',
            'access' => 'free',
            'instructor_id' => $instructor->id,
            'enrollment_count' => 0,
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Module 1 Quiz',
            'slug' => 'module-1-quiz',
            'pass_percentage' => 70,
            'randomize_questions' => false,
            'total_points' => 10,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Module 1: Quiz',
            'slug' => 'module-1-quiz-unit',
            'unit_type' => 'quiz',
            'order' => 1,
            'quiz_id' => $quiz->id,
        ]);

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'What is 2 + 2?',
            'type' => 'multiple_choice',
            'options' => ['3', '4'],
            'correct_answers' => ['4'],
            'points' => 10,
            'order' => 1,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_status' => 'in_progress',
            'progress_percentage' => 0,
            'enrolled_at' => now(),
        ]);

        $response = $this
            ->actingAs($student)
            ->post(route('learn.quiz.submit', [$course, $unit]), [
                'answers' => [
                    $question->id => '4',
                ],
            ]);

        $response->assertRedirect(route('learn.show', [
            'course' => $course,
            'unit' => $unit->id,
            'quiz_results' => 1,
        ]));

        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'course_id' => $course->id,
            'status' => 'passed',
        ]);

        $this->assertDatabaseHas('unit_completions', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'unit_id' => $unit->id,
        ]);

        $response->assertSessionHas('quiz_result', function (array $result): bool {
            return ($result['passed'] ?? false) === true;
        });
    }
}

