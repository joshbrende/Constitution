<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePresidiumPublicationRequest;
use App\Http\Requests\Admin\UpdatePresidiumPublicationRequest;
use App\Models\PresidiumPublication;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PresidiumPublicationsController extends Controller
{
    public function index(): View
    {
        $this->authorize('admin.section', 'presidium');

        $publications = PresidiumPublication::query()
            ->orderBy('order')
            ->orderBy('id')
            ->paginate(50);

        return view('admin.presidium-publications.index', compact('publications'));
    }

    public function create(): View
    {
        $this->authorize('admin.section', 'presidium');

        $publication = new PresidiumPublication();

        return view('admin.presidium-publications.form', compact('publication'));
    }

    public function store(StorePresidiumPublicationRequest $request): RedirectResponse
    {
        $publication = PresidiumPublication::create($request->validated());

        return redirect()
            ->route('admin.presidium-publications.edit', $publication)
            ->with('success', 'Publication created.');
    }

    public function edit(PresidiumPublication $publication): View
    {
        $this->authorize('admin.section', 'presidium');

        return view('admin.presidium-publications.form', compact('publication'));
    }

    public function update(UpdatePresidiumPublicationRequest $request, PresidiumPublication $publication): RedirectResponse
    {
        $publication->update($request->validated());

        return redirect()
            ->route('admin.presidium-publications.edit', $publication)
            ->with('success', 'Publication updated.');
    }

    public function destroy(PresidiumPublication $publication): RedirectResponse
    {
        $this->authorize('admin.section', 'presidium');

        $publication->delete();

        return redirect()
            ->route('admin.presidium-publications.index')
            ->with('success', 'Publication deleted.');
    }
}

