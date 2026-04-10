<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $table = 'certificates';

    protected $fillable = [
        'user_id', 'course_id', 'certificate_number',
        'template', 'content', 'pdf_path', 'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public static function ensureForUserAndCourse(int $userId, int $courseId): ?self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $courseId],
            [
                'certificate_number' => 'CERT-' . strtoupper(Str::random(10)) . '-' . $courseId . '-' . $userId,
                'template' => 'default',
                'content' => null,
                'issued_at' => now(),
            ]
        );
    }
}
