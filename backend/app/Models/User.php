<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'wing',
        'province_id',
        'district_id',
        'branch_id',
        'cell_id',
        'national_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'national_id_verified_at' => 'datetime',
        ];
    }

    public function hasVerifiedNationalId(): bool
    {
        return $this->national_id !== null && $this->national_id !== '' && $this->national_id_verified_at !== null;
    }

    /**
     * Roles assigned to the user.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains(fn ($role) => $role->slug === $slug);
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function assessmentAttempts(): HasMany
    {
        return $this->hasMany(\App\Models\AssessmentAttempt::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(\App\Models\Certificate::class);
    }
}
