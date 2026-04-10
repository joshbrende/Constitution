@extends('layouts.dashboard')

@section('title', $project ? 'Edit priority project' : 'Add priority project')
@section('page_heading', $project ? 'Edit priority project' : 'Add priority project')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">
                        {{ $project ? 'Edit project' : 'New project' }}
                    </div>
                    <div class="dash-panel-subtitle">
                        Describe the project clearly. Mark it as published when you are ready for members to see it in the app.
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.priority-projects.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Back to projects</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="dash-alert dash-alert--error">
                    <ul style="margin:0;padding-left:1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ $project ? route('admin.priority-projects.update', $project) : route('admin.priority-projects.store') }}">
                @csrf
                @if($project)
                    @method('PUT')
                @endif

                <div class="form-grid">
                    <div class="form-grid-main">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-input"
                               value="{{ old('title', $project->title ?? '') }}" required>

                        <label class="form-label" style="margin-top:1rem;">Short summary</label>
                        <textarea name="summary" rows="2" class="form-input"
                                  placeholder="One or two sentences explaining the project.">{{ old('summary', $project->summary ?? '') }}</textarea>

                        <label class="form-label" style="margin-top:1rem;">Detail</label>
                        <textarea name="body" rows="8" class="form-input"
                                  placeholder="Background, objectives, benefits, and key milestones.">{{ old('body', $project->body ?? '') }}</textarea>
                    </div>

                    <div class="form-grid-side">
                        <label class="form-label">Image URL (optional)</label>
                        <input type="text" name="image_url" class="form-input"
                               value="{{ old('image_url', $project->image_url ?? '') }}"
                               placeholder="https://…">

                        <label class="form-label" style="margin-top:1rem;">ZANU PF article</label>
                        <select name="zanupf_section_id" class="form-input">
                            <option value="">None</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ (string) old('zanupf_section_id', $project->zanupf_section_id ?? '') === (string) $section->id ? 'selected' : '' }}>
                                    {{ $section->title }}
                                </option>
                            @endforeach
                        </select>

                        <label class="form-label" style="margin-top:1rem;">Zimbabwe Constitution article</label>
                        <select name="zimbabwe_section_id" class="form-input">
                            <option value="">None</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ (string) old('zimbabwe_section_id', $project->zimbabwe_section_id ?? '') === (string) $section->id ? 'selected' : '' }}>
                                    {{ $section->title }}
                                </option>
                            @endforeach
                        </select>

                        <label class="form-label" style="margin-top:1rem;">Slug (optional)</label>
                        <input type="text" name="slug" class="form-input"
                               value="{{ old('slug', $project->slug ?? '') }}"
                               placeholder="priority-project-name">

                        <div style="margin-top:1rem;">
                            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.9rem;color:var(--text-main);">
                                <input type="checkbox" name="is_published" value="1"
                                       {{ old('is_published', $project->is_published ?? false) ? 'checked' : '' }}>
                                <span>Published (visible in the mobile app)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div style="margin-top:1.25rem;display:flex;gap:0.75rem;">
                    <button type="submit" class="form-btn-primary">
                        {{ $project ? 'Save changes' : 'Create project' }}
                    </button>
                    <a href="{{ route('admin.priority-projects.index') }}" class="dash-btn-ghost" style="text-decoration:none;">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection

