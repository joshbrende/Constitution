<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyProfile extends Model
{
    protected $fillable = [
        'history',
        'vision',
        'mission',
        'veterans_league_body',
        'veterans_league_leader_name',
        'veterans_league_leader_title',
        'womens_league_body',
        'womens_league_leader_name',
        'womens_league_leader_title',
        'youth_league_body',
        'youth_league_leader_name',
        'youth_league_leader_title',
    ];

    public function relatedSections()
    {
        return $this->hasMany(PartyProfileRelatedSection::class)->orderBy('sort_order');
    }
}

