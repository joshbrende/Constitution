<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresidiumMember extends Model
{
    protected $fillable = [
        'name',
        'title',
        'role_slug',
        'photo_url',
        'bio',
        'order',
        'is_published',
        'zanupf_section_id',
        'zimbabwe_section_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function zanupfSection()
    {
        return $this->belongsTo(Section::class, 'zanupf_section_id');
    }

    public function zimbabweSection()
    {
        return $this->belongsTo(Section::class, 'zimbabwe_section_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}

