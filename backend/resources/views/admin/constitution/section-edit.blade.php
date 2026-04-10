@extends('layouts.dashboard')

@section('title', 'Edit Section – ' . $section->logical_number . ' ' . $section->title)
@section('page_heading', 'Edit Section')

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Edit Section</div>
                    <div class="dash-panel-subtitle">Chapter {{ $chapter->number }}: {{ $chapter->title }} — Section {{ $section->logical_number }}</div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <a href="{{ route('admin.constitution.sections.versions', $section) }}" class="dash-btn-ghost" style="text-decoration:none;">Amendments</a>
                    <a href="{{ route('admin.constitution.sections', $chapter) }}" class="dash-btn-ghost" style="text-decoration:none;">← Sections</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.constitution.sections.update', $section) }}" style="max-width:100%;">
                @csrf
                @method('PUT')
                <div style="display:grid;gap:1rem;max-width:48rem;">
                    <div>
                        <label for="logical_number" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Logical number (e.g. 1A, 2)</label>
                        <input id="logical_number" type="text" name="logical_number" value="{{ old('logical_number', $section->logical_number) }}" required
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div>
                        <label for="title" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Title</label>
                        <input id="title" type="text" name="title" value="{{ old('title', $section->title) }}" required
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div>
                        <label for="body" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Body text</label>
                        <textarea id="body" name="body" rows="16" required
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);font-family:inherit;resize:vertical;line-height:1.5;">{{ old('body', $bodySource?->body ?? '') }}</textarea>
                        <p class="dash-panel-subtitle" style="margin-top:0.35rem;">Edit the article text directly. Save as draft (for approval workflow) or publish now if you have Presidium/System Admin access.</p>
                    </div>
                    <div>
                        <label for="order" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Order</label>
                        <input id="order" type="number" name="order" min="0" value="{{ old('order', $section->order) }}"
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $section->is_active) ? 'checked' : '' }}
                                style="width:1rem;height:1rem;">
                            <span style="font-size:0.9rem;">Active (visible to readers)</span>
                        </label>
                    </div>
                    @if ($canPublishNow)
                        <div>
                            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                                <input type="hidden" name="publish_now" value="0">
                                <input type="checkbox" name="publish_now" value="1" {{ old('publish_now') ? 'checked' : '' }}
                                    style="width:1rem;height:1rem;">
                                <span style="font-size:0.9rem;font-weight:600;">Publish now</span>
                                <span style="font-size:0.8rem;color:var(--text-muted);">— bypass approval workflow and make changes live immediately</span>
                            </label>
                        </div>
                    @endif
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">Save</button>
                        <a href="{{ route('admin.constitution.sections', $chapter) }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
