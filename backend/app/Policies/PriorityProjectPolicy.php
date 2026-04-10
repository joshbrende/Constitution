<?php

namespace App\Policies;

use App\Models\PriorityProject;
use App\Models\User;

class PriorityProjectPolicy
{
    public function like(User $user, PriorityProject $priorityProject): bool
    {
        return $priorityProject->is_published
            && $priorityProject->published_at !== null
            && $priorityProject->published_at->isPast();
    }
}
