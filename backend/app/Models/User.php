<?php

namespace App\Models;

use App\Enums\MembershipStanding;
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
        'membership_standing',
        'branch_admitted_at',
        'branch_admitted_by_user_id',
        'branch_admission_note',
        'cadre_designated_at',
        'cadre_designated_by_user_id',
        'province_id',
        'district_id',
        'branch_id',
        'cell_id',
        'national_id',
        'password',
        'accepted_terms_at',
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
            'accepted_terms_at' => 'datetime',
            'national_id_verified_at' => 'datetime',
            'membership_standing' => MembershipStanding::class,
            'branch_admitted_at' => 'datetime',
            'cadre_designated_at' => 'datetime',
        ];
    }

    public function hasBranchAdmission(): bool
    {
        return $this->branch_admitted_at !== null;
    }

    public function isCadreDesignee(): bool
    {
        return $this->cadre_designated_at !== null;
    }

    public function branchAdmittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'branch_admitted_by_user_id');
    }

    public function cadreDesignatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadre_designated_by_user_id');
    }

    public function membershipStandingLabel(): string
    {
        $standing = $this->membership_standing;

        return $standing instanceof MembershipStanding
            ? $standing->label()
            : MembershipStanding::Applicant->label();
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

    public function hasPermission(string $slug): bool
    {
        $this->loadMissing('roles.permissions');

        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $slug)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermissionWithPrefix(string $prefix): bool
    {
        $this->loadMissing('roles.permissions');

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                if (str_starts_with((string) $permission->slug, $prefix)) {
                    return true;
                }
            }
        }

        return false;
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

    public function certificateApplications(): HasMany
    {
        return $this->hasMany(\App\Models\CertificateApplication::class);
    }

    public function pushTokens(): HasMany
    {
        return $this->hasMany(UserPushToken::class);
    }

    public function webPushSubscriptions(): HasMany
    {
        return $this->hasMany(UserWebPushSubscription::class);
    }

    public function unreadAcademyPortalMessagesCount(): int
    {
        return \App\Support\PortalNotifications::unreadCount($this);
    }
}
