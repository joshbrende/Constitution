<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CertificateApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function certificateForUser(User $recipient): Certificate
    {
        $course = Course::create([
            'code' => 'MEM-301',
            'title' => 'Membership',
            'description' => 'Test',
            'level' => 'basic',
            'status' => 'published',
            'is_mandatory' => true,
            'grants_membership' => true,
        ]);

        return Certificate::create([
            'user_id' => $recipient->id,
            'course_id' => $course->id,
            'certificate_number' => 'ZP-MEM-2026-XYZZY123',
            'verification_code' => 'VERIFY01',
            'issued_at' => now(),
            'pdf_status' => 'pending',
        ]);
    }

    public function test_user_cannot_generate_another_users_certificate(): void
    {
        $owner = User::factory()->create(['surname' => 'Owner']);
        $other = User::factory()->create(['surname' => 'Other']);
        $cert = $this->certificateForUser($owner);

        Sanctum::actingAs($other);

        $response = $this->postJson("/api/v1/certificates/{$cert->id}/generate");

        $response->assertForbidden()
            ->assertJsonFragment(['message' => 'Unauthorized.']);
    }

    public function test_owner_can_generate_own_certificate(): void
    {
        $owner = User::factory()->create(['surname' => 'Owner']);
        $cert = $this->certificateForUser($owner);

        Sanctum::actingAs($owner);

        $response = $this->postJson("/api/v1/certificates/{$cert->id}/generate");

        $response->assertStatus(202);
    }
}
