<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    public const DOMAIN_ADMIN = 'admin';

    public const DOMAIN_API = 'api';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'domain',
        'resource_type',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
}
