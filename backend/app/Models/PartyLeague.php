<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PartyLeague extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'leader_name',
        'leader_title',
        'body',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (PartyLeague $league) {
            if (empty($league->slug)) {
                $league->slug = Str::slug($league->name);
            }
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
