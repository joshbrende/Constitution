@extends('layouts.dashboard')

@section('title', 'Admin documentation')
@section('page_heading', 'Documentation')

@section('content')
<style>
    .admin-doc { max-width: 1100px; margin: 0 auto; }
    .admin-doc-hero {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
        flex-wrap: wrap; margin-bottom: 1.5rem;
    }
    .admin-doc-hero h1 { font-size: 1.35rem; font-weight: 700; margin: 0 0 0.35rem 0; }
    .admin-doc-hero p { margin: 0; font-size: 0.9rem; color: var(--text-muted); max-width: 52rem; line-height: 1.55; }
    .admin-doc-ver {
        font-size: 0.72rem; font-weight: 600; padding: 0.35rem 0.65rem; border-radius: 0.45rem;
        background: #2563eb; color: #fff; white-space: nowrap;
    }
    body.theme-light .admin-doc-ver { background: #1d4ed8; }
    .admin-doc-stats {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.85rem; margin-bottom: 1.25rem;
    }
    .admin-doc-stat {
        background: var(--bg-panel); border: 1px solid var(--border-subtle); border-radius: 0.75rem;
        padding: 1rem 0.85rem; text-align: center;
    }
    .admin-doc-stat-icon { font-size: 1.25rem; margin-bottom: 0.35rem; opacity: 0.9; }
    .admin-doc-stat-num { font-size: 1.45rem; font-weight: 700; color: var(--zanupf-gold); }
    .admin-doc-stat-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-top: 0.25rem; }
    .admin-doc-stack {
        background: var(--bg-panel); border: 1px solid var(--border-subtle); border-radius: 0.85rem;
        padding: 1rem 1.1rem; margin-bottom: 1.5rem;
    }
    .admin-doc-stack h2 { font-size: 0.85rem; font-weight: 600; margin: 0 0 0.65rem 0; }
    .admin-doc-pills { display: flex; flex-wrap: wrap; gap: 0.45rem; }
    .admin-doc-pill {
        font-size: 0.75rem; padding: 0.28rem 0.65rem; border-radius: 999px;
        background: rgba(148,163,184,0.15); border: 1px solid var(--border-subtle); color: var(--text-muted);
    }
    body.theme-light .admin-doc-pill { background: #f3f4f6; color: #475569; }
    .admin-doc-toc {
        background: var(--bg-panel); border: 1px solid var(--border-subtle); border-radius: 0.85rem;
        padding: 0.9rem 1rem; margin-bottom: 1.5rem; font-size: 0.82rem;
    }
    .admin-doc-toc strong { display: block; margin-bottom: 0.5rem; font-size: 0.8rem; }
    .admin-doc-toc ul { margin: 0; padding-left: 1.1rem; color: var(--text-muted); line-height: 1.7; }
    .admin-doc-toc a { color: #60a5fa; text-decoration: none; }
    .admin-doc-toc a:hover { text-decoration: underline; }
    body.theme-light .admin-doc-toc a { color: #2563eb; }
    .admin-doc-section {
        margin-bottom: 1.75rem; scroll-margin-top: 1rem;
    }
    .admin-doc-section h2 {
        font-size: 1.05rem; font-weight: 600; margin: 0 0 0.6rem 0;
        padding-bottom: 0.35rem; border-bottom: 1px solid var(--border-subtle);
    }
    .admin-doc-section p, .admin-doc-section li { font-size: 0.88rem; line-height: 1.6; color: var(--text-muted); }
    .admin-doc-section ul { margin: 0.4rem 0 0 1.1rem; }
    .admin-doc-table-wrap { overflow-x: auto; border-radius: 0.65rem; border: 1px solid var(--border-subtle); }
    table.admin-doc-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
    table.admin-doc-table th, table.admin-doc-table td {
        padding: 0.55rem 0.65rem; text-align: left; border-bottom: 1px solid var(--border-subtle); vertical-align: top;
    }
    table.admin-doc-table th { font-weight: 600; color: var(--text-muted); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; background: rgba(15,23,42,0.5); }
    body.theme-light table.admin-doc-table th { background: #f1f5f9; }
    table.admin-doc-table tr:last-child td { border-bottom: none; }
    .admin-doc-badge-yes { color: #4ade80; font-size: 0.72rem; font-weight: 600; }
    .admin-doc-badge-no { color: var(--text-muted); font-size: 0.72rem; }
    .admin-doc-callout {
        padding: 0.75rem 1rem; border-radius: 0.65rem; border: 1px solid rgba(59,130,246,0.35);
        background: rgba(59,130,246,0.08); font-size: 0.84rem; line-height: 1.55; margin-top: 0.75rem;
    }
    body.theme-light .admin-doc-callout { background: #eff6ff; border-color: #bfdbfe; color: #1e3a5f; }
</style>

<div class="admin-doc">
    <div class="admin-doc-hero">
        <div>
            <h1>Introduction</h1>
            <p>
                This console is the <strong style="color:var(--text-main)">ZANU PF Constitution platform</strong> administration and member experience:
                constitution readers, academy, digital library, party content, dialogue, certificates, and reporting.
                Use the sidebar to switch between <em>reading</em> content and <em>managing</em> it. What you see under <strong>Administration</strong>
                depends on your assigned roles (see Settings for yours).
            </p>
        </div>
        <span class="admin-doc-ver">v{{ $docVersion }}</span>
    </div>

    <div class="admin-doc-stats">
        <div class="admin-doc-stat">
            <div class="admin-doc-stat-icon" aria-hidden="true">
                <x-icons.workflow-icon key="document.text" />
            </div>
            <div class="admin-doc-stat-num">{{ number_format($stats['sections']) }}</div>
            <div class="admin-doc-stat-label">Constitution sections</div>
        </div>
        <div class="admin-doc-stat">
            <div class="admin-doc-stat-icon" aria-hidden="true">
                <x-icons.workflow-icon key="academy.course" />
            </div>
            <div class="admin-doc-stat-num">{{ number_format($stats['courses']) }}</div>
            <div class="admin-doc-stat-label">Academy courses</div>
        </div>
        <div class="admin-doc-stat">
            <div class="admin-doc-stat-icon" aria-hidden="true">
                <x-icons.workflow-icon key="library.document" />
            </div>
            <div class="admin-doc-stat-num">{{ number_format($stats['library_docs']) }}</div>
            <div class="admin-doc-stat-label">Library documents</div>
        </div>
        <div class="admin-doc-stat">
            <div class="admin-doc-stat-icon" aria-hidden="true">
                <x-icons.workflow-icon key="role.fallback" />
            </div>
            <div class="admin-doc-stat-num">{{ number_format($stats['users']) }}</div>
            <div class="admin-doc-stat-label">User accounts</div>
        </div>
    </div>

    <div class="admin-doc-stack">
        <h2>Platform stack</h2>
        <div class="admin-doc-pills">
            <span class="admin-doc-pill">Laravel {{ app()->version() }}</span>
            <span class="admin-doc-pill">PHP {{ PHP_VERSION }}</span>
            <span class="admin-doc-pill">Livewire</span>
            <span class="admin-doc-pill">Blade</span>
            <span class="admin-doc-pill">Sanctum (API)</span>
            <span class="admin-doc-pill">MySQL</span>
            <span class="admin-doc-pill">Redis (sessions / cache / queue)</span>
            <span class="admin-doc-pill">TCPDF / FPDI (certificates)</span>
        </div>
    </div>

    <nav class="admin-doc-toc" aria-label="On this page">
        <strong>On this page</strong>
        <ul>
            <li><a href="#navigating">Navigating the sidebar</a></li>
            <li><a href="#modules">Administration modules</a></li>
            <li><a href="#rbac">Roles and access</a></li>
            <li><a href="#workflows">Key workflows</a></li>
            <li><a href="#mobile-api">Mobile app and API</a></li>
            <li><a href="#canonical-docs">Canonical manual (repository)</a></li>
        </ul>
    </nav>

    <section class="admin-doc-section" id="navigating">
        <h2>Navigating the sidebar</h2>
        <p>The sidebar is grouped as follows:</p>
        <ul>
            <li><strong style="color:var(--text-main)">Main</strong> — Dashboard overview.</li>
            <li><strong style="color:var(--text-main)">Constitution &amp; Learning</strong> — Read ZANU PF Constitution, Zimbabwe Constitution, Amendment Bill No. 3, Academy courses, Digital Library, Party, Party Organs, and Opinion &amp; Dialogue (member-facing).</li>
            <li><strong style="color:var(--text-main)">Administration</strong> — Appears when you have at least one admin role. Each link opens the corresponding CMS or oversight screen.</li>
            <li><strong style="color:var(--text-main)">Help &amp; resources</strong> — This documentation, quick Help, and personal Settings.</li>
        </ul>
        <p>Use the <strong style="color:var(--text-main)">Theme</strong> control in the top bar to switch light/dark; your choice is stored in this browser.</p>
    </section>

    <section class="admin-doc-section" id="modules">
        <h2>Administration modules</h2>
        <p>Below is a professional map from sidebar labels to responsibilities. <span class="admin-doc-badge-yes">Your access</span> means your current roles include that module.</p>
        <div class="admin-doc-table-wrap">
            <table class="admin-doc-table">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Your access</th>
                        <th>Summary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($modules as $row)
                        @php
                            $can = $row['section'] === null || !empty($accessibleSections[$row['section']]);
                        @endphp
                        <tr>
                            <td>
                                @if (!empty($row['route']))
                                    <a href="{{ route($row['route']) }}" style="color:#60a5fa;font-weight:600;text-decoration:none;">{{ $row['label'] }}</a>
                                @else
                                    <strong style="color:var(--text-main);">{{ $row['label'] }}</strong>
                                @endif
                            </td>
                            <td>
                                @if ($can)
                                    <span class="admin-doc-badge-yes">Yes</span>
                                @else
                                    <span class="admin-doc-badge-no">—</span>
                                @endif
                            </td>
                            <td>{{ $row['summary'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-doc-section" id="rbac">
        <h2>Roles and access</h2>
        <p>
            Access to each administration area is controlled in <code style="font-size:0.85em;">config/admin.php</code>, which maps <em>sections</em> to role slugs.
            Special roles (e.g. Presidium) may gate actions such as approving constitution versions. If a menu item is missing, your account does not include a role for that section.
        </p>
        <p>
            Only <strong style="color:var(--text-main)">system administrators</strong> should use <strong>Roles</strong> to change who can do what.
        </p>
    </section>

    <section class="admin-doc-section" id="workflows">
        <h2>Key workflows</h2>
        <ul>
            <li><strong style="color:var(--text-main)">Constitution publishing</strong> — Editors work on section <em>versions</em>; submitted versions move to review; Presidium approves or rejects. Approved content drives the public readers and API.</li>
            <li><strong style="color:var(--text-main)">Official Amendment PDF</strong> — Upload the gazetted or cabinet PDF from Manage Constitution so the mobile app can offer an &ldquo;official PDF&rdquo; action when the file is present.</li>
            <li><strong style="color:var(--text-main)">Academy</strong> — Build courses and assessments, then publish. Mandatory courses and badges are respected in the app and analytics.</li>
            <li><strong style="color:var(--text-main)">Certificates</strong> — Revoking or reinstating a certificate writes to audit logs; members verify certificates on the public verification page.</li>
            <li><strong style="color:var(--text-main)">Dialogue</strong> — Moderators manage channels and threads, pin guidance, and remove policy-violating messages.</li>
        </ul>
    </section>

    <section class="admin-doc-section" id="mobile-api">
        <h2>Mobile app and API</h2>
        <p>
            The mobile apps use the JSON API under <code style="font-size:0.85em;">/api/v1</code> with Sanctum-style access and refresh tokens.
            Content you publish here (constitution, library visibility, academy, dialogue, certificates) is what authenticated app users see, subject to the same rules as the web experience.
        </p>
    </section>

    <section class="admin-doc-section" id="canonical-docs">
        <h2>Canonical manual (repository)</h2>
        <p>
            For deep chapters (API reference, ops, security), the engineering team maintains <strong style="color:var(--text-main)">docs/backend-manual/</strong> in the project repository
            (numbered files 01–40 and appendices). This in-app page is the concise map for day-to-day admin navigation; the manual is the full reference for developers and compliance.
        </p>
        <div class="admin-doc-callout">
            Tip: Use <a href="{{ route('admin.guide.help') }}">Help</a> for shortcuts and common questions, and <a href="{{ route('admin.guide.settings') }}">Settings</a> for your account and theme.
        </div>
    </section>
</div>
@endsection
