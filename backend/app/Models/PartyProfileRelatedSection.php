<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyProfileRelatedSection extends Model
{
    protected $fillable = ['party_profile_id', 'section_id', 'label', 'sort_order'];

    public function partyProfile()
    {
        return $this->belongsTo(PartyProfile::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
