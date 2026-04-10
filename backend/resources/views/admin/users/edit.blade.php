@extends('layouts.dashboard')

@section('title', 'Edit User – ' . $user->name)
@section('page_heading', 'Edit User')

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
                    <div class="dash-panel-title">{{ $user->name }} {{ $user->surname }}</div>
                    <div class="dash-panel-subtitle">{{ $user->email }} · Assign roles for backend administration.</div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Users</a>
            </div>

            <div style="margin-bottom:1.25rem;padding:1rem;border-radius:0.5rem;border:1px solid var(--border-subtle);background:rgba(250,204,21,0.06);">
                <div style="font-weight:700;font-size:0.9rem;margin-bottom:0.5rem;color:var(--zanupf-gold);">Role assignment guide (System Administrator)</div>
                <p style="margin:0 0 0.75rem;font-size:0.85rem;line-height:1.5;color:var(--text-muted);">This page controls which admin areas this user can access. Follow these steps:</p>
                <ol style="margin:0;padding-left:1.2rem;font-size:0.85rem;line-height:1.7;color:var(--text-muted);">
                    <li><strong>Check or uncheck roles</strong> — Each checkbox corresponds to one role. Check the roles this user should have (e.g. Academy Manager, Content Editor, User Manager). Uncheck any role you want to remove. The user will lose access to that role’s sections after you save.</li>
                    <li><strong>Click “Update roles”</strong> — Changes are not saved until you click the green button. There is no automatic save.</li>
                    <li><strong>Effect is immediate</strong> — Once saved, the user gains or loses access on their next page load. No re-login required.</li>
                    <li><strong>What each role does</strong> — Academy Manager = courses only; User Manager = Users & Members; Presidium = approve constitutional amendments; Content Editor = edit constitution and library; Analytics Viewer = read-only reports; Audit Viewer = read-only audit logs.</li>
                    <li><strong>Restricted roles</strong> — Only a System Administrator can assign <strong>System Admin</strong> or <strong>Presidium</strong>. User Managers cannot assign these two roles.</li>
                </ol>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div style="display:grid;gap:1rem;max-width:40rem;">
                    <div>
                        <label class="form-label">Roles</label>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:0.5rem;">
                            @foreach ($roles as $role)
                                <label style="display:flex;align-items:center;gap:0.5rem;padding:0.4rem;border-radius:0.3rem;background:rgba(15,23,42,0.5);">
                                    <input
                                        type="checkbox"
                                        name="roles[]"
                                        value="{{ $role->id }}"
                                        {{ $user->roles->contains('id', $role->id) ? 'checked' : '' }}
                                    >
                                    <span>{{ $role->name }}</span>
                                    <span style="font-size:0.75rem;color:var(--text-muted);">({{ $role->slug }})</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="form-help">See the instruction box above for the complete flow.</p>
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="form-btn-primary">Update roles</button>
                        <a href="{{ route('admin.users.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

        <style>
            .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-main); margin-bottom:0.35rem; }
            .form-help { font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; }
            .form-btn-primary { padding:0.5rem 1.25rem; background:var(--zanupf-green); color:#fff; border:none; border-radius:0.4rem; cursor:pointer; font-weight:600; font-size:0.9rem; }
        </style>
    </div>
@endsection
