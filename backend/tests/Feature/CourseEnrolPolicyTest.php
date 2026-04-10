<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseEnrolPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrol_returns_404_for_unpublished_course(): void
    {
        $user = User::factory()->create([
            'surname' => 'Learner',
            'national_id' => '12-123456-A-12',
        ]);

        $course = Course::create([
            'code' => 'DRAFT-1',
            'title' => 'Draft course',
            'description' => 'X',
            'level' => 'basic',
            'status' => 'draft',
            'is_mandatory' => false,
            'grants_membership' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/academy/courses/{$course->id}/enrol");

        $response->assertNotFound()
            ->assertJson([
                'error' => 'not_found',
                'message' => 'Course not found.',
            ]);
    }
}
