<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PriorityProject extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'body',
        'image_url',
        'zanupf_section_id',
        'zimbabwe_section_id',
        'is_published',
        'published_at',
        'likes_count',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_published' => 'bool',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PriorityProject $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    public function zanupfSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'zanupf_section_id');
    }

    public function zimbabweSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'zimbabwe_section_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PriorityProjectLike::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}

