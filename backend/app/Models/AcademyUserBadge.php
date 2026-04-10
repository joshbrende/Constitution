<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademyUserBadge extends Model
{
    protected $fillable = [
        'user_id',
        'academy_badge_id',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
    ];

    public function academyBadge(): BelongsTo
    {
        return $this->belongsTo(AcademyBadge::class, 'academy_badge_id');
    }
}

