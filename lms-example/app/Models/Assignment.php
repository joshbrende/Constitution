<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Assignment extends Model
{
    use SoftDeletes;

    protected $table = 'assignments';

    protected $fillable = [
        'course_id', 'title', 'slug', 'description', 'instructions',
        'duration', 'due_date', 'max_points', 'allow_file_upload',
        'allowed_file_types', 'max_file_size', 'assessment_type',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_file_upload' => 'boolean',
        'allowed_file_types' => 'array',
        'max_points' => 'integer',
        'max_file_size' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }

    public function unit(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Unit::class, 'assignment_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Assignment $a) {
            if (empty($a->slug)) {
                $a->slug = Str::slug($a->title) . '-' . Str::random(6);
            }
        });
    }
}
