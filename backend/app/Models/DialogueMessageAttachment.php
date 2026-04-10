<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogueMessageAttachment extends Model
{
    protected $fillable = [
        'dialogue_message_id',
        'type',
        'disk',
        'path',
        'original_name',
        'mime',
        'size_bytes',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function message()
    {
        return $this->belongsTo(DialogueMessage::class, 'dialogue_message_id');
    }
}

