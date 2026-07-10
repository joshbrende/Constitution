<?php

use App\Models\DialogueThread;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return (int) $user->id === $id;
});

Broadcast::channel('dialogue.thread.{threadId}', function (User $user, int $threadId) {
    $thread = DialogueThread::query()->find($threadId);

    return $thread !== null && $user->can('view', $thread);
});
