<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LibraryCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
    ];

    protected static function booted(): void
    {
        static::creating(function (LibraryCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LibraryCategory::class, 'parent_id')->orderBy('order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LibraryDocument::class, 'library_category_id')->orderBy('published_at', 'desc');
    }
}
