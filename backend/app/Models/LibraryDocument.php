<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LibraryDocument extends Model
{
    protected $fillable = [
        'library_category_id',
        'title',
        'slug',
        'abstract',
        'body',
        'document_type',
        'language',
        'published_at',
        'access_rule',
        'file_path',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (LibraryDocument $doc) {
            if (empty($doc->slug)) {
                $doc->slug = Str::slug($doc->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'library_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    public static function documentTypes(): array
    {
        return [
            'document' => 'Document',
            'policy' => 'Policy',
            'speech' => 'Speech',
            'pamphlet' => 'Pamphlet',
            'manual' => 'Ideological Manual',
            'resolution' => 'Congress Resolution',
            'other' => 'Other',
        ];
    }

    public static function accessRules(): array
    {
        return [
            'public' => 'Public',
            'member' => 'Members only',
            'leadership' => 'Leadership only',
        ];
    }
}
