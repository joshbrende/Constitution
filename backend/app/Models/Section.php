<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'chapter_id',
        'logical_number',
        'slug',
        'title',
        'order',
        'is_active',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function versions()
    {
        return $this->hasMany(SectionVersion::class);
    }

    public function currentVersion()
    {
        return $this->hasOne(SectionVersion::class)
            ->whereNull('effective_to')
            ->where('status', 'published')
            ->latest('effective_from');
    }

    public function aliases()
    {
        return $this->hasMany(ArticleAlias::class);
    }

    public function amendsZimbabweSections()
    {
        return $this->belongsToMany(Section::class, 'amendment_clause_relations', 'amendment_section_id', 'zimbabwe_section_id')
            ->withPivot(['ref_label', 'relation_type'])
            ->withTimestamps();
    }

    public function amendmentClauseRelations()
    {
        return $this->hasMany(AmendmentClauseRelation::class, 'amendment_section_id');
    }
}
