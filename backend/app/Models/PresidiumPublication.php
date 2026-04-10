<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PresidiumPublication extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'author',
        'summary',
        'cover_url',
        'article_url',
        'purchase_url',
        'online_copy_url',
        'is_featured',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }
}

