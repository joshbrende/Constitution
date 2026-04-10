<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class BadgeController extends Controller
{
    private function ensureAdmin(): void
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage badges.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();

        $badges = Badge::orderBy('points_required')->get();

        return view('admin.badges.index', compact('badges'));
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('admin.badges.create', ['badge' => new Badge()]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $valid = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:badges,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'points_required' => ['nullable', 'integer', 'min:0'],
        ]);

        $valid['slug'] = $valid['slug'] ?: $this->uniqueSlug(Str::slug($valid['name']));
        $valid['points_required'] = (int) ($valid['points_required'] ?? 0);

        Badge::create($valid);

        return redirect()->route('admin.badges.index')->with('message', 'Badge created.');
    }

    public function edit(Badge $badge)
    {
        $this->ensureAdmin();

        return view('admin.badges.edit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $this->ensureAdmin();

        $valid = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:badges,slug,' . $badge->id],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'points_required' => ['nullable', 'integer', 'min:0'],
        ]);

        $valid['slug'] = $valid['slug'] ?: $this->uniqueSlug(Str::slug($valid['name']), $badge->id);
        $valid['points_required'] = (int) ($valid['points_required'] ?? 0);

        $badge->update($valid);

        return redirect()->route('admin.badges.index')->with('message', 'Badge updated.');
    }

    private function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = $base;
        $n = 0;
        while (true) {
            $q = Badge::where('slug', $slug);
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
