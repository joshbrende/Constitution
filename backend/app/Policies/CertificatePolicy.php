<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CertificatePolicy
{
    public function generate(User $user, Certificate $certificate): bool|Response
    {
        return $this->allowsPdfActions($user, $certificate);
    }

    public function download(User $user, Certificate $certificate): bool|Response
    {
        return $this->allowsPdfActions($user, $certificate);
    }

    private function allowsPdfActions(User $user, Certificate $certificate): bool|Response
    {
        if ($certificate->user_id !== $user->id) {
            return Response::deny('Unauthorized.');
        }

        if ($certificate->isRevoked()) {
            return Response::deny('This certificate has been revoked.');
        }

        return true;
    }
}
