<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberNotificationCampaign extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const AUDIENCE_ALL = 'all';

    public const AUDIENCE_PROVINCE = 'province';

    public const AUDIENCE_ROLE = 'role';

    protected $fillable = [
        'title',
        'body',
        'audience_type',
        'province_id',
        'role_id',
        'cta_type',
        'cta_label',
        'cta_tab',
        'cta_screen',
        'cta_params',
        'cta_url',
        'status',
        'published_at',
        'recipients_count',
        'created_by_user_id',
        'trigger',
        'source_type',
        'source_id',
    ];

    protected function casts(): array
    {
        return [
            'cta_params' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}
