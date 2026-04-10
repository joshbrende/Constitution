<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogueThreadRead extends Model
{
    protected $fillable = [
        'dialogue_thread_id',
        'user_id',
        'last_read_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'last_read_at' => 'datetime',
    ];
}

