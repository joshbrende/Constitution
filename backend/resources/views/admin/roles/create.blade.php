@extends('layouts.dashboard')

@section('title', 'Create Role')
@section('page_heading', 'Create Role')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">New role</div>
                    <div class="dash-panel-subtitle">Add section access in config/admin.php after creating the role.</div>
                </div>
                <a href="{{ route('admin.roles.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Roles</a>
            </div>

            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf
                <div style="display:grid;gap:1rem;max-width:40rem;">
                    <div>
                        <label for="name" class="form-label">Name <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="e.g. Academy Manager">
                    </div>
                    <div>
                        <label for="slug" class="form-label">Slug</label>
                        <input id="slug" type="text" name="slug" value="{{ old('slug') }}" placeholder="auto from name" class="form-input">
                        <p class="form-help">Used in code and config; leave blank to auto-generate (e.g. academy_manager).</p>
                    </div>
                    <div>
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="2" class="form-input" placeholder="e.g. Manages courses, assessments, and enrolments.">{{ old('description') }}</textarea>
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="form-btn-primary">Create</button>
                        <a href="{{ route('admin.roles.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
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
