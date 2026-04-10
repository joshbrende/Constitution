<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, MustVerifyEmail, Notifiable;

    protected $fillable = ['name', 'surname', 'email', 'password', 'points'];

    /** For password reset: email to send the reset link to. */
    public function getEmailForPasswordReset(): string
    {
        return (string) $this->email;
    }

    /** Send the password reset notification (used by Password facade). */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->morphToMany(
            \App\Models\Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        )->where('model_has_roles.model_type', self::class);
    }

    /** Role names are stored lowercase in `roles` table. */
    public function hasRole(string $name): bool
    {
        $name = strtolower(trim($name));
        return $this->roles()->whereRaw('LOWER(roles.name) = ?', [$name])->exists();
    }

    public function isInstructor(): bool
    {
        return $this->isFacilitator();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super-admin');
    }

    /** Facilitators and instructors are the same; both can teach/edit courses. */
    public function isFacilitator(): bool
    {
        return $this->hasRole('facilitator') || $this->hasRole('instructor');
    }

    /** Only facilitators (or instructors) and admins may create or edit courses. */
    public function canEditCourses(): bool
    {
        return $this->isFacilitator() || $this->isAdmin();
    }

    /** Whether the user may edit this specific course (admin, or instructor of this course). */
    public function canEditCourse(Course $course): bool
    {
        return $this->isAdmin() || (int) $this->id === (int) $course->instructor_id;
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function instructingCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function instructorRequests(): HasMany
    {
        return $this->hasMany(InstructorRequest::class);
    }

    public function facilitatorRatingsReceived(): HasMany
    {
        return $this->hasMany(FacilitatorRating::class, 'instructor_id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Badge::class, 'user_badges', 'user_id', 'badge_id')->withTimestamps();
    }
}
