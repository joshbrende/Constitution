<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'title', 'slug', 'content', 'description',
        'order', 'unit_type', 'video_url', 'audio_url', 'document_url',
        'duration', 'is_free', 'is_draft', 'prerequisite_unit_id', 'quiz_id', 'assignment_id',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_draft' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function prerequisiteUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'prerequisite_unit_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function unitCompletions(): HasMany
    {
        return $this->hasMany(UnitCompletion::class, 'unit_id');
    }

    public function isLesson(): bool
    {
        return in_array($this->unit_type, ['text', 'video', 'audio', 'document'], true);
    }

    public function isQuiz(): bool
    {
        return $this->unit_type === 'quiz';
    }

    public function isAssignment(): bool
    {
        return $this->unit_type === 'assignment';
    }
}
