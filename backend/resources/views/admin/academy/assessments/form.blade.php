@extends('layouts.dashboard')

@section('title', $assessment ? 'Edit Assessment – ' . $assessment->title : 'Add Assessment')
@section('page_heading', $assessment ? 'Edit Assessment' : 'Add Assessment')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $assessment ? 'Edit Assessment' : 'Add Assessment' }}</div>
                    <div class="dash-panel-subtitle">{{ $course->code }} – {{ $course->title }}</div>
                </div>
                <a href="{{ route('admin.academy.assessments.index', $course) }}" class="dash-btn-ghost" style="text-decoration:none;">← Assessments</a>
            </div>

            <form method="POST" action="{{ $assessment ? route('admin.academy.assessments.update', $assessment) : route('admin.academy.assessments.store', $course) }}" style="max-width:36rem;">
                @csrf
                @if ($assessment) @method('PUT') @endif
                <div style="display:grid;gap:1rem;">
                    <div>
                        <label for="title" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Title</label>
                        <input id="title" type="text" name="title" value="{{ old('title', $assessment?->title) }}" required
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div>
                        <label for="description" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Description</label>
                        <textarea id="description" name="description" rows="3"
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);font-family:inherit;resize:vertical;">{{ old('description', $assessment?->description) }}</textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label for="pass_mark" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Pass mark (%)</label>
                            <input id="pass_mark" type="number" name="pass_mark" min="0" max="100" value="{{ old('pass_mark', $assessment?->pass_mark ?? 70) }}"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                        </div>
                        <div>
                            <label for="duration_minutes" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Duration (minutes, optional)</label>
                            <input id="duration_minutes" type="number" name="duration_minutes" min="0" value="{{ old('duration_minutes', $assessment?->duration_minutes) }}"
                                placeholder="Leave empty for no limit"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                        </div>
                    </div>
                    <div>
                        <label for="status" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Status</label>
                        <select id="status" name="status"
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                            <option value="draft" {{ old('status', $assessment?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $assessment?->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $assessment?->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">{{ $assessment ? 'Save changes' : 'Create assessment' }}</button>
                        <a href="{{ route('admin.academy.assessments.index', $course) }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
