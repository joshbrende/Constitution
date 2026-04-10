<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyBadge extends Model
{
    protected $fillable = [
        'course_id',
        'slug',
        'title',
        'description',
        'icon',
        'rule_type',
        'target_value',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function userUnlocks(): HasMany
    {
        return $this->hasMany(AcademyUserBadge::class, 'academy_badge_id');
    }
}

