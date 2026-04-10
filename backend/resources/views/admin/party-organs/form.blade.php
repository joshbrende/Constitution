@extends('layouts.dashboard')

@section('title', $organ ? 'Edit – ' . $organ->name : 'Create Party Organ')
@section('page_heading', $organ ? 'Edit party organ' : 'Create party organ')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $organ ? 'Edit organ' : 'New party organ' }}</div>
                    <div class="dash-panel-subtitle">Name, short description, and body text shown in the app.</div>
                </div>
                <a href="{{ route('admin.party-organs.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Party Organs</a>
            </div>

            <form method="POST" action="{{ $organ ? route('admin.party-organs.update', $organ) : route('admin.party-organs.store') }}">
                @csrf
                @if ($organ) @method('PUT') @endif

                <div style="display:grid;gap:1rem;max-width:40rem;">
                    <div>
                        <label for="name" class="form-label">Name <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $organ?->name) }}" required class="form-input" placeholder="e.g. Congress, Central Committee">
                    </div>
                    <div>
                        <label for="slug" class="form-label">Slug</label>
                        <input id="slug" type="text" name="slug" value="{{ old('slug', $organ?->slug) }}" placeholder="auto from name" class="form-input">
                    </div>
                    <div>
                        <label for="short_description" class="form-label">Short description</label>
                        <input id="short_description" type="text" name="short_description" value="{{ old('short_description', $organ?->short_description) }}" maxlength="500" class="form-input" placeholder="One line summary for list view.">
                    </div>
                    <div>
                        <label for="body" class="form-label">Body</label>
                        <textarea id="body" name="body" rows="12" class="form-input" placeholder="Full description, roles, and provisions. Plain text or simple HTML.">{{ old('body', $organ?->body) }}</textarea>
                    </div>
                    <div>
                        <label for="order" class="form-label">Order</label>
                        <input id="order" type="number" name="order" value="{{ old('order', $organ?->order ?? 0) }}" min="0" class="form-input" style="max-width:6rem;">
                        <p class="form-help">Lower numbers appear first in the app.</p>
                    </div>
                    <div>
                        <label class="form-check">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $organ?->is_published ?? true) ? 'checked' : '' }}>
                            <span>Published (visible in app)</span>
                        </label>
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="form-btn-primary">{{ $organ ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('admin.party-organs.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

        <style>
            .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-main); margin-bottom:0.35rem; }
            .form-input { width:100%; padding:0.5rem 0.65rem; border:1px solid var(--border-subtle); border-radius:0.4rem; background:rgba(15,23,42,0.9); color:var(--text-main); font-size:0.95rem; }
            .form-input:focus { outline:none; border-color:var(--zanupf-gold); }
            .form-help { font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; }
            .form-check { display:flex; align-items:center; gap:0.6rem; cursor:pointer; font-size:0.9rem; }
            .form-check input[type="checkbox"] { width:1.1rem; height:1.1rem; }
            .form-btn-primary { padding:0.5rem 1.25rem; background:var(--zanupf-green); color:#fff; border:none; border-radius:0.4rem; cursor:pointer; font-weight:600; font-size:0.9rem; }
        </style>
    </div>
@endsection
