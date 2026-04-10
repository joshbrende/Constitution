@extends('layouts.dashboard')

@section('title', 'Edit Chapter – ' . $chapter->title)
@section('page_heading', 'Edit Chapter')

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
                    <div class="dash-panel-title">Edit Chapter</div>
                    <div class="dash-panel-subtitle">Part {{ $part->number }}: {{ $part->title }} — Chapter {{ $chapter->number }}</div>
                </div>
                <a href="{{ route('admin.constitution.chapters', $part) }}" class="dash-btn-ghost" style="text-decoration:none;">← Chapters</a>
            </div>

            <form method="POST" action="{{ route('admin.constitution.chapters.update', $chapter) }}" style="max-width:32rem;">
                @csrf
                @method('PUT')
                <div style="display:grid;gap:1rem;">
                    <div>
                        <label for="number" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Number</label>
                        <input id="number" type="number" name="number" min="1" value="{{ old('number', $chapter->number) }}" required
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div>
                        <label for="title" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Title</label>
                        <input id="title" type="text" name="title" value="{{ old('title', $chapter->title) }}" required
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div>
                        <label for="order" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Order</label>
                        <input id="order" type="number" name="order" min="0" value="{{ old('order', $chapter->order) }}"
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">Save changes</button>
                        <a href="{{ route('admin.constitution.chapters', $part) }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
