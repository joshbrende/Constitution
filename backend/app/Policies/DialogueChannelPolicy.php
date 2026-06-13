<?php

namespace App\Policies;

use App\Models\DialogueChannel;
use App\Models\User;

class DialogueChannelPolicy
{
    public function view(User $user, DialogueChannel $dialogueChannel): bool
    {
        return $dialogueChannel->canUserAccess($user);
    }

    public function createThread(User $user, DialogueChannel $dialogueChannel): bool
    {
        return $dialogueChannel->canUserPost($user);
    }
}
