<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;

class SectionPolicy
{
    public function comment(User $_user, Section $section): bool
    {
        return (bool) ($section->is_active ?? true);
    }
}
