<?php

namespace Tests\Feature;

use App\Enums\CertificateApplicationStatus;
use App\Enums\MembershipStanding;
use App\Enums\MembershipWingStatus;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use App\Services\CertificateApplicationService;
use App\Services\WingMembershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueCourseAutoGrantTest extends TestCase
{
    use RefreshDatabase;

    public function test_presidium_approve_youth_certificate_activates_youth_membership(): void
    {
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Provisional->value,
            'wing' => 'main',
        ]);
        $user->roles()->attach(Role::where('slug', 'member')->first()->id);

        $course = Course::create([
            'code' => 'YOUTH-101',
            'title' => 'Youth Leadership',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => false,
            'requires_membership' => true,
            'audience' => 'youth',
            'issues_certificate' => true,
            'certificate_number_prefix' => 'ZPF-YOUTH',
            'certificate_fee_amount' => 10,
            'certificate_fee_currency' => 'USD',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Youth Final',
            'pass_mark' => 70,
            'status' => 'published',
        ]);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [1],
            'score' => 100,
            'status' => 'graded',
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        $application = CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'assessment_attempt_id' => $attempt->id,
            'receipt_number' => 'ZPF-REC-YOUTH-001',
            'payment_reference_code' => 'YOUTHREF001',
            'fee_amount' => 10,
            'fee_currency' => 'USD',
            'status' => CertificateApplicationStatus::PresidiumPending,
            'exam_passed_at' => now(),
            'payment_confirmed_at' => now(),
        ]);

        $presidium = User::factory()->create(['surname' => 'Presidium']);
        $presidium->roles()->attach(
            Role::firstOrCreate(['slug' => 'presidium'], ['name' => 'Presidium'])->id
        );

        app(CertificateApplicationService::class)->presidiumApprove($application, $presidium, 'OK');

        $user->refresh();
        $this->assertTrue(
            app(\App\Services\MembershipStandingService::class)->isFullMember($user)
        );

        $wings = app(WingMembershipService::class)->activeWings($user);
        $this->assertContains('main', $wings);
        $this->assertContains('youth', $wings);

        $this->assertDatabaseHas('memberships', [
            'user_id' => $user->id,
            'wing' => 'youth',
            'status' => MembershipWingStatus::Active->value,
        ]);
    }

    public function test_presidium_approve_membership_course_does_not_invent_league_wing(): void
    {
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Provisional->value,
            'wing' => null,
        ]);
        $user->roles()->attach(Role::where('slug', 'member')->first()->id);

        $course = Course::create([
            'code' => 'MEMBERSHIP',
            'title' => 'Membership',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'audience' => 'all',
            'issues_certificate' => true,
            'certificate_number_prefix' => 'ZPF-MEM',
            'certificate_fee_amount' => 25,
            'certificate_fee_currency' => 'USD',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Final',
            'pass_mark' => 70,
            'status' => 'published',
        ]);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [1],
            'score' => 100,
            'status' => 'graded',
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        $application = CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'assessment_attempt_id' => $attempt->id,
            'receipt_number' => 'ZPF-REC-MEM-001',
            'payment_reference_code' => 'MEMREF001',
            'fee_amount' => 25,
            'fee_currency' => 'USD',
            'status' => CertificateApplicationStatus::PresidiumPending,
            'exam_passed_at' => now(),
            'payment_confirmed_at' => now(),
        ]);

        $presidium = User::factory()->create(['surname' => 'Presidium']);

        app(CertificateApplicationService::class)->presidiumApprove($application, $presidium, 'OK');

        $wings = app(WingMembershipService::class)->activeWings($user->fresh());
        $this->assertContains('main', $wings);
        $this->assertNotContains('youth', $wings);
        $this->assertNotContains('women', $wings);
        $this->assertNotContains('veterans', $wings);
    }
}
