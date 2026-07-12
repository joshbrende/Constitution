<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAccessTest extends TestCase
{
    use RefreshDatabase;

    private function membershipCourse(): Course
    {
        return Course::updateOrCreate(
            ['code' => 'MEMBERSHIP'],
            [
                'title' => 'Membership Course',
                'level' => 'basic',
                'status' => 'published',
                'grants_membership' => true,
                'requires_membership' => false,
                'audience' => 'all',
                'issues_certificate' => true,
                'certificate_number_prefix' => 'ZPF-MEM',
                'certificate_fee_amount' => 25,
                'certificate_fee_currency' => 'USD',
            ]
        );
    }

    private function youthCourse(): Course
    {
        return Course::updateOrCreate(
            ['code' => 'YOUTH-101'],
            [
                'title' => 'Youth Leadership',
                'level' => 'basic',
                'status' => 'published',
                'requires_membership' => true,
                'audience' => 'youth',
            ]
        );
    }

    public function test_youth_course_enrol_blocked_without_membership(): void
    {
        $this->membershipCourse();
        $youthCourse = $this->youthCourse();

        $user = User::factory()->create([
            'national_id' => '12-123456-A-12',
            'wing' => 'youth',
        ]);

        $this->sanctumAs($user);

        $this->postJson("/api/v1/academy/courses/{$youthCourse->id}/enrol")
            ->assertForbidden()
            ->assertJson([
                'code' => 'MEMBERSHIP_REQUIRED',
            ]);
    }

    private function attachMemberRole(User $user): void
    {
        $user->roles()->attach(
            Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member'])->id
        );
    }

    public function test_youth_course_enrol_allowed_after_membership_completion(): void
    {
        $membership = $this->membershipCourse();
        $youthCourse = $this->youthCourse();

        $user = User::factory()->create([
            'national_id' => '12-123456-A-12',
            'wing' => 'youth',
            'branch_admitted_at' => now(),
        ]);
        $this->attachMemberRole($user);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $membership->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->sanctumAs($user);

        $this->postJson("/api/v1/academy/courses/{$youthCourse->id}/enrol")
            ->assertCreated();
    }

    public function test_youth_course_allowed_for_member_without_matching_wing(): void
    {
        $membership = $this->membershipCourse();
        $youthCourse = $this->youthCourse();

        $user = User::factory()->create([
            'national_id' => '12-123456-A-12',
            'wing' => 'main',
            'membership_standing' => 'member',
            'branch_admitted_at' => now(),
        ]);
        $this->attachMemberRole($user);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $membership->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        app(\App\Services\WingMembershipService::class)->ensureForFullMember($user);

        $this->sanctumAs($user);

        $this->postJson("/api/v1/academy/courses/{$youthCourse->id}/enrol")
            ->assertCreated();
    }

    public function test_youth_course_blocked_without_member_privileges(): void
    {
        $membership = $this->membershipCourse();
        $youthCourse = $this->youthCourse();

        $user = User::factory()->create([
            'national_id' => '12-123456-A-12',
            'wing' => 'main',
            'branch_admitted_at' => now(),
        ]);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $membership->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->sanctumAs($user);

        $this->postJson("/api/v1/academy/courses/{$youthCourse->id}/enrol")
            ->assertForbidden()
            ->assertJson([
                'code' => 'AUDIENCE_RESTRICTED',
            ]);
    }

    public function test_course_list_includes_access_metadata(): void
    {
        $this->membershipCourse();
        $youthCourse = $this->youthCourse();

        $user = User::factory()->create(['wing' => 'main']);
        $this->sanctumAs($user);

        $response = $this->getJson('/api/v1/academy/courses');
        $response->assertOk();

        $youth = collect($response->json('data'))->firstWhere('id', $youthCourse->id);
        $this->assertFalse($youth['access']['allowed']);
        $this->assertSame('MEMBERSHIP_REQUIRED', $youth['access']['code']);
    }

    public function test_presidium_audience_requires_presidium_role(): void
    {
        $this->membershipCourse();

        $course = Course::updateOrCreate(
            ['code' => 'PRES-101'],
            [
                'title' => 'Presidium Briefing',
                'level' => 'advanced',
                'status' => 'published',
                'requires_membership' => false,
                'audience' => 'presidium',
            ]
        );

        $user = User::factory()->create([
            'national_id' => '12-123456-A-12',
            'wing' => 'main',
        ]);
        $user->roles()->attach(Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']));

        $this->sanctumAs($user);

        $this->postJson("/api/v1/academy/courses/{$course->id}/enrol")
            ->assertForbidden()
            ->assertJson(['code' => 'AUDIENCE_RESTRICTED']);
    }

    public function test_youth_course_blocked_without_branch_admission(): void
    {
        $membership = $this->membershipCourse();
        $youthCourse = $this->youthCourse();

        $user = User::factory()->create([
            'national_id' => '12-123456-A-12',
            'wing' => 'youth',
        ]);
        $this->attachMemberRole($user);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $membership->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->sanctumAs($user);

        $this->postJson("/api/v1/academy/courses/{$youthCourse->id}/enrol")
            ->assertForbidden()
            ->assertJson([
                'code' => 'BRANCH_ADMISSION_REQUIRED',
            ]);
    }

    public function test_league_course_skips_branch_gate_when_config_disabled(): void
    {
        config(['academy.require_branch_admission_for_league_courses' => false]);

        $membership = $this->membershipCourse();
        $youthCourse = $this->youthCourse();

        $user = User::factory()->create([
            'national_id' => '12-123456-A-12',
            'wing' => 'youth',
        ]);
        $this->attachMemberRole($user);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $membership->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->sanctumAs($user);

        $this->postJson("/api/v1/academy/courses/{$youthCourse->id}/enrol")
            ->assertCreated();
    }

    public function test_certificate_uses_course_prefix(): void
    {
        $course = Course::create([
            'code' => 'YOUTH-LEAD',
            'title' => 'Youth Lead',
            'level' => 'basic',
            'status' => 'published',
            'certificate_number_prefix' => 'ZPF-YOUTH',
        ]);

        $number = \App\Models\Certificate::nextCertificateNumber($course);

        $this->assertStringStartsWith('ZPF-YOUTH-', $number);
    }
}
