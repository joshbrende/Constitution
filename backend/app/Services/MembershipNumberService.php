<?php

namespace App\Services;

use App\Enums\MembershipSource;
use App\Enums\MembershipStanding;
use App\Models\User;

class MembershipNumberService
{
    private const PREFIX = 'ZPF-';

    /** Alphabet without ambiguous 0/O/1/I/L */
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public function __construct(
        protected AuditLogger $auditLogger,
    ) {}

    /**
     * Ensure a full member has a stable opaque membership number.
     * Idempotent — never regenerates an existing number.
     */
    public function ensureForFullMember(User $user): void
    {
        $user->refresh();

        if ($this->standingValue($user) !== MembershipStanding::Member->value) {
            return;
        }

        if (filled($user->membership_number)) {
            return;
        }

        $source = $this->resolveSource($user);
        $admittedAt = $user->membership_admitted_at
            ?? $user->certificates()->orderBy('issued_at')->value('issued_at')
            ?? $user->updated_at
            ?? now();

        $number = $this->generateUnique();

        $user->forceFill([
            'membership_number' => $number,
            'membership_admitted_at' => $admittedAt,
            'membership_source' => $source->value,
        ])->save();

        $this->auditLogger->log(
            action: 'membership.number_assigned',
            targetType: User::class,
            targetId: $user->id,
            metadata: [
                'membership_number' => $number,
                'membership_source' => $source->value,
            ],
            actorUserId: $user->id,
        );
    }

    public function generateUnique(): string
    {
        do {
            $number = self::PREFIX.$this->randomSuffix(6);
        } while (User::query()->where('membership_number', $number)->exists());

        return $number;
    }

    private function randomSuffix(int $length): string
    {
        $alphabet = self::ALPHABET;
        $max = strlen($alphabet) - 1;
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $alphabet[random_int(0, $max)];
        }

        return $out;
    }

    private function resolveSource(User $user): MembershipSource
    {
        if (filled($user->membership_source)) {
            return MembershipSource::tryFrom((string) $user->membership_source)
                ?? MembershipSource::Academy;
        }

        $admission = $user->certificateApplications()
            ->orderByDesc('id')
            ->value('admission_source');

        return MembershipSource::tryFrom((string) $admission)
            ?? MembershipSource::Academy;
    }

    private function standingValue(User $user): string
    {
        $standing = $user->membership_standing;

        if ($standing instanceof MembershipStanding) {
            return $standing->value;
        }

        return (string) ($standing ?? MembershipStanding::Applicant->value);
    }
}
