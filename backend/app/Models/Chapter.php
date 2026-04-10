<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = [
        'part_id',
        'constitution_slug',
        'number',
        'title',
        'order',
    ];

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }
}
