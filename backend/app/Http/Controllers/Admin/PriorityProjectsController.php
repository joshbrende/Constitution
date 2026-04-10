<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriorityProject;
use App\Rules\SafeUrlRule;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PriorityProjectsController extends Controller
{
    public function index(): View
    {
        $projects = PriorityProject::orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.priority-projects.index', compact('projects'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('title')->get(['id', 'title']);
        return view('admin.priority-projects.form', [
            'project' => null,
            'sections' => $sections,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'priority_projects');
        $data = $this->validateProject($request);
        $data['created_by_user_id'] = $request->user()?->id;
        if (!empty($data['is_published']) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        PriorityProject::create($data);

        return redirect()->route('admin.priority-projects.index')
            ->with('success', 'Priority project created.');
    }

    public function edit(PriorityProject $priority_project): View
    {
        $sections = Section::orderBy('title')->get(['id', 'title']);
        return view('admin.priority-projects.form', [
            'project' => $priority_project,
            'sections' => $sections,
        ]);
    }

    public function update(Request $request, PriorityProject $priority_project): RedirectResponse
    {
        $this->authorize('admin.section', 'priority_projects');
        $data = $this->validateProject($request, $priority_project);
        if (!empty($data['is_published']) && empty($priority_project->published_at)) {
            $data['published_at'] = now();
        }
        $priority_project->update($data);

        return redirect()->route('admin.priority-projects.index')
            ->with('success', 'Priority project updated.');
    }

    public function destroy(PriorityProject $priority_project): RedirectResponse
    {
        $this->authorize('admin.section', 'priority_projects');
        $priority_project->delete();

        return redirect()->route('admin.priority-projects.index')
            ->with('success', 'Priority project deleted.');
    }

    private function validateProject(Request $request, ?PriorityProject $project = null): array
    {
        $slugRule = ['nullable', 'string', 'max:255'];
        if ($project) {
            $slugRule[] = 'unique:priority_projects,slug,' . $project->id;
        } else {
            $slugRule[] = 'unique:priority_projects,slug';
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => $slugRule,
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:500', new SafeUrlRule],
            'zanupf_section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'zimbabwe_section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'is_published' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['zanupf_section_id'] = $data['zanupf_section_id'] ?: null;
        $data['zimbabwe_section_id'] = $data['zimbabwe_section_id'] ?: null;
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        return $data;
    }
}

