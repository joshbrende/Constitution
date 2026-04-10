<?php

namespace App\Policies;

use App\Models\DialogueThread;
use App\Models\User;

class DialogueThreadPolicy
{
    /**
     * Post a new message in the thread (thread must be open; channel must allow posting).
     */
    public function reply(User $user, DialogueThread $dialogueThread): bool
    {
        $dialogueThread->loadMissing('channel');

        if ($dialogueThread->status !== 'open') {
            return false;
        }

        $channel = $dialogueThread->channel;

        return $channel !== null && $channel->canUserPost($user);
    }
}
