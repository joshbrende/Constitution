<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionSummaryVersion extends Model
{
    protected $fillable = [
        'section_version_id',
        'language',
        'summary_text',
        'reading_level',
        'status',
    ];

    public function sectionVersion()
    {
        return $this->belongsTo(SectionVersion::class);
    }
}
