<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\Enrolment;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;

class MembershipService
{
    public function __construct(
        protected AuditLogger $auditLogger
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
     * When a user passes an assessment for a course that grants membership,
     * complete their enrolment and grant the member role.
     */
    public function grantMembershipIfPassed(AssessmentAttempt $attempt): void
    {
        $attempt->load(['assessment.course', 'user']);

        $course = $attempt->assessment->course;
        $user = $attempt->user;

        if (! $course->grants_membership) {
            return;
        }

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

        $memberRole = Role::firstOrCreate(
            ['slug' => 'member'],
            ['name' => 'Member', 'description' => 'Ordinary party member or app user.']
        );

        $wasMember = $user->hasRole('member');
        if (! $wasMember) {
            $user->roles()->attach($memberRole->id);
        }

        // Issue certificate if not already issued (PDF generated async via queue)
        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'certificate_number' => Certificate::nextCertificateNumber(),
                'issued_at' => now(),
                'expires_at' => $this->defaultCertificateExpiry(),
                'pdf_status' => 'pending',
            ]
        );

        // Log "membership granted" once per user+course grant (when we actually issued or confirmed certificate).
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
            ]
        );
    }
}
