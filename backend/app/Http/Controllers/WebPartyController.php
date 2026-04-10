<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\PartyProfile;
use Illuminate\View\View;

class WebPartyController extends Controller
{
    /**
     * High-level "The Party" page, summarising Article 1 of the ZANU PF Constitution.
     */
    public function index(): View
    {
        $section = Section::where('slug', 'article-1-the-party')->first();

        $version = $section
            ? $section->currentVersion()->first()
                ?? $section->versions()
                    ->where('status', 'published')
                    ->orderByDesc('version_number')
                    ->first()
            : null;

        $body = $version?->body ?? null;
        $profile = PartyProfile::first();
        $relatedSections = $profile ? $profile->relatedSections()->with('section')->orderBy('sort_order')->get() : collect();
        $leagues = \App\Models\PartyLeague::ordered()->get();
        if ($leagues->isEmpty() && $profile) {
            $leagues = collect([
                (object)['name' => 'Veterans League', 'leader_name' => $profile->veterans_league_leader_name, 'leader_title' => $profile->veterans_league_leader_title, 'body' => $profile->veterans_league_body],
                (object)['name' => "Women's League", 'leader_name' => $profile->womens_league_leader_name, 'leader_title' => $profile->womens_league_leader_title, 'body' => $profile->womens_league_body],
                (object)['name' => 'Youth League', 'leader_name' => $profile->youth_league_leader_name, 'leader_title' => $profile->youth_league_leader_title, 'body' => $profile->youth_league_body],
            ]);
        }

        return view('sections.party', [
            'body' => $body,
            'profile' => $profile,
            'relatedSections' => $relatedSections,
            'leagues' => $leagues,
        ]);
    }
}

