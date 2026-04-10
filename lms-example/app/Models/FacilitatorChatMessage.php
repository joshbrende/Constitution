<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilitatorChatMessage extends Model
{
    protected $fillable = [
        'course_id', 'unit_id', 'user_id', 'body', 'type',
        'in_reply_to_id', 'status',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inReplyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'in_reply_to_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'in_reply_to_id')->orderBy('created_at');
    }

    public function isQuestion(): bool
    {
        return $this->type === 'question';
    }

    public function isReply(): bool
    {
        return $this->type === 'reply';
    }

    public function isAnnouncement(): bool
    {
        return $this->type === 'announcement';
    }
}
