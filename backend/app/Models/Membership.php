<?php

namespace App\Models;

use App\Enums\MembershipWingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    protected $fillable = [
        'user_id',
        'wing',
        'status',
        'joined_at',
        'ended_at',
        'assigned_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => MembershipWingStatus::class,
            'joined_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function isActive(): bool
    {
        return $this->status === MembershipWingStatus::Active;
    }
}
