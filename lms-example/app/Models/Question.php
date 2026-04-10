<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'quiz_id', 'question', 'type', 'options', 'correct_answers',
        'points', 'order', 'explanation',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'points' => 'integer',
        'order' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * For multiple_choice / true_false: correct_answers is ["value"] of correct option(s); exact match.
     * For short_answer: correct_answers is ["accepted1","accepted2",...]; case-insensitive trimmed match.
     */
    public function isCorrectAnswer(string $value): bool
    {
        $ca = $this->correct_answers;
        if ($this->type === 'short_answer') {
            $v = strtolower(trim($value));
            if ($v === '') {
                return false;
            }
            foreach ((array) $ca as $c) {
                if (strtolower(trim((string) $c)) === $v) {
                    return true;
                }
            }
            return false;
        }
        if (is_array($ca)) {
            return in_array($value, $ca, true);
        }
        return (string) $ca === (string) $value;
    }
}
