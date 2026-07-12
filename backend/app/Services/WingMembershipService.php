<?php

namespace App\Services;

use App\Enums\MembershipStanding;
use App\Enums\MembershipWingStatus;
use App\Models\Membership;
use App\Models\User;
use InvalidArgumentException;

class WingMembershipService
{
    /** @var list<string> */
    private const LEAGUE_PRIORITY = ['youth', 'women', 'veterans'];

    public function __construct(
        protected AuditLogger $auditLogger,
    ) {}

    /**
     * Ensure full members have an active main membership; mirror legacy wing if set.
     */
    public function ensureForFullMember(User $user, ?User $assignedBy = null): void
    {
        $user->refresh();

        if (! $this->isFullMember($user)) {
            return;
        }

        $this->ensureActive($user, 'main', $assignedBy);

        $wing = strtolower(trim((string) ($user->wing ?? '')));
        if ($wing !== '' && $this->isAllowedWing($wing)) {
            $this->ensureActive($user, $wing, $assignedBy);
        }

        $this->syncPrimaryWingColumn($user);
    }

    /**
     * Bridge for Admin → Users wing edits (until Step 2.2 multi-select UI).
     */
    public function syncFromLegacyWingField(User $user, ?string $wing, ?User $assignedBy = null): void
    {
        $wing = strtolower(trim((string) ($wing ?? '')));

        if ($wing !== '' && ! $this->isAllowedWing($wing)) {
            throw new InvalidArgumentException("Invalid wing [{$wing}].");
        }

        // Applicants/provisional: keep legacy column only (no memberships rows yet).
        if (! $this->isFullMember($user)) {
            $user->forceFill(['wing' => $wing !== '' ? $wing : null])->save();

            return;
        }

        $this->ensureActive($user, 'main', $assignedBy);

        if ($wing !== '') {
            $this->ensureActive($user, $wing, $assignedBy);
            $user->forceFill(['wing' => $wing])->save();
        }

        $this->syncPrimaryWingColumn($user->fresh());
    }

    /**
     * Sync Youth/Women/Veterans checkboxes from admin UI. Always keeps main for full members.
     *
     * @param  list<string>  $leagueWings  Subset of youth|women|veterans
     */
    public function syncLeagueMemberships(
        User $user,
        array $leagueWings,
        ?string $primaryWing,
        ?User $assignedBy = null
    ): void {
        $allowedLeagues = array_values(array_filter(
            config('academy.user_wings', ['main', 'youth', 'women', 'veterans']),
            fn (string $w) => $w !== 'main'
        ));

        $desired = [];
        foreach ($leagueWings as $wing) {
            $wing = strtolower(trim((string) $wing));
            if (in_array($wing, $allowedLeagues, true)) {
                $desired[] = $wing;
            }
        }
        $desired = array_values(array_unique($desired));

        if ($this->isFullMember($user)) {
            $this->ensureActive($user, 'main', $assignedBy);
        }

        foreach ($allowedLeagues as $league) {
            if (in_array($league, $desired, true)) {
                if ($this->isFullMember($user)) {
                    $this->ensureActive($user, $league, $assignedBy);
                }
            } else {
                $this->end($user, $league, $assignedBy);
            }
        }

        if (! $this->isFullMember($user)) {
            // Prefill primary wing for applicants without membership rows.
            $prefill = $primaryWing && in_array($primaryWing, $desired, true)
                ? $primaryWing
                : ($desired[0] ?? null);
            $user->forceFill(['wing' => $prefill])->save();

            return;
        }

        $primary = strtolower(trim((string) ($primaryWing ?? '')));
        $active = $this->activeWings($user->fresh());

        if ($primary !== '' && in_array($primary, $active, true)) {
            $user->forceFill(['wing' => $primary])->save();

            return;
        }

        $this->syncPrimaryWingColumn($user->fresh());
    }

    public function ensureActive(User $user, string $wing, ?User $assignedBy = null, array $extraMetadata = []): Membership
    {
        if (! $this->isAllowedWing($wing)) {
            throw new InvalidArgumentException("Invalid wing [{$wing}].");
        }

        $membership = Membership::query()->firstOrNew([
            'user_id' => $user->id,
            'wing' => $wing,
        ]);

        $wasActive = $membership->exists && $membership->isActive();

        $membership->fill([
            'status' => MembershipWingStatus::Active,
            'joined_at' => $membership->joined_at ?? now(),
            'ended_at' => null,
            'assigned_by_user_id' => $assignedBy?->id ?? $membership->assigned_by_user_id,
        ]);
        $membership->save();

        if (! $wasActive) {
            $this->auditLogger->log(
                action: 'membership.wing_activated',
                targetType: Membership::class,
                targetId: $membership->id,
                metadata: array_merge([
                    'user_id' => $user->id,
                    'wing' => $wing,
                    'assigned_by_user_id' => $assignedBy?->id,
                ], $extraMetadata),
                actorUserId: $assignedBy?->id,
            );
        }

        return $membership;
    }

    public function end(User $user, string $wing, ?User $assignedBy = null): void
    {
        if ($wing === 'main') {
            throw new InvalidArgumentException('Cannot end main party membership while the user remains a full member.');
        }

        $membership = Membership::query()
            ->where('user_id', $user->id)
            ->where('wing', $wing)
            ->where('status', MembershipWingStatus::Active->value)
            ->first();

        if (! $membership) {
            return;
        }

        $membership->update([
            'status' => MembershipWingStatus::Ended,
            'ended_at' => now(),
            'assigned_by_user_id' => $assignedBy?->id ?? $membership->assigned_by_user_id,
        ]);

        $this->auditLogger->log(
            action: 'membership.wing_ended',
            targetType: Membership::class,
            targetId: $membership->id,
            metadata: [
                'user_id' => $user->id,
                'wing' => $wing,
                'assigned_by_user_id' => $assignedBy?->id,
            ],
            actorUserId: $assignedBy?->id,
        );

        $this->syncPrimaryWingColumn($user->fresh());
    }

    public function syncPrimaryWingColumn(User $user): void
    {
        $active = Membership::query()
            ->where('user_id', $user->id)
            ->where('status', MembershipWingStatus::Active->value)
            ->pluck('wing')
            ->map(fn ($w) => strtolower((string) $w))
            ->all();

        if ($active === []) {
            if ($user->wing !== null) {
                $user->forceFill(['wing' => null])->save();
            }

            return;
        }

        $current = strtolower(trim((string) ($user->wing ?? '')));
        if ($current !== '' && in_array($current, $active, true)) {
            return;
        }

        $primary = 'main';
        foreach (self::LEAGUE_PRIORITY as $league) {
            if (in_array($league, $active, true)) {
                $primary = $league;
                break;
            }
        }
        if (! in_array($primary, $active, true) && in_array('main', $active, true)) {
            $primary = 'main';
        } elseif (! in_array($primary, $active, true)) {
            $primary = $active[0];
        }

        $user->forceFill(['wing' => $primary])->save();
    }

    /**
     * @return list<string>
     */
    public function activeWings(User $user): array
    {
        return Membership::query()
            ->where('user_id', $user->id)
            ->where('status', MembershipWingStatus::Active->value)
            ->orderBy('wing')
            ->pluck('wing')
            ->map(fn ($w) => (string) $w)
            ->values()
            ->all();
    }

    private function isFullMember(User $user): bool
    {
        $standing = $user->membership_standing;

        if ($standing instanceof MembershipStanding) {
            return $standing === MembershipStanding::Member;
        }

        return (string) $standing === MembershipStanding::Member->value;
    }

    private function isAllowedWing(string $wing): bool
    {
        return in_array($wing, config('academy.user_wings', ['main', 'youth', 'women', 'veterans']), true);
    }
}
