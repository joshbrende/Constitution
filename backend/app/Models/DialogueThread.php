<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogueThread extends Model
{
    protected $fillable = [
        'dialogue_channel_id',
        'created_by_user_id',
        'title',
        'zanupf_section_id',
        'zimbabwe_section_id',
        'status',
    ];

    public function channel()
    {
        return $this->belongsTo(DialogueChannel::class, 'dialogue_channel_id');
    }

    public function messages()
    {
        return $this->hasMany(DialogueMessage::class);
    }

    public function zanupfSection()
    {
        return $this->belongsTo(Section::class, 'zanupf_section_id');
    }

    public function zimbabweSection()
    {
        return $this->belongsTo(Section::class, 'zimbabwe_section_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

