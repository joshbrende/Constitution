<?php

namespace App\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class TagController extends Controller
{
    private function ensureAdmin(): void
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage tags.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        $tags = Tag::withCount('courses')->orderBy('name')->get();
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.tags.create', ['tag' => new Tag()]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $valid = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:tags,slug'],
        ]);
        $valid['slug'] = $valid['slug'] ?: $this->uniqueSlug(Str::slug($valid['name']));
        Tag::create($valid);
        CacheHelper::clearTagsCache();
        return redirect()->route('admin.tags.index')->with('message', 'Tag created.');
    }

    public function edit(Tag $tag)
    {
        $this->ensureAdmin();
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $this->ensureAdmin();
        $valid = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:tags,slug,' . $tag->id],
        ]);
        $valid['slug'] = $valid['slug'] ?: $this->uniqueSlug(Str::slug($valid['name']), $tag->id);
        $tag->update($valid);
        CacheHelper::clearTagsCache();
        return redirect()->route('admin.tags.index')->with('message', 'Tag updated.');
    }

    private function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = $base;
        $n = 0;
        while (true) {
            $q = Tag::where('slug', $slug);
            if ($excludeId) {
                $q->where('id', '!=', $excludeId);
            }
            if (!$q->exists()) {
                return $slug;
            }
            $slug = $base . '-' . (++$n);
        }
    }
}
