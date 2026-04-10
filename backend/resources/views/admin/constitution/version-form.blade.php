@extends('layouts.dashboard')

@section('title', ($version ? 'Edit' : 'New') . ' amendment – ' . $section->logical_number)
@section('page_heading', ($version ? 'Edit' : 'New') . ' amendment – Article ' . $section->logical_number)

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $version ? 'Edit draft' : 'New amendment' }}</div>
                    <div class="dash-panel-subtitle">{{ $section->logical_number }} – {{ $section->title }}</div>
                </div>
                <a href="{{ route('admin.constitution.sections.versions', $section) }}" class="dash-btn-ghost" style="text-decoration:none;">← Versions</a>
            </div>

            <form method="POST" action="{{ $version ? route('admin.constitution.versions.update', $version) : route('admin.constitution.versions.store', $section) }}">
                @csrf
                @if ($version)
                    @method('PUT')
                @endif
                <div style="margin-bottom:1rem;">
                    <label style="font-size:0.75rem;color:var(--text-muted);display:block;">Law reference (optional)</label>
                    <input type="text" name="law_reference" value="{{ old('law_reference', $version?->law_reference) }}" placeholder="e.g. Act 1/2019" style="width:100%;max-width:400px;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="font-size:0.75rem;color:var(--text-muted);display:block;">Body (article content)</label>
                    <textarea name="body" rows="16" required style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);font-family:monospace;font-size:0.9rem;">{{ old('body', $version?->body ?? $latest?->body) }}</textarea>
                </div>
                <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">{{ $version ? 'Update draft' : 'Create draft' }}</button>
            </form>
        </section>
    </div>
@endsection
