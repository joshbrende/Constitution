<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEvaluation extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'difficulty',
        'would_recommend',
        'comments',
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}

