<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEvaluation;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseEvaluationTest extends TestCase
{
    use RefreshDatabase;

    private function createStudentUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web'], ['guard_name' => 'web']);

        $user = User::create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => 'password',
        ]);

        $user->roles()->sync([$role->id]);

        return $user;
    }

    public function test_student_must_complete_course_before_evaluating(): void
    {
        $student = $this->createStudentUser();

        $course = Course::create([
            'title' => 'Test course',
            'slug' => 'test-course',
            'status' => 'published',
            'instructor_id' => $student->id,
            'enrollment_count' => 0,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_status' => 'in_progress',
            'progress_percentage' => 50,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($student)->post(route('courses.evaluate', $course), [
            'rating' => 5,
        ]);

        $response->assertRedirect(route('courses.show', $course));
        $this->assertDatabaseCount('course_evaluations', 0);
    }

    public function test_student_can_submit_and_update_evaluation_after_completion(): void
    {
        $student = $this->createStudentUser();

        $course = Course::create([
            'title' => 'Test course',
            'slug' => 'test-course',
            'status' => 'published',
            'instructor_id' => $student->id,
            'enrollment_count' => 0,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_status' => 'completed',
            'progress_percentage' => 100,
            'enrolled_at' => now(),
            'completed_at' => now(),
        ]);

        // Submit first evaluation
        $response = $this->actingAs($student)->post(route('courses.evaluate', $course), [
            'rating' => 4,
            'difficulty' => 3,
            'would_recommend' => 1,
            'comments' => 'Good course',
        ]);

        $response->assertRedirect(route('courses.show', $course));
        $this->assertDatabaseHas('course_evaluations', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'rating' => 4,
        ]);

        // Update evaluation
        $response = $this->actingAs($student)->post(route('courses.evaluate', $course), [
            'rating' => 5,
            'difficulty' => 4,
            'would_recommend' => 0,
            'comments' => 'Updated comment',
        ]);

        $response->assertRedirect(route('courses.show', $course));

        $this->assertEquals(1, CourseEvaluation::where('user_id', $student->id)->where('course_id', $course->id)->count());
        $this->assertDatabaseHas('course_evaluations', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'rating' => 5,
            'would_recommend' => false,
        ]);
    }
}

