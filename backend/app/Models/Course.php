<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Course extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (Course $course) {
            Cache::forget('academy.courses');
            Cache::forget('academy.membership');
            Cache::forget('academy.course.' . $course->id);
        });
        static::deleted(function (Course $course) {
            Cache::forget('academy.courses');
            Cache::forget('academy.membership');
            Cache::forget('academy.course.' . $course->id);
        });
    }

    protected $fillable = [
        'code',
        'title',
        'description',
        'level',
        'is_mandatory',
        'grants_membership',
        'certificate_title',
        'status',
        'created_by',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'grants_membership' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }
}

