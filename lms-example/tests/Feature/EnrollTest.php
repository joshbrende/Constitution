<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_enroll_when_guest_redirects_to_login(): void
    {
        $instructor = User::create(['name' => 'Instructor', 'email' => 'instr@example.com', 'password' => 'password']);
        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'status' => 'published',
            'enrollment_count' => 0,
            'instructor_id' => $instructor->id,
        ]);
        $response = $this->post(route('courses.enroll', $course));
        $response->assertRedirect(route('login'));
    }

    public function test_enroll_when_authenticated_creates_enrollment(): void
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web'], ['guard_name' => 'web']);
        $instructor = User::create(['name' => 'Instructor', 'email' => 'instr@example.com', 'password' => 'password']);
        $user = User::create(['name' => 'Test', 'email' => 'e@example.com', 'password' => 'password']);
        $user->roles()->sync([Role::where('name', 'student')->first()->id]);

        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'status' => 'published',
            'enrollment_count' => 0,
            'instructor_id' => $instructor->id,
        ]);

        $response = $this->actingAs($user)->post(route('courses.enroll', $course));
        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }
}
