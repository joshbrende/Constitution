<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmendmentClauseRelation extends Model
{
    protected $fillable = [
        'amendment_section_id',
        'zimbabwe_section_id',
        'ref_label',
        'relation_type',
    ];

    public function amendmentSection()
    {
        return $this->belongsTo(Section::class, 'amendment_section_id');
    }

    public function zimbabweSection()
    {
        return $this->belongsTo(Section::class, 'zimbabwe_section_id');
    }
}
