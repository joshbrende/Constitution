@extends('layouts.dashboard')

@section('title', 'Settings')
@section('page_heading', 'Settings')

@section('content')
<style>
    .set-grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); gap: 1.25rem; max-width: 960px; align-items: start; }
    @media (max-width: 800px) { .set-grid { grid-template-columns: 1fr; } }
    .set-panel {
        background: var(--bg-panel); border: 1px solid var(--border-subtle); border-radius: 0.85rem;
        padding: 1.1rem 1.15rem; font-size: 0.88rem;
    }
    .set-panel h2 { font-size: 0.95rem; font-weight: 600; margin: 0 0 0.75rem 0; padding-bottom: 0.4rem; border-bottom: 1px solid var(--border-subtle); }
    .set-row { display: flex; justify-content: space-between; gap: 0.75rem; padding: 0.4rem 0; border-bottom: 1px solid rgba(148,163,184,0.12); }
    .set-row:last-child { border-bottom: none; }
    .set-label { color: var(--text-muted); flex: 0 0 38%; }
    .set-value { color: var(--text-main); font-weight: 500; text-align: right; word-break: break-word; }
    .set-pill {
        display: inline-block; font-size: 0.72rem; padding: 0.2rem 0.5rem; border-radius: 999px;
        background: rgba(21,128,61,0.2); color: #86efac; margin: 0.15rem 0.15rem 0 0;
    }
    body.theme-light .set-pill { background: #dcfce7; color: #166534; }
    .set-panel p { color: var(--text-muted); line-height: 1.55; margin: 0 0 0.65rem 0; }
    .set-panel a { color: #60a5fa; text-decoration: none; }
    .set-panel a:hover { text-decoration: underline; }
    body.theme-light .set-panel a { color: #2563eb; }
</style>

<div class="set-grid">
    <div class="set-panel">
        <h2>Your profile</h2>
        <div class="set-row">
            <span class="set-label">Name</span>
            <span class="set-value">{{ $user->name }} {{ $user->surname }}</span>
        </div>
        <div class="set-row">
            <span class="set-label">Email</span>
            <span class="set-value">{{ $user->email }}</span>
        </div>
        @if ($user->wing)
            <div class="set-row">
                <span class="set-label">Wing</span>
                <span class="set-value">{{ $user->wing }}</span>
            </div>
        @endif
        <div class="set-row">
            <span class="set-label">Roles</span>
            <span class="set-value" style="text-align:right;">
                @forelse ($user->roles as $role)
                    <span class="set-pill">{{ $role->name ?? $role->slug }}</span>
                @empty
                    <span style="color:var(--text-muted);font-weight:400;">No roles assigned</span>
                @endforelse
            </span>
        </div>
        <p style="margin-top:1rem;">
            Profile edits for your organisation are usually performed by a <strong style="color:var(--text-main)">user manager</strong> from Administration → Users.
            If your details are wrong, contact them rather than sharing passwords.
        </p>
    </div>

    <div class="set-panel">
        <h2>Security</h2>
        <p>Use a strong, unique password. To change it, sign out and reset from the login screen, or use the link below while signed in (you will receive an email).</p>
        <p><a href="{{ route('password.request') }}">Request password reset email</a></p>
        @if ($user && $user->hasRole('system_admin'))
            <h2 style="margin-top:1.25rem;">Platform</h2>
            <p>Update platform-wide defaults (branding, legal links, operational toggles).</p>
            <p><a href="{{ route('admin.platform-settings.edit') }}">Open platform settings</a></p>
        @endif
        <h2 style="margin-top:1.25rem;">Appearance</h2>
        <p>The <strong style="color:var(--text-main)">Theme</strong> toggle in the top bar switches light and dark mode. The choice is stored in this browser only.</p>
        <h2 style="margin-top:1.25rem;">Session</h2>
        <p>Use <strong style="color:var(--text-main)">Logout</strong> in the sidebar when finished on a shared computer.</p>
    </div>
</div>
@endsection
