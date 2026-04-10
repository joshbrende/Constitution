<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PresidiumMember;
use App\Rules\SafeUrlRule;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresidiumAdminController extends Controller
{
    public function index(): View
    {
        $members = PresidiumMember::ordered()->get();
        return view('admin.presidium.index', compact('members'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('title')->get(['id', 'title']);
        return view('admin.presidium.form', [
            'member' => null,
            'sections' => $sections,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'presidium');
        $data = $this->validateMember($request);
        PresidiumMember::create($data);

        return redirect()->route('admin.presidium.index')
            ->with('success', 'Presidium member created.');
    }

    public function edit(PresidiumMember $presidium): View
    {
        $sections = Section::orderBy('title')->get(['id', 'title']);
        return view('admin.presidium.form', [
            'member' => $presidium,
            'sections' => $sections,
        ]);
    }

    public function update(Request $request, PresidiumMember $presidium): RedirectResponse
    {
        $this->authorize('admin.section', 'presidium');
        $data = $this->validateMember($request);
        $presidium->update($data);

        return redirect()->route('admin.presidium.index')
            ->with('success', 'Presidium member updated.');
    }

    public function destroy(PresidiumMember $presidium): RedirectResponse
    {
        $this->authorize('admin.section', 'presidium');
        $presidium->delete();

        return redirect()->route('admin.presidium.index')
            ->with('success', 'Presidium member deleted.');
    }

    private function validateMember(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'role_slug' => ['required', 'string', 'max:100'],
            'photo_url' => ['nullable', 'string', 'max:500', new SafeUrlRule],
            'bio' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['sometimes', 'boolean'],
            'zanupf_section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'zimbabwe_section_id' => ['nullable', 'integer', 'exists:sections,id'],
        ]);

        $data['order'] = $data['order'] ?? 1;
        $data['is_published'] = (bool) ($data['is_published'] ?? true);
        $data['zanupf_section_id'] = $data['zanupf_section_id'] ?: null;
        $data['zimbabwe_section_id'] = $data['zimbabwe_section_id'] ?: null;

        return $data;
    }
}

