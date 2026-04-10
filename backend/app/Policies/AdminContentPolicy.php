<?php

namespace App\Policies;

use App\Models\User;

/**
 * Shared CMS / dashboard authorization (registered as Gate abilities in AppServiceProvider).
 * Section-scoped admin access uses the `admin.section` gate + config/admin.php.
 */
class AdminContentPolicy
{
    /**
     * Direct publish / approve amendment versions (Presidium or system admin).
     */
    public function presidiumPublish(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('presidium') || $user->hasRole('system_admin');
    }

    /**
     * Web UI: library + party organs "manage" link (no approver role).
     */
    public function contentManage(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('content_editor')
            || $user->hasRole('presidium')
            || $user->hasRole('system_admin');
    }
}
