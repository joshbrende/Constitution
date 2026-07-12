<?php

namespace App\Models;

use App\Enums\CertificateApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CertificateApplication extends Model
{
    protected $fillable = [
        'public_id',
        'user_id',
        'course_id',
        'assessment_attempt_id',
        'admission_source',
        'receipt_number',
        'payment_reference_code',
        'fee_amount',
        'fee_currency',
        'status',
        'exam_passed_at',
        'payment_confirmed_at',
        'payment_confirmed_by',
        'payment_reference_note',
        'presidium_approved_at',
        'presidium_approved_by',
        'presidium_note',
        'certificate_id',
        'printed_at',
        'printed_by',
        'ready_for_collection_at',
        'collected_at',
        'collected_by',
        'collection_office',
    ];

    protected static function booted(): void
    {
        static::creating(function (CertificateApplication $application) {
            if (empty($application->public_id)) {
                $application->public_id = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => CertificateApplicationStatus::class,
            'fee_amount' => 'decimal:2',
            'exam_passed_at' => 'datetime',
            'payment_confirmed_at' => 'datetime',
            'presidium_approved_at' => 'datetime',
            'printed_at' => 'datetime',
            'ready_for_collection_at' => 'datetime',
            'collected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function assessmentAttempt(): BelongsTo
    {
        return $this->belongsTo(AssessmentAttempt::class);
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

    public function paymentConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_confirmed_by');
    }

    public function presidiumApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'presidium_approved_by');
    }

    /**
     * API routes are scoped to the authenticated owner (404 if not owned).
     */
    public function resolveRouteBinding($value, $field = null): Model
    {
        $query = static::query()->where($field ?? $this->getRouteKeyName(), $value);

        if (request()->is('api/v1/academy/applications/*') && auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        return $query->firstOrFail();
    }
}
