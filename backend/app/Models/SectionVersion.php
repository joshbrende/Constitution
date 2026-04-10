<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionVersion extends Model
{
    protected $fillable = [
        'section_id',
        'version_number',
        'law_reference',
        'effective_from',
        'effective_to',
        'body',
        'status',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function summaries()
    {
        return $this->hasMany(SectionSummaryVersion::class);
    }
}
