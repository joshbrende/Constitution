<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartyOrgan;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PartyOrgansController extends Controller
{
    public function index(): View
    {
        $organs = PartyOrgan::ordered()->get();
        return view('admin.party-organs.index', compact('organs'));
    }

    public function create(): View
    {
        return view('admin.party-organs.form', ['organ' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'party_organs');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:party_organs,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_published'] = $request->boolean('is_published', true);
        if (isset($data['body']) && $data['body'] !== '') {
            $data['body'] = HtmlSanitizer::sanitize($data['body']);
        }
        PartyOrgan::create($data);
        return redirect()->route('admin.party-organs.index')->with('success', 'Party organ created.');
    }

    public function edit(PartyOrgan $party_organ): View
    {
        return view('admin.party-organs.form', ['organ' => $party_organ]);
    }

    public function update(Request $request, PartyOrgan $party_organ): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:party_organs,slug,' . $party_organ->id],
            'short_description' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published', true);
        if (isset($data['body']) && $data['body'] !== null) {
            $data['body'] = HtmlSanitizer::sanitize($data['body']);
        }
        $party_organ->update($data);
        return redirect()->route('admin.party-organs.index')->with('success', 'Party organ updated.');
    }

    public function destroy(PartyOrgan $party_organ): RedirectResponse
    {
        $this->authorize('admin.section', 'party_organs');
        $party_organ->delete();
        return redirect()->route('admin.party-organs.index')->with('success', 'Party organ deleted.');
    }
}
