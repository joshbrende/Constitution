<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PartyLeague;
use App\Models\PartyProfile;
use App\Models\PartyProfileRelatedSection;
use App\Models\Section;
use Illuminate\Http\JsonResponse;

class PartyController extends Controller
{
    /**
     * Public Party profile for mobile/web: overview, vision, mission, leagues, related articles.
     */
    public function profile(): JsonResponse
    {
        $profile = PartyProfile::first();

        $section = Section::where('slug', 'article-1-the-party')->first();
        $version = $section
            ? $section->currentVersion()->first()
                ?? $section->versions()
                    ->where('status', 'published')
                    ->orderByDesc('version_number')
                    ->first()
            : null;

        $leaguesFromTable = PartyLeague::ordered()->get();
        $leaguesArray = $leaguesFromTable->map(fn ($l) => [
            'slug' => $l->slug,
            'name' => $l->name,
            'leader_name' => $l->leader_name,
            'leader_title' => $l->leader_title,
            'body' => $l->body,
        ])->values()->all();

        $relatedSections = $profile
            ? $profile->relatedSections()->with('section')->orderBy('sort_order')->get()
            : collect();
        $relatedArray = $relatedSections->map(function ($rel) {
            $sec = $rel->section;
            if (!$sec) {
                return null;
            }
            return [
                'id' => $sec->id,
                'title' => $rel->label ?: $sec->title,
                'slug' => $sec->slug,
            ];
        })->filter()->values()->all();

        $data = [
            'history' => $profile?->history,
            'vision' => $profile?->vision,
            'mission' => $profile?->mission,
            'article_body' => $version?->body,
            'leagues' => $leaguesArray,
            'related_sections' => $relatedArray,
        ];

        if ($leaguesArray === []) {
            $data['veterans_league'] = [
                'leader_name' => $profile?->veterans_league_leader_name,
                'leader_title' => $profile?->veterans_league_leader_title,
                'body' => $profile?->veterans_league_body,
            ];
            $data['womens_league'] = [
                'leader_name' => $profile?->womens_league_leader_name,
                'leader_title' => $profile?->womens_league_leader_title,
                'body' => $profile?->womens_league_body,
            ];
            $data['youth_league'] = [
                'leader_name' => $profile?->youth_league_leader_name,
                'leader_title' => $profile?->youth_league_leader_title,
                'body' => $profile?->youth_league_body,
            ];
        } else {
            $bySlug = collect($leaguesArray)->keyBy('slug');
            $legacyMap = [
                'veterans_league' => 'veterans-league',
                'womens_league' => 'womens-league',
                'youth_league' => 'youth-league',
            ];
            foreach ($legacyMap as $key => $slug) {
                $l = $bySlug->get($slug);
                $data[$key] = $l ? [
                    'leader_name' => $l['leader_name'] ?? null,
                    'leader_title' => $l['leader_title'] ?? null,
                    'body' => $l['body'] ?? null,
                ] : [
                    'leader_name' => null,
                    'leader_title' => null,
                    'body' => null,
                ];
            }
        }

        return response()->json(['data' => $data]);
    }
}
