<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Note;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotesTest extends TestCase
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

    public function test_guest_cannot_save_notes(): void
    {
        $instructor = User::create([
            'name' => 'Instructor',
            'email' => 'instr@example.com',
            'password' => 'password',
        ]);

        $course = Course::create([
            'title' => 'Test course',
            'slug' => 'test-course',
            'status' => 'published',
            'instructor_id' => $instructor->id,
            'enrollment_count' => 0,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'slug' => 'lesson-1',
            'unit_type' => 'text',
            'order' => 1,
        ]);

        $response = $this->post(route('learn.notes.store', [$course, $unit]), [
            'body' => 'My note',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('notes', 0);
    }

    public function test_student_can_create_and_clear_note_for_unit(): void
    {
        $student = $this->createStudentUser();

        $instructor = User::create([
            'name' => 'Instructor',
            'email' => 'instr@example.com',
            'password' => 'password',
        ]);

        $course = Course::create([
            'title' => 'Test course',
            'slug' => 'test-course',
            'status' => 'published',
            'instructor_id' => $instructor->id,
            'enrollment_count' => 0,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'slug' => 'lesson-1',
            'unit_type' => 'text',
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

        // Create note
        $response = $this->actingAs($student)->post(route('learn.notes.store', [$course, $unit]), [
            'body' => 'My first note',
        ]);

        $response->assertRedirect(route('learn.show', ['course' => $course, 'unit' => $unit->id]));
        $this->assertDatabaseHas('notes', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'unit_id' => $unit->id,
            'body' => 'My first note',
        ]);

        // Clear note (empty body deletes)
        $response = $this->actingAs($student)->post(route('learn.notes.store', [$course, $unit]), [
            'body' => '',
        ]);

        $response->assertRedirect(route('learn.show', ['course' => $course, 'unit' => $unit->id]));
        $this->assertDatabaseMissing('notes', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'unit_id' => $unit->id,
        ]);
    }
}

