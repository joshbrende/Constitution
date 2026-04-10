<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartyLeague;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PartyLeaguesController extends Controller
{
    public function index(): View
    {
        $leagues = PartyLeague::ordered()->get();
        return view('admin.party-leagues.index', compact('leagues'));
    }

    public function create(): View
    {
        return view('admin.party-leagues.form', ['league' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'party_leagues');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:party_leagues,slug'],
            'leader_name' => ['nullable', 'string', 'max:255'],
            'leader_title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? PartyLeague::max('sort_order') + 10);
        PartyLeague::create($data);
        return redirect()->route('admin.party-leagues.index')->with('success', 'League created.');
    }

    public function edit(PartyLeague $party_league): View
    {
        return view('admin.party-leagues.form', ['league' => $party_league]);
    }

    public function update(Request $request, PartyLeague $party_league): RedirectResponse
    {
        $this->authorize('admin.section', 'party_leagues');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:party_leagues,slug,' . $party_league->id],
            'leader_name' => ['nullable', 'string', 'max:255'],
            'leader_title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['sort_order'] = (int) ($data['sort_order'] ?? $party_league->sort_order);
        $party_league->update($data);
        return redirect()->route('admin.party-leagues.index')->with('success', 'League updated.');
    }

    public function destroy(PartyLeague $party_league): RedirectResponse
    {
        $this->authorize('admin.section', 'party_leagues');
        $party_league->delete();
        return redirect()->route('admin.party-leagues.index')->with('success', 'League deleted.');
    }
}
