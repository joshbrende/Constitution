<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportQuestion extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'source',
        'ip_address',
        'user_agent',
    ];
}

