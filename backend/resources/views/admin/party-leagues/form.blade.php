@extends('layouts.dashboard')

@section('title', $league ? 'Edit – ' . $league->name : 'Create Party League')
@section('page_heading', $league ? 'Edit league' : 'Create league')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $league ? 'Edit league' : 'New league' }}</div>
                    <div class="dash-panel-subtitle">Name, leader, and description shown on The Party page and in the app.</div>
                </div>
                <a href="{{ route('admin.party-leagues.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Party Leagues</a>
            </div>

            <form method="POST" action="{{ $league ? route('admin.party-leagues.update', $league) : route('admin.party-leagues.store') }}">
                @csrf
                @if ($league) @method('PUT') @endif

                <div style="display:grid;gap:1rem;max-width:40rem;">
                    <div>
                        <label for="name" class="form-label">Name <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $league?->name) }}" required class="form-input" placeholder="e.g. Veterans League, Women's League">
                    </div>
                    <div>
                        <label for="slug" class="form-label">Slug</label>
                        <input id="slug" type="text" name="slug" value="{{ old('slug', $league?->slug) }}" placeholder="auto from name" class="form-input">
                        <p class="form-help">Used in the API; leave blank to auto-generate from name.</p>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div>
                            <label for="leader_name" class="form-label">Leader name</label>
                            <input id="leader_name" type="text" name="leader_name" value="{{ old('leader_name', $league?->leader_name) }}" class="form-input" placeholder="e.g. Cde Douglas Mahiya">
                        </div>
                        <div>
                            <label for="leader_title" class="form-label">Leader title</label>
                            <input id="leader_title" type="text" name="leader_title" value="{{ old('leader_title', $league?->leader_title) }}" class="form-input" placeholder="e.g. Secretary Veterans League">
                        </div>
                    </div>
                    <div>
                        <label for="body" class="form-label">Description / mandate</label>
                        <textarea id="body" name="body" rows="8" class="form-input" placeholder="Mandate and role of the league. Plain text or simple HTML.">{{ old('body', $league?->body) }}</textarea>
                    </div>
                    <div>
                        <label for="sort_order" class="form-label">Sort order</label>
                        <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $league?->sort_order ?? 0) }}" min="0" class="form-input" style="max-width:6rem;">
                        <p class="form-help">Lower numbers appear first.</p>
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="form-btn-primary">{{ $league ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('admin.party-leagues.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

        <style>
            .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-main); margin-bottom:0.35rem; }
            .form-input { width:100%; padding:0.5rem 0.65rem; border:1px solid var(--border-subtle); border-radius:0.4rem; background:rgba(15,23,42,0.9); color:var(--text-main); font-size:0.95rem; }
            .form-input:focus { outline:none; border-color:var(--zanupf-gold); }
            .form-help { font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; }
            .form-btn-primary { padding:0.5rem 1.25rem; background:var(--zanupf-green); color:#fff; border:none; border-radius:0.4rem; cursor:pointer; font-weight:600; font-size:0.9rem; }
        </style>
    </div>
@endsection
