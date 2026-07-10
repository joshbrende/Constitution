<?php

namespace App\Policies;

use App\Models\DialogueChannel;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DialogueChannelPolicy
{
    public function view(User $user, DialogueChannel $dialogueChannel): bool
    {
        return $dialogueChannel->canUserAccess($user);
    }

    /**
     * Only dialogue editors (admin web) may open new topics. Members comment on existing threads.
     */
    public function createThread(User $user, DialogueChannel $dialogueChannel): bool
    {
        if (! $dialogueChannel->canUserAccess($user)) {
            return false;
        }

        return Gate::forUser($user)->allows('admin.section', 'dialogue');
    }
}
