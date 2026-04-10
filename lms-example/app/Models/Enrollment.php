<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'status', 'progress_status', 'progress_percentage',
        'enrolled_at', 'started_at', 'completed_at', 'expires_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function unitCompletions(): HasMany
    {
        return $this->hasMany(UnitCompletion::class, 'enrollment_id');
    }

    public function facilitatorRating(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FacilitatorRating::class);
    }

    public function getProgressAttribute(): int
    {
        return (int) $this->progress_percentage;
    }
}
