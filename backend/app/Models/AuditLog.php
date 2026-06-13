<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class AuditLog extends Model
{
    protected static bool $allowMutation = false;

    protected $fillable = [
        'actor_user_id',
        'action',
        'target_type',
        'target_id',
        'metadata',
        'ip_address',
        'user_agent',
        'request_id',
        'previous_hash',
        'integrity_hash',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function getConnectionName(): ?string
    {
        return config('audit.connection') ?: parent::getConnectionName();
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * Temporarily allow update/delete (retention purge only).
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function allowingMutation(callable $callback)
    {
        $previous = static::$allowMutation;
        static::$allowMutation = true;

        try {
            return $callback();
        } finally {
            static::$allowMutation = $previous;
        }
    }

    protected static function booted(): void
    {
        static::updating(function () {
            if (! static::$allowMutation) {
                throw new RuntimeException('Audit logs are append-only and cannot be updated.');
            }
        });

        static::deleting(function () {
            if (! static::$allowMutation) {
                throw new RuntimeException('Audit logs are append-only and cannot be deleted.');
            }
        });
    }
}
