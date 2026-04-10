<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilitatorLearnersViewTest extends TestCase
{
    use RefreshDatabase;

    private function createFacilitatorUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'facilitator', 'guard_name' => 'web'], ['guard_name' => 'web']);

        $user = User::create([
            'name' => 'Facilitator',
            'email' => 'facilitator@example.com',
            'password' => 'password',
        ]);

        $user->roles()->sync([$role->id]);

        return $user;
    }

    public function test_non_facilitator_cannot_view_learners(): void
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => 'password',
        ]);

        $course = Course::create([
            'title' => 'Test course',
            'slug' => 'test-course',
            'status' => 'published',
            'instructor_id' => $student->id,
            'enrollment_count' => 0,
        ]);

        $response = $this->actingAs($student)->get(route('instructor.course-learners', $course));
        $response->assertStatus(403);
    }

    public function test_facilitator_can_view_learners_and_at_risk_flag(): void
    {
        $facilitator = $this->createFacilitatorUser();

        $course = Course::create([
            'title' => 'Test course',
            'slug' => 'test-course',
            'status' => 'published',
            'instructor_id' => $facilitator->id,
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
            'progress_status' => 'in_progress',
            'progress_percentage' => 40,
            'enrolled_at' => now()->subMonth(),
        ]);

        CourseProgress::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
            'units_completed' => 2,
            'total_units' => 10,
            'quizzes_completed' => 0,
            'total_quizzes' => 3,
            'assignments_completed' => 0,
            'total_assignments' => 0,
            'overall_progress' => 40,
            'last_activity_at' => now()->subWeeks(3),
        ]);

        $response = $this->actingAs($facilitator)->get(route('instructor.course-learners', $course));
        $response->assertStatus(200);
        $response->assertSee('Learners', false);
        $response->assertSee('At risk', false);

        $response = $this->actingAs($facilitator)->get(route('instructor.course-learners', [$course, 'filter' => 'at-risk']));
        $response->assertStatus(200);
        $response->assertSee('At risk', false);
    }
}

