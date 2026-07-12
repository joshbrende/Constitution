<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;

class CourseAccessService
{
    /**
     * @return array{allowed: bool, code: string|null, message: string|null}
     */
    public function evaluateAccess(User $user, Course $course): array
    {
        if ($this->hasActiveEnrolment($user, $course)) {
            return $this->allowed();
        }

        if ($course->grants_membership) {
            return $this->allowed();
        }

        if ($course->requires_membership && ! $this->hasCompletedMembership($user)) {
            return $this->denied(
                'MEMBERSHIP_REQUIRED',
                'Complete the membership course before enrolling in this course.'
            );
        }

        if (! $this->userMatchesAudience($user, $course)) {
            return $this->denied(
                'AUDIENCE_RESTRICTED',
                'This course is not available for your league or role.'
            );
        }

        if ($this->requiresBranchAdmission($course) && ! $user->hasBranchAdmission()) {
            return $this->denied(
                'BRANCH_ADMISSION_REQUIRED',
                'Branch admission must be confirmed by your provincial administrator before enrolling in this course.'
            );
        }

        return $this->allowed();
    }

    private function requiresBranchAdmission(Course $course): bool
    {
        if (! config('academy.require_branch_admission_for_league_courses', true)) {
            return false;
        }

        return in_array((string) ($course->audience ?? 'all'), ['youth', 'women', 'veterans'], true);
    }

    public function hasCompletedMembership(User $user): bool
    {
        return app(MembershipStandingService::class)->hasCompletedMembershipPathway($user);
    }

    public function userMatchesAudience(User $user, Course $course): bool
    {
        $audience = (string) ($course->audience ?? 'all');

        if ($audience === 'all') {
            return true;
        }

        if ($audience === 'member') {
            return app(MembershipStandingService::class)->hasMemberPrivileges($user);
        }

        if ($audience === 'presidium') {
            return $user->hasRole('presidium');
        }

        // League pathways: open to members with privileges (wing granted on certificate issue).
        if (in_array($audience, ['youth', 'women', 'veterans'], true)) {
            return app(MembershipStandingService::class)->hasMemberPrivileges($user);
        }

        if ($audience === 'main') {
            $active = app(WingMembershipService::class)->activeWings($user);
            if (in_array('main', $active, true)) {
                return true;
            }

            $wing = strtolower(trim((string) ($user->wing ?? '')));

            return $wing === 'main';
        }

        return false;
    }

    private function hasActiveEnrolment(User $user, Course $course): bool
    {
        return Enrolment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
            ->exists();
    }

    /**
     * @return array{allowed: bool, code: string|null, message: string|null}
     */
    private function allowed(): array
    {
        return ['allowed' => true, 'code' => null, 'message' => null];
    }

    /**
     * @return array{allowed: bool, code: string|null, message: string|null}
     */
    private function denied(string $code, string $message): array
    {
        return ['allowed' => false, 'code' => $code, 'message' => $message];
    }
}
