<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'question_ids',
        'score',
        'status',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'question_ids' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * API attempt submit routes are scoped to the authenticated owner (404 if not owned).
     */
    public function resolveRouteBinding($value, $field = null): Model
    {
        $query = static::query()->where($field ?? $this->getRouteKeyName(), $value);

        if (request()->is('api/v1/academy/attempts/*') && auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        return $query->firstOrFail();
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
