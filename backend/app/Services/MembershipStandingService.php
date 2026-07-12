<?php

namespace App\Services;

use App\Enums\MembershipStanding;
use App\Models\User;

class MembershipStandingService
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected MembershipNumberService $membershipNumbers,
        protected WingMembershipService $wingMemberships,
    ) {}

    public function standing(User $user): MembershipStanding
    {
        $standing = $user->membership_standing;

        if ($standing instanceof MembershipStanding) {
            return $standing;
        }

        $raw = (string) ($standing ?? MembershipStanding::Applicant->value);

        return MembershipStanding::tryFrom($raw) ?? MembershipStanding::Applicant;
    }

    /** Full party member — certificate issued (Members register). */
    public function isFullMember(User $user): bool
    {
        return $this->standing($user) === MembershipStanding::Member;
    }

    /** May access member-only library, dialogue, and league courses (after payment confirm). */
    public function hasMemberPrivileges(User $user): bool
    {
        if ($this->standing($user) === MembershipStanding::Suspended) {
            return false;
        }

        return $user->hasRole('member');
    }

    /** Completed membership pathway for course prerequisites. */
    public function hasCompletedMembershipPathway(User $user): bool
    {
        if ($this->standing($user) === MembershipStanding::Suspended) {
            return false;
        }

        if ($this->isFullMember($user) || $user->hasRole('member')) {
            return true;
        }

        if ($user->certificates()->exists()) {
            return true;
        }

        return $user->enrolments()
            ->whereHas('course', fn ($q) => $q->where('grants_membership', true))
            ->whereNotNull('completed_at')
            ->exists();
    }

    public function markApplicant(User $user, ?string $source = null): void
    {
        $this->transition($user, MembershipStanding::Applicant, $source ?? 'registration');
    }

    public function markProvisional(User $user, ?string $source = null): void
    {
        if ($this->standing($user) === MembershipStanding::Member) {
            return;
        }

        $this->transition($user, MembershipStanding::Provisional, $source ?? 'exam_passed');
    }

    public function markFullMember(User $user, ?string $source = null): void
    {
        $this->transition($user, MembershipStanding::Member, $source ?? 'certificate_issued');
        $fresh = $user->fresh();
        $this->membershipNumbers->ensureForFullMember($fresh);
        $this->wingMemberships->ensureForFullMember($fresh);
    }

    public function markSuspended(User $user, User $admin, ?string $reason = null): void
    {
        $before = $this->standing($user)->value;
        $user->forceFill(['membership_standing' => MembershipStanding::Suspended->value])->save();

        $this->auditLogger->log(
            action: 'membership.suspended',
            targetType: User::class,
            targetId: $user->id,
            metadata: [
                'before' => $before,
                'after' => MembershipStanding::Suspended->value,
                'admin_user_id' => $admin->id,
                'reason' => $reason,
            ]
        );
    }

    public function reinstate(User $user, User $admin, MembershipStanding $to = MembershipStanding::Applicant): void
    {
        if ($to === MembershipStanding::Suspended) {
            return;
        }

        $before = $this->standing($user)->value;
        $resolved = $this->resolveStandingFromRecords($user, $to);
        $user->forceFill(['membership_standing' => $resolved->value])->save();

        if ($resolved === MembershipStanding::Member) {
            $fresh = $user->fresh();
            $this->membershipNumbers->ensureForFullMember($fresh);
            $this->wingMemberships->ensureForFullMember($fresh);
        }

        $this->auditLogger->log(
            action: 'membership.reinstated',
            targetType: User::class,
            targetId: $user->id,
            metadata: [
                'before' => $before,
                'after' => $resolved->value,
                'admin_user_id' => $admin->id,
            ]
        );
    }

    public function syncFromRecords(User $user): void
    {
        $resolved = $this->resolveStandingFromRecords($user);
        if ($this->standing($user) === $resolved) {
            if ($resolved === MembershipStanding::Member) {
                $this->membershipNumbers->ensureForFullMember($user);
                $this->wingMemberships->ensureForFullMember($user);
            }

            return;
        }

        $user->forceFill(['membership_standing' => $resolved->value])->save();

        if ($resolved === MembershipStanding::Member) {
            $fresh = $user->fresh();
            $this->membershipNumbers->ensureForFullMember($fresh);
            $this->wingMemberships->ensureForFullMember($fresh);
        }
    }

    private function resolveStandingFromRecords(User $user, ?MembershipStanding $fallback = null): MembershipStanding
    {
        if ($user->certificates()->exists()) {
            return MembershipStanding::Member;
        }

        if ($user->certificateApplications()->exists() || $user->hasRole('member')) {
            return MembershipStanding::Provisional;
        }

        return $fallback ?? MembershipStanding::Applicant;
    }

    private function transition(User $user, MembershipStanding $to, string $source): void
    {
        $from = $this->standing($user);
        if ($from === $to) {
            return;
        }

        // Do not downgrade full members to provisional/applicant via automated flows.
        if ($from === MembershipStanding::Member && $to !== MembershipStanding::Suspended) {
            return;
        }

        $user->forceFill(['membership_standing' => $to->value])->save();

        $this->auditLogger->log(
            action: 'membership.standing_changed',
            targetType: User::class,
            targetId: $user->id,
            metadata: [
                'from' => $from->value,
                'to' => $to->value,
                'source' => $source,
            ]
        );
    }
}
