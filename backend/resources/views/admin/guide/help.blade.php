@extends('layouts.dashboard')

@section('title', 'Help')
@section('page_heading', 'Help')

@section('content')
<style>
    .help-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; max-width: 1100px; }
    .help-card {
        background: var(--bg-panel); border: 1px solid var(--border-subtle); border-radius: 0.85rem;
        padding: 1rem 1.1rem; font-size: 0.86rem; line-height: 1.55;
    }
    .help-card h2 { font-size: 0.95rem; font-weight: 600; margin: 0 0 0.5rem 0; color: var(--text-main); }
    .help-card p { margin: 0 0 0.5rem 0; color: var(--text-muted); }
    .help-card ul { margin: 0.35rem 0 0 1rem; color: var(--text-muted); }
    .help-card a { color: #60a5fa; text-decoration: none; }
    .help-card a:hover { text-decoration: underline; }
    body.theme-light .help-card a { color: #2563eb; }
    .help-steps { max-width: 1100px; margin-top: 1.25rem; }
    .help-steps h2 { font-size: 1rem; font-weight: 600; margin: 0 0 0.65rem 0; }
    .help-steps ol { margin: 0; padding-left: 1.2rem; color: var(--text-muted); font-size: 0.88rem; line-height: 1.65; }
    .help-steps li { margin-bottom: 0.45rem; }
</style>

<div class="help-grid">
    <div class="help-card">
        <h2>Start here</h2>
        <p>New to the console? Read the structured overview and live counts on the documentation page.</p>
        <p><a href="{{ route('admin.guide.documentation') }}">Open Documentation</a></p>
    </div>
    <div class="help-card">
        <h2>Your access</h2>
        @if (count($accessibleSections))
            <p>You currently have administration access to these sections:</p>
            <ul>
                @foreach ($accessibleSections as $slug)
                    <li><code style="font-size:0.8em;">{{ $slug }}</code></li>
                @endforeach
            </ul>
        @else
            <p>No section-specific roles were detected. If you expect admin menus, contact a system administrator to assign roles.</p>
        @endif
    </div>
    <div class="help-card">
        <h2>Account &amp; appearance</h2>
        <p>Change how the console looks and review your profile.</p>
        <p><a href="{{ route('admin.guide.settings') }}">Open Settings</a></p>
    </div>
    <div class="help-card">
        <h2>Password &amp; login</h2>
        <p>If you forgot your password, sign out and use the reset flow from the login page, or ask an administrator to verify your account.</p>
        <p><a href="{{ route('password.request') }}">Forgot password (web)</a></p>
    </div>
</div>

<div class="help-steps">
    <h2>Common tasks</h2>
    <ol>
        <li><strong style="color:var(--text-main)">Publish constitution changes</strong> — Administration → Manage Constitution → navigate Part → Chapter → Section → create or edit a version → submit for approval → Presidium approves.</li>
        <li><strong style="color:var(--text-main)">Add a library document</strong> — Administration → Manage Digital Library → Documents → create; set visibility for guests, members, or leadership as appropriate.</li>
        <li><strong style="color:var(--text-main)">Publish a course</strong> — Administration → Manage Academy → edit course status and content, then verify assessments in the academy admin guides.</li>
        <li><strong style="color:var(--text-main)">Moderate dialogue</strong> — Administration → Dialogue → pick a channel, open threads, pin official guidance or remove messages that breach policy.</li>
        <li><strong style="color:var(--text-main)">Export analytics</strong> — Administration → Analytics &amp; reports → use CSV exports where your role allows.</li>
        <li><strong style="color:var(--text-main)">Certificate issue</strong> — Use Academy completion flows; to revoke or reinstate, Administration → Certificates (audit entries are recorded).</li>
    </ol>
</div>

<div class="help-card" style="max-width:1100px;margin-top:1.25rem;">
    <h2>Operational support</h2>
    <p style="margin-bottom:0;">
        For infrastructure, backups, queues, and production checks, technical staff should follow the repository <strong>docs/</strong> folder (for example OPS runbook and environment guides).
        This Help page is aimed at <strong>administrators and content teams</strong> using the web console.
    </p>
</div>
@endsection
