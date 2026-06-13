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
        if (! config('academy.student_certificate_download_enabled', false)) {
            return Response::deny(
                'Certificates are issued and collected through the government payment workflow. Use your Academy payment receipt.',
                'CERTIFICATE_WORKFLOW_ADMIN_ONLY'
            );
        }

        if ($certificate->user_id !== $user->id) {
            return Response::deny('Unauthorized.');
        }

        if ($certificate->isRevoked()) {
            return Response::deny('This certificate has been revoked.');
        }

        return true;
    }
}
