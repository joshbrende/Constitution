<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DialogueReport extends Model
{
    protected $fillable = [
        'reporter_user_id',
        'reported_user_id',
        'dialogue_thread_id',
        'dialogue_message_id',
        'reason',
        'details',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'resolution_action',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(DialogueThread::class, 'dialogue_thread_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(DialogueMessage::class, 'dialogue_message_id');
    }
}

