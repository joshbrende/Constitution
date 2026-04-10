<?php

namespace App\Policies;

use App\Models\AssessmentAttempt;
use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssessmentAttemptPolicy
{
    public function submit(User $user, AssessmentAttempt $attempt): bool|Response
    {
        if ($attempt->status !== 'in_progress') {
            return Response::deny('Attempt already submitted.')->withStatus(422);
        }

        $assessment = $attempt->assessment;
        if (! $assessment) {
            return Response::denyAsNotFound('Assessment not found.');
        }

        if ($assessment->status !== 'published') {
            return Response::denyAsNotFound('Assessment not available.');
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
