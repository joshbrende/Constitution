<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPagesController extends Controller
{
    public function index(): View
    {
        $pages = StaticPage::orderBy('slug')->get();

        return view('admin.static-pages.index', compact('pages'));
    }

    public function edit(StaticPage $page): View
    {
        return view('admin.static-pages.form', compact('page'));
    }

    public function update(Request $request, StaticPage $page): RedirectResponse
    {
        $this->authorize('admin.section', 'static_pages');
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:50000'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published', true);

        $page->update($data);

        return redirect()->route('admin.static-pages.index')
            ->with('success', 'Page updated.');
    }
}

