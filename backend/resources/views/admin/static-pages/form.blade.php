@extends('layouts.dashboard')

@section('title', 'Edit page – ' . $page->title)
@section('page_heading', 'Edit static page')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $page->title }}</div>
                    <div class="dash-panel-subtitle">
                        Editing <code style="font-size:0.8rem;">{{ $page->slug }}</code>. This text appears in the app.
                    </div>
                </div>
                <a href="{{ route('admin.static-pages.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Pages</a>
            </div>

            @if ($errors->any())
                <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.static-pages.update', $page) }}">
                @csrf
                @method('PUT')

                <div style="display:grid;gap:1rem;max-width:48rem;">
                    <div>
                        <label class="form-label" for="title">Title</label>
                        <input id="title" type="text" name="title" class="form-input"
                               value="{{ old('title', $page->title) }}" required>
                    </div>

                    <div>
                        <label class="form-label" for="body">Body</label>
                        <textarea id="body" name="body" rows="14" class="form-input"
                                  placeholder="Write the help / terms / privacy content here.">{{ old('body', $page->body) }}</textarea>
                        <p class="form-help">Plain text or basic Markdown is recommended. The mobile app renders this in a scrollable help screen.</p>
                    </div>

                    <label class="form-check">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                        <span>Published – show this page in the app.</span>
                    </label>
                </div>

                <div style="margin-top:1.25rem;display:flex;gap:0.75rem;">
                    <button type="submit" class="form-btn-primary">
                        Save changes
                    </button>
                    <a href="{{ route('admin.static-pages.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                </div>
            </form>
        </section>
    </div>

    <style>
        .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-main); margin-bottom:0.35rem; }
        .form-input { width:100%; padding:0.5rem 0.65rem; border:1px solid var(--border-subtle); border-radius:0.4rem; background:rgba(15,23,42,0.9); color:var(--text-main); font-size:0.95rem; }
        .form-input:focus { outline:none; border-color:var(--zanupf-gold); }
        .form-help { font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; line-height:1.4; }
        .form-check { display:flex; align-items:flex-start; gap:0.6rem; cursor:pointer; font-size:0.9rem; margin-top:0.5rem; }
        .form-check input[type="checkbox"] { width:1.1rem; height:1.1rem; margin-top:0.15rem; flex-shrink:0; }
        .form-btn-primary { padding:0.5rem 1.25rem; background:var(--zanupf-green); color:#fff; border:none; border-radius:0.4rem; cursor:pointer; font-weight:600; font-size:0.9rem; }
        .form-btn-primary:hover { filter:brightness(1.1); }
    </style>
@endsection

