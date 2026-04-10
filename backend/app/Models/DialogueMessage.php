<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogueMessage extends Model
{
    protected $fillable = [
        'dialogue_thread_id',
        'user_id',
        'body',
        'is_pinned',
        'is_deleted',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function thread()
    {
        return $this->belongsTo(DialogueThread::class, 'dialogue_thread_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(DialogueMessageAttachment::class, 'dialogue_message_id');
    }
}

