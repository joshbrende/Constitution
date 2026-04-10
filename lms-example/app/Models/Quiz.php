<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $table = 'quizzes';

    protected $fillable = [
        'course_id', 'title', 'slug', 'description', 'instructions',
        'duration', 'pass_percentage', 'max_attempts', 'randomize_questions',
        'show_results', 'show_correct_answers', 'total_points',
        'grading_type', 'assessment_type',
    ];

    protected $casts = [
        'pass_percentage' => 'integer',
        'max_attempts' => 'integer',
        'randomize_questions' => 'boolean',
        'show_results' => 'boolean',
        'show_correct_answers' => 'boolean',
        'total_points' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'quiz_id')->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
}
