<?php

namespace App\Observers;

use App\Events\DialogueMessageChanged;
use App\Models\DialogueMessage;

class DialogueMessageObserver
{
    public function created(DialogueMessage $message): void
    {
        $this->broadcast($message);
    }

    public function updated(DialogueMessage $message): void
    {
        if ($message->wasChanged(['body', 'is_deleted', 'is_pinned'])) {
            $this->broadcast($message);
        }
    }

    private function broadcast(DialogueMessage $message): void
    {
        if (config('broadcasting.default') === 'null') {
            return;
        }

        DialogueMessageChanged::dispatch($message);
    }
}
