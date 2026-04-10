<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\PartyProfile;
use App\Models\PartyProfileRelatedSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartyController extends Controller
{
    public function index(): View
    {
        $articleTheParty = Section::where('slug', 'article-1-the-party')->first();
        $articleOrgans = Section::where('slug', 'article-4-principal-organs-and-structure')->first();
        $profile = PartyProfile::first();
        $relatedSections = $profile ? $profile->relatedSections()->with('section')->get() : collect();
        $sectionsForSelect = Section::whereHas('versions')
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);

        return view('admin.party.index', [
            'articleTheParty' => $articleTheParty,
            'articleOrgans' => $articleOrgans,
            'profile' => $profile,
            'relatedSections' => $relatedSections,
            'sectionsForSelect' => $sectionsForSelect,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'party');
        $data = $request->validate([
            'history' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'veterans_league_body' => ['nullable', 'string'],
            'veterans_league_leader_name' => ['nullable', 'string', 'max:255'],
            'veterans_league_leader_title' => ['nullable', 'string', 'max:255'],
            'womens_league_body' => ['nullable', 'string'],
            'womens_league_leader_name' => ['nullable', 'string', 'max:255'],
            'womens_league_leader_title' => ['nullable', 'string', 'max:255'],
            'youth_league_body' => ['nullable', 'string'],
            'youth_league_leader_name' => ['nullable', 'string', 'max:255'],
            'youth_league_leader_title' => ['nullable', 'string', 'max:255'],
        ]);

        PartyProfile::updateOrCreate(['id' => 1], $data);

        return redirect()->route('admin.party.index')->with('success', 'Party profile updated.');
    }

    public function attachSection(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'party');
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);
        $profile = PartyProfile::firstOrCreate(['id' => 1]);
        $maxOrder = $profile->relatedSections()->max('sort_order') ?? 0;
        $profile->relatedSections()->firstOrCreate(
            ['section_id' => $data['section_id']],
            ['label' => $data['label'] ?? null, 'sort_order' => $maxOrder + 10]
        );
        return redirect()->route('admin.party.index')->with('success', 'Article linked to Party.');
    }

    public function detachSection(int $id): RedirectResponse
    {
        $this->authorize('admin.section', 'party');
        PartyProfileRelatedSection::where('id', $id)->delete();
        return redirect()->route('admin.party.index')->with('success', 'Article unlinked.');
    }

    public function updateRelatedSectionOrder(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'party');
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:party_profile_related_sections,id'],
        ]);
        foreach ($data['order'] as $sortOrder => $id) {
            PartyProfileRelatedSection::where('id', $id)->update(['sort_order' => (int) $sortOrder]);
        }
        return redirect()->route('admin.party.index')->with('success', 'Order updated.');
    }
}

