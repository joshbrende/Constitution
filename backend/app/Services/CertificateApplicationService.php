<?php

namespace App\Services;

use App\Enums\CertificateApplicationStatus;
use App\Jobs\GenerateCertificatePdfJob;
use App\Jobs\SendAcademyApplicationMailJob;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use App\Notifications\Academy\AcademyApplicationNotification;
use App\Notifications\Academy\CertificateCollectedNotification;
use App\Notifications\Academy\CertificatePresidiumApprovedNotification;
use App\Notifications\Academy\CertificateReadyForCollectionNotification;
use App\Notifications\Academy\ExamPassedPaymentRequiredNotification;
use App\Notifications\Academy\PaymentConfirmedNotification;
use App\Services\ExpoPushNotificationService;
use InvalidArgumentException;

class CertificateApplicationService
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected ReceiptNumberService $receiptNumbers,
        protected MembershipStandingService $membershipStanding,
    ) {}

    /**
     * Create or return existing application when a learner passes a membership assessment.
     */
    public function createFromPassedAttempt(AssessmentAttempt $attempt): CertificateApplication
    {
        $attempt->loadMissing(['assessment.course', 'user.province']);

        $course = $attempt->assessment->course;
        $user = $attempt->user;

        if (! $course instanceof Course || ! $course->issuesCertificate()) {
            throw new InvalidArgumentException('Certificate applications require a course that issues certificates.');
        }

        $passMark = $attempt->assessment->pass_mark ?? 70;
        if ($attempt->score === null || $attempt->score < $passMark) {
            throw new InvalidArgumentException('Attempt did not meet the pass mark.');
        }

        $existing = CertificateApplication::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $feeAmount = $course->certificate_fee_amount;
        if ($feeAmount === null || (float) $feeAmount <= 0) {
            throw new InvalidArgumentException('Course certificate fee is not configured.');
        }

        $numbers = $this->receiptNumbers->generateForUser($user);

        $application = CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'assessment_attempt_id' => $attempt->id,
            'receipt_number' => $numbers['receipt_number'],
            'payment_reference_code' => $numbers['payment_reference_code'],
            'fee_amount' => $feeAmount,
            'fee_currency' => $course->certificate_fee_currency ?: config('academy.default_fee_currency', 'USD'),
            'status' => CertificateApplicationStatus::PaymentPending,
            'exam_passed_at' => now(),
        ]);

        $this->auditLogger->log(
            action: 'academy.application.created',
            targetType: CertificateApplication::class,
            targetId: $application->id,
            metadata: [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'assessment_attempt_id' => $attempt->id,
                'receipt_number' => $application->receipt_number,
                'fee_amount' => (string) $application->fee_amount,
                'fee_currency' => $application->fee_currency,
            ]
        );

        $application->load('course');
        $this->membershipStanding->markProvisional($user, 'exam_passed');
        $this->notifyStudent($user, new ExamPassedPaymentRequiredNotification($application));

        return $application;
    }

    public function confirmPayment(CertificateApplication $application, User $admin, ?string $paymentReferenceNote = null): CertificateApplication
    {
        $this->assertStatus($application, CertificateApplicationStatus::PaymentPending);

        $application->update([
            'status' => CertificateApplicationStatus::PresidiumPending,
            'payment_confirmed_at' => now(),
            'payment_confirmed_by' => $admin->id,
            'payment_reference_note' => $paymentReferenceNote,
        ]);

        $application->loadMissing('user');
        $this->attachMemberRoleIfNeeded($application->user, $application);

        $this->auditLogger->log(
            action: 'academy.application.payment_confirmed',
            targetType: CertificateApplication::class,
            targetId: $application->id,
            metadata: [
                'admin_user_id' => $admin->id,
                'payment_reference_note' => $paymentReferenceNote,
                'receipt_number' => $application->receipt_number,
            ]
        );

        $this->notifyStudent($application->user, new PaymentConfirmedNotification($application));

        return $application->fresh();
    }

    public function presidiumApprove(CertificateApplication $application, User $admin, ?string $note = null): CertificateApplication
    {
        $this->assertStatus($application, CertificateApplicationStatus::PresidiumPending);

        if ($application->certificate_id) {
            throw new InvalidArgumentException('Certificate already linked to this application.');
        }

        $application->loadMissing(['user', 'course']);

        $certificate = Certificate::create([
            'user_id' => $application->user_id,
            'course_id' => $application->course_id,
            'certificate_number' => Certificate::nextCertificateNumber($application->course),
            'issued_at' => now(),
            'expires_at' => $this->defaultCertificateExpiry(),
            'pdf_status' => 'pending',
        ]);

        $application->update([
            'status' => CertificateApplicationStatus::PrintReady,
            'presidium_approved_at' => now(),
            'presidium_approved_by' => $admin->id,
            'presidium_note' => $note,
            'certificate_id' => $certificate->id,
        ]);

        GenerateCertificatePdfJob::dispatch($certificate);

        $this->membershipStanding->markFullMember($application->user, 'certificate_issued');

        $this->auditLogger->log(
            action: 'academy.application.presidium_approved',
            targetType: CertificateApplication::class,
            targetId: $application->id,
            metadata: [
                'admin_user_id' => $admin->id,
                'certificate_id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'presidium_note' => $note,
            ]
        );

        $this->notifyStudent($application->user, new CertificatePresidiumApprovedNotification($application));

        return $application->fresh(['certificate', 'user', 'course']);
    }

    public function markPrinted(CertificateApplication $application, User $admin): CertificateApplication
    {
        $this->assertStatus($application, CertificateApplicationStatus::PrintReady);

        $application->update([
            'status' => CertificateApplicationStatus::Printed,
            'printed_at' => now(),
            'printed_by' => $admin->id,
        ]);

        $this->auditLogger->log(
            action: 'academy.application.printed',
            targetType: CertificateApplication::class,
            targetId: $application->id,
            metadata: ['admin_user_id' => $admin->id]
        );

        return $application->fresh();
    }

    public function markReadyForCollection(CertificateApplication $application, User $admin, ?string $collectionOffice = null): CertificateApplication
    {
        $this->assertStatus($application, CertificateApplicationStatus::Printed);

        $application->update([
            'status' => CertificateApplicationStatus::ReadyForCollection,
            'ready_for_collection_at' => now(),
            'collection_office' => $collectionOffice,
        ]);

        $this->auditLogger->log(
            action: 'academy.application.ready_for_collection',
            targetType: CertificateApplication::class,
            targetId: $application->id,
            metadata: [
                'admin_user_id' => $admin->id,
                'collection_office' => $collectionOffice,
            ]
        );

        $application->loadMissing('user');
        $this->notifyStudent($application->user, new CertificateReadyForCollectionNotification($application));

        return $application->fresh();
    }

    public function markCollected(CertificateApplication $application, User $admin): CertificateApplication
    {
        $this->assertStatus($application, CertificateApplicationStatus::ReadyForCollection);

        $application->update([
            'status' => CertificateApplicationStatus::Collected,
            'collected_at' => now(),
            'collected_by' => $admin->id,
        ]);

        $this->auditLogger->log(
            action: 'academy.application.collected',
            targetType: CertificateApplication::class,
            targetId: $application->id,
            metadata: ['admin_user_id' => $admin->id]
        );

        $application->loadMissing('user');
        $this->notifyStudent($application->user, new CertificateCollectedNotification($application));

        return $application->fresh();
    }

    /**
     * @return list<string>
     */
    public static function statusesForTab(string $tab): array
    {
        return match ($tab) {
            'payment_pending' => [CertificateApplicationStatus::PaymentPending->value],
            'presidium' => [CertificateApplicationStatus::PresidiumPending->value],
            'print' => [CertificateApplicationStatus::PrintReady->value],
            'collection' => [
                CertificateApplicationStatus::Printed->value,
                CertificateApplicationStatus::ReadyForCollection->value,
            ],
            'completed' => [CertificateApplicationStatus::Collected->value],
            default => [],
        };
    }

    private function assertStatus(CertificateApplication $application, CertificateApplicationStatus $expected): void
    {
        if ($application->status !== $expected) {
            throw new InvalidArgumentException(
                'Application cannot transition from '.$application->status->label().' at this step.'
            );
        }
    }

    private function defaultCertificateExpiry(): ?\Illuminate\Support\Carbon
    {
        $days = (int) config('certificates.default_expiry_days', 730);
        if ($days <= 0) {
            return null;
        }

        return now()->addDays($days);
    }

    private function notifyStudent(User $user, AcademyApplicationNotification $notification): void
    {
        $notification->application->loadMissing('course');
        $user->notifyNow($notification, ['database']);

        app(ExpoPushNotificationService::class)->sendToUser(
            $user,
            $notification->title(),
            $notification->body(),
            [
                'type' => $notification->notificationType(),
                'application_id' => $notification->application->id,
            ]
        );

        SendAcademyApplicationMailJob::dispatch(
            $user->id,
            $notification->application->id,
            $notification::class,
        );
    }

    private function attachMemberRoleIfNeeded(User $user, CertificateApplication $application): void
    {
        $memberRole = Role::firstOrCreate(
            ['slug' => 'member'],
            ['name' => 'Member', 'description' => 'Ordinary party member or app user.']
        );

        $wasMember = $user->hasRole('member');
        if (! $wasMember) {
            $user->roles()->attach($memberRole->id);
        }

        $this->auditLogger->log(
            action: 'membership.granted',
            targetType: CertificateApplication::class,
            targetId: $application->id,
            metadata: [
                'user_id' => $user->id,
                'course_id' => $application->course_id,
                'member_role_attached' => ! $wasMember,
                'source' => 'payment_confirmed',
                'receipt_number' => $application->receipt_number,
            ]
        );
    }
}
