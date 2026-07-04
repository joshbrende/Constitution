<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;
use App\Services\CourseAccessService;
use Illuminate\Auth\Access\Response;

class AssessmentPolicy
{
    public function __construct(
        protected CourseAccessService $courseAccess,
    ) {}

    /**
     * View assessment for taking / start attempt prerequisites (published + enrolled).
     */
    public function take(User $user, Assessment $assessment): bool|Response
    {
        if ($assessment->status !== 'published') {
            return Response::denyAsNotFound('Assessment not found.');
        }

        $course = $assessment->course ?? Course::find($assessment->course_id);
        if ($course instanceof Course) {
            $access = $this->courseAccess->evaluateAccess($user, $course);
            if (! $access['allowed']) {
                return Response::deny($access['message']);
            }
        }

        $enrolled = Enrolment::where('user_id', $user->id)
            ->where('course_id', $assessment->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
            ->exists();

        if (! $enrolled) {
            return Response::deny('Please enrol in this course before taking the assessment.');
        }

        return true;
    }
}
