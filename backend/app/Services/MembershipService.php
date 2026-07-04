<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Role;
use App\Models\User;
use InvalidArgumentException;

class MembershipService
{
    public function __construct(
        protected AuditLogger $auditLogger,
    ) {}

    private function defaultCertificateExpiry(): ?\Illuminate\Support\Carbon
    {
        $days = (int) config('certificates.default_expiry_days', 730);
        if ($days <= 0) {
            return null;
        }

        return now()->addDays($days);
    }

    /**
     * When a user passes an assessment, complete enrolment and start certificate processing.
     */
    public function grantMembershipIfPassed(AssessmentAttempt $attempt): void
    {
        $attempt->load(['assessment.course', 'user']);

        $course = $attempt->assessment->course;
        $user = $attempt->user;

        $passMark = $attempt->assessment->pass_mark ?? 70;
        if ($attempt->score === null || $attempt->score < $passMark) {
            return;
        }

        $enrolment = Enrolment::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            ['status' => 'enrolled']
        );

        $enrolment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        if (! $course->issuesCertificate()) {
            return;
        }

        if ($course->grants_membership && config('academy.grant_member_role_on') === 'exam_pass') {
            $this->grantLegacyImmediateMembership($user, $course, $attempt);

            return;
        }

        try {
            app(CertificateApplicationService::class)->createFromPassedAttempt($attempt);
        } catch (InvalidArgumentException) {
            return;
        }
    }

    /**
     * Attach member role after payment confirmation (Phase 3 admin workflow).
     */
    public function grantMemberRoleAfterPayment(User $user, Certificate $certificate): void
    {
        $wasMember = $user->hasRole('member');
        if (! $wasMember) {
            $this->attachMemberRole($user);
        }

        $this->auditLogger->log(
            action: 'membership.granted',
            targetType: Certificate::class,
            targetId: $certificate->id,
            metadata: [
                'user_id' => $user->id,
                'course_id' => $certificate->course_id,
                'member_role_attached' => ! $wasMember,
                'source' => 'payment_confirmed',
                'certificate_number' => $certificate->certificate_number,
            ]
        );
    }

    private function grantLegacyImmediateMembership(User $user, Course $course, AssessmentAttempt $attempt): void
    {
        $wasMember = $user->hasRole('member');
        if (! $wasMember) {
            $this->attachMemberRole($user);
        }

        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'certificate_number' => Certificate::nextCertificateNumber($course),
                'issued_at' => now(),
                'expires_at' => $this->defaultCertificateExpiry(),
                'pdf_status' => 'pending',
            ]
        );

        app(MembershipStandingService::class)->markFullMember($user, 'exam_pass_legacy');

        $this->auditLogger->log(
            action: 'membership.granted',
            targetType: Certificate::class,
            targetId: $certificate->id,
            metadata: [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_title' => $course->title,
                'assessment_attempt_id' => $attempt->id,
                'score' => $attempt->score,
                'member_role_attached' => ! $wasMember,
                'certificate_number' => $certificate->certificate_number,
                'source' => 'exam_pass',
            ]
        );
    }

    private function attachMemberRole(User $user): void
    {
        $memberRole = Role::firstOrCreate(
            ['slug' => 'member'],
            ['name' => 'Member', 'description' => 'Ordinary party member or app user.']
        );

        if (! $user->hasRole('member')) {
            $user->roles()->attach($memberRole->id);
        }
    }
}
