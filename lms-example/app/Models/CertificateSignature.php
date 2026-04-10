<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateSignature extends Model
{
    protected $table = 'certificate_signatures';

    protected $fillable = ['type', 'user_id', 'path'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Get path for Board of Faculty (admin-set). */
    public static function getBoardOfFacultyPath(): ?string
    {
        $sig = self::where('type', 'board_of_faculty')->whereNull('user_id')->first();
        return $sig && $sig->path ? storage_path('app/' . $sig->path) : null;
    }

    /** Get path for Supervisor (admin-set). */
    public static function getSupervisorPath(): ?string
    {
        $sig = self::where('type', 'supervisor')->whereNull('user_id')->first();
        return $sig && $sig->path ? storage_path('app/' . $sig->path) : null;
    }

    /** Get path for Facilitator signature (per-user). */
    public static function getFacilitatorPath(?int $userId): ?string
    {
        if (!$userId) {
            return null;
        }
        $sig = self::where('type', 'facilitator')->where('user_id', $userId)->first();
        return $sig && $sig->path ? storage_path('app/' . $sig->path) : null;
    }
}
