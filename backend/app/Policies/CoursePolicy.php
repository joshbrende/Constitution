<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Services\CourseAccessService;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    public function __construct(
        protected CourseAccessService $courseAccess,
    ) {}

    /**
     * Enrol in a published course (national ID and other rules enforced in the controller).
     */
    public function enrol(User $user, Course $course): bool|Response
    {
        if ($course->status !== 'published') {
            return Response::denyAsNotFound('Course not found.');
        }

        $access = $this->courseAccess->evaluateAccess($user, $course);
        if (! $access['allowed']) {
            return Response::deny($access['message']);
        }

        return true;
    }

    /**
     * View course content (modules, lessons, assessments list).
     */
    public function view(User $user, Course $course): bool|Response
    {
        if ($course->status !== 'published') {
            return Response::denyAsNotFound('Course not found.');
        }

        $access = $this->courseAccess->evaluateAccess($user, $course);
        if (! $access['allowed']) {
            return Response::deny($access['message']);
        }

        return true;
    }
}
