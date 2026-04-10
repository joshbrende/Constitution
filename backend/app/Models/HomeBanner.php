<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeBanner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image_url',
        'cta_label',
        'cta_url',
        'cta_type',
        'cta_tab',
        'cta_screen',
        'cta_params',
        'is_published',
        'sort_order',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'cta_params' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

