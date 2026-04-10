<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Enrol in a published course (national ID and other rules enforced in the controller).
     */
    public function enrol(User $user, Course $course): bool|Response
    {
        if ($course->status !== 'published') {
            return Response::denyAsNotFound('Course not found.');
        }

        return true;
    }
}
