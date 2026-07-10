<?php

namespace App\Events;

use App\Models\DialogueMessage;
use App\Support\DialogueMessagePresenter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DialogueMessageChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DialogueMessage $message) {}

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('dialogue.thread.'.$this->message->dialogue_thread_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.changed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $this->message->loadMissing('user', 'attachments');

        return [
            'message' => DialogueMessagePresenter::toArray($this->message),
        ];
    }
}
