<?php

namespace App\Policies;

use App\Models\LibraryDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LibraryDocumentPolicy
{
    /**
     * Published documents only: callers should 404 first when a document is not published.
     */
    public function view(?User $user, LibraryDocument $libraryDocument): bool|Response
    {
        return match ($libraryDocument->access_rule) {
            'public' => true,
            'member' => $user !== null
                ? true
                : Response::deny('This document is restricted. Sign in or request access.'),
            'leadership' => $user !== null && ($user->hasRole('presidium') || $user->hasRole('system_admin'))
                ? true
                : Response::deny('This document is restricted. Sign in or request access.'),
            default => Response::deny('This document is restricted. Sign in or request access.'),
        };
    }
}
