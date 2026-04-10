<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriorityProjectLike extends Model
{
    protected $fillable = [
        'priority_project_id',
        'user_id',
    ];
}

