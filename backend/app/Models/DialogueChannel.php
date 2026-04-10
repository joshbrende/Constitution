<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogueChannel extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_public',
        'min_role_slug',
        'zanupf_section_id',
        'zimbabwe_section_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function threads()
    {
        return $this->hasMany(DialogueThread::class);
    }

    public function defaultZanupfSection()
    {
        return $this->belongsTo(Section::class, 'zanupf_section_id');
    }

    public function defaultZimbabweSection()
    {
        return $this->belongsTo(Section::class, 'zimbabwe_section_id');
    }

    public function canUserPost(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (! $this->min_role_slug) {
            return true;
        }

        $roles = $user->roles ?? collect();

        return $roles->contains(fn ($r) => ($r->slug ?? null) === $this->min_role_slug);
    }
}

