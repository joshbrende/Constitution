<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'public_id',
        'user_id',
        'course_id',
        'certificate_number',
        'verification_code',
        'issued_at',
        'expires_at',
        'revoked_at',
        'revoked_by_user_id',
        'revoked_reason',
        'pdf_status',
        'pdf_path',
    ];

    protected static function booted(): void
    {
        static::creating(function (Certificate $cert) {
            if (empty($cert->public_id)) {
                $cert->public_id = (string) Str::uuid();
            }
            if (empty($cert->verification_code)) {
                $cert->verification_code = strtoupper(Str::random(8));
            }
        });
    }

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate next certificate number (e.g. ZP-MEM-2025-00001).
     */
    public static function nextCertificateNumber(): string
    {
        // Keep the human-readable format but make it non-sequential to reduce enumeration.
        $year = date('Y');
        do {
            $suffix = strtoupper(Str::random(8));
            $number = sprintf('ZP-MEM-%s-%s', $year, $suffix);
        } while (static::where('certificate_number', $number)->exists());

        return $number;
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(?Carbon $at = null): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        $now = $at ?? now();
        return $this->expires_at->lt($now);
    }

    public function verificationStatus(): string
    {
        if ($this->isRevoked()) {
            return 'revoked';
        }
        if ($this->isExpired()) {
            return 'expired';
        }

        return 'active';
    }

    public function signedVerificationToken(): string
    {
        $payload = implode('|', [
            (string) ($this->public_id ?? ''),
            (string) ($this->certificate_number ?? ''),
            strtoupper((string) ($this->verification_code ?? '')),
            optional($this->issued_at)->format('Y-m-d') ?? '',
        ]);

        $appKey = (string) config('app.key', '');
        if (str_starts_with($appKey, 'base64:')) {
            $decoded = base64_decode(substr($appKey, 7), true);
            if ($decoded !== false) {
                $appKey = $decoded;
            }
        }

        return hash_hmac('sha256', $payload, $appKey);
    }

    public function hasValidVerificationToken(?string $token): bool
    {
        if (! is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($this->signedVerificationToken(), strtolower($token));
    }
}
