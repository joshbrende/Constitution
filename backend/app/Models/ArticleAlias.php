<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleAlias extends Model
{
    protected $fillable = [
        'section_id',
        'alias_label',
        'notes',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
