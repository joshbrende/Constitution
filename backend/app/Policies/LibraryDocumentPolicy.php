<?php

namespace App\Policies;

use App\Models\LibraryDocument;
use App\Models\User;
use App\Services\MembershipStandingService;
use Illuminate\Auth\Access\Response;

class LibraryDocumentPolicy
{
    private const DENY_RESTRICTED = 'This document is restricted. Sign in or request access.';

    /**
     * Published documents only: callers should 404 first when a document is not published.
     */
    public function view(?User $user, LibraryDocument $libraryDocument): bool|Response
    {
        $membership = app(MembershipStandingService::class);

        return match ($libraryDocument->access_rule) {
            'public' => true,
            'member' => $user !== null && $membership->hasMemberPrivileges($user)
                ? true
                : Response::deny(self::DENY_RESTRICTED),
            'leadership' => $user !== null && ($user->hasRole('presidium') || $user->hasRole('system_admin') || $user->isCadreDesignee())
                ? true
                : Response::deny(self::DENY_RESTRICTED),
            default => Response::deny(self::DENY_RESTRICTED),
        };
    }
}
