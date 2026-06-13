<?php

namespace App\Policies;

use App\Models\CertificateApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CertificateApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CertificateApplication $application): bool|Response
    {
        if ($application->user_id !== $user->id) {
            return Response::deny('Unauthorized.');
        }

        return true;
    }

    public function downloadReceipt(User $user, CertificateApplication $application): bool|Response
    {
        return $this->view($user, $application);
    }
}
