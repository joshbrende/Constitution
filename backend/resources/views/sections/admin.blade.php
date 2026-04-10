@extends('layouts.dashboard')

@section('title', 'Administration & Oversight')
@section('page_heading', 'Administration')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Admin & oversight</div>
                    <div class="dash-panel-subtitle">
                        Manage constitution, members, academy, and more. Access depends on your assigned roles.
                    </div>
                </div>
                <span class="dash-tag">Admin only</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:1rem;">
                <a href="{{ route('admin.guide.documentation') }}" class="dash-tile">
                    <div class="dash-tile-title">Documentation</div>
                    <div class="dash-tile-text">
                        Introduction, module map, workflows, and stack — written for administrators using this console.
                    </div>
                    <div class="dash-tile-footer">Open documentation</div>
                </a>
                <a href="{{ route('admin.guide.help') }}" class="dash-tile">
                    <div class="dash-tile-title">Help</div>
                    <div class="dash-tile-text">
                        Shortcuts, common tasks, and links to reset your password or review your access.
                    </div>
                    <div class="dash-tile-footer">Open help</div>
                </a>
                <a href="{{ route('admin.guide.settings') }}" class="dash-tile">
                    <div class="dash-tile-title">Settings</div>
                    <div class="dash-tile-text">
                        Your profile, assigned roles, security and appearance notes for this browser.
                    </div>
                    <div class="dash-tile-footer">Open settings</div>
                </a>
                @canAccessSection('constitution')
                <a href="{{ route('admin.constitution.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Constitution</div>
                    <div class="dash-tile-text">
                        Edit Parts, Chapters, Sections. Create amendments; submit for review; Presidium approves.
                    </div>
                    <div class="dash-tile-footer">Manage constitution</div>
                </a>
                @endcanAccessSection

                @canAccessSection('academy')
                <a href="{{ route('admin.academy.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Academy</div>
                    <div class="dash-tile-text">
                        Manage courses, modules, lessons. Create, edit, publish, or archive courses.
                    </div>
                    <div class="dash-tile-footer">Manage courses</div>
                </a>
                @endcanAccessSection

                @canAccessSection('library')
                <a href="{{ route('admin.library.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Digital Library</div>
                    <div class="dash-tile-text">
                        Manage party documents, policy papers, and historical material.
                    </div>
                    <div class="dash-tile-footer">Manage library</div>
                </a>
                @endcanAccessSection

                @canAccessSection('certificates')
                <a href="{{ route('admin.certificates.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Certificates</div>
                    <div class="dash-tile-text">
                        Verify membership certificates by unique number. Search and prevent duplication.
                    </div>
                    <div class="dash-tile-footer">Verify certificates</div>
                </a>
                @endcanAccessSection

                @canAccessSection('members')
                <a href="{{ route('admin.members.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Members</div>
                    <div class="dash-tile-text">
                        View and manage member accounts, roles, and branches.
                    </div>
                    <div class="dash-tile-footer">Manage members</div>
                </a>
                @endcanAccessSection

                @canAccessSection('party')
                <a href="{{ route('admin.party.index') }}" class="dash-tile">
                    <div class="dash-tile-title">The Party</div>
                    <div class="dash-tile-text">
                        Manage the Party landing content and its related constitution sections.
                    </div>
                    <div class="dash-tile-footer">Manage the party</div>
                </a>
                @endcanAccessSection

                @canAccessSection('party_organs')
                <a href="{{ route('admin.party-organs.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Party Organs</div>
                    <div class="dash-tile-text">
                        Edit Congress, Central Committee, Politburo, Leagues, and other principal organs. Shown in the app.
                    </div>
                    <div class="dash-tile-footer">Manage party organs</div>
                </a>
                @endcanAccessSection

                @canAccessSection('party_leagues')
                <a href="{{ route('admin.party-leagues.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Party Leagues</div>
                    <div class="dash-tile-text">
                        Manage leagues shown under the Party organs and navigation in the app.
                    </div>
                    <div class="dash-tile-footer">Manage party leagues</div>
                </a>
                @endcanAccessSection

                @canAccessSection('presidium')
                <a href="{{ route('admin.presidium.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Presidium</div>
                    <div class="dash-tile-text">
                        Manage the President, Vice Presidents, National Chairperson and Secretary-General list.
                    </div>
                    <div class="dash-tile-footer">Manage presidium</div>
                </a>
                @endcanAccessSection

                @canAccessSection('dialogue')
                <a href="{{ route('admin.dialogue.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Dialogue</div>
                    <div class="dash-tile-text">
                        Curate channels and topics, and moderate Presidium and League conversations.
                    </div>
                    <div class="dash-tile-footer">Manage dialogue</div>
                </a>
                @endcanAccessSection

                @canAccessSection('priority_projects')
                <a href="{{ route('admin.priority-projects.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Priority projects</div>
                    <div class="dash-tile-text">
                        Create, edit, and publish Vision 2030-aligned projects surfaced in the app.
                    </div>
                    <div class="dash-tile-footer">Manage priority projects</div>
                </a>
                @endcanAccessSection

                @canAccessSection('analytics')
                <a href="{{ route('admin.analytics.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Analytics & reports</div>
                    <div class="dash-tile-text">
                        View key metrics on members, academy, dialogue, projects, and certificates for stakeholders.
                    </div>
                    <div class="dash-tile-footer">View analytics</div>
                </a>
                @endcanAccessSection

                @canAccessSection('home_banners')
                <a href="{{ route('admin.home-banners.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Home banners</div>
                    <div class="dash-tile-text">
                        Curate promotional banners for the mobile Overview carousel (Vision 2030, membership, campaigns).
                    </div>
                    <div class="dash-tile-footer">Manage home banners</div>
                </a>
                @endcanAccessSection

                @canAccessSection('static_pages')
                <a href="{{ route('admin.static-pages.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Static pages</div>
                    <div class="dash-tile-text">
                        Edit Help, Terms &amp; Conditions, Privacy policy and other static information for the app.
                    </div>
                    <div class="dash-tile-footer">Manage static pages</div>
                </a>
                @endcanAccessSection

                @canAccessSection('audit_logs')
                <a href="{{ route('admin.audit-logs.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Audit logs</div>
                    <div class="dash-tile-text">
                        Read-only view of authentication, academy, and certificate actions for compliance.
                    </div>
                    <div class="dash-tile-footer">View audit logs</div>
                </a>
                @endcanAccessSection

                @canAccessSection('roles')
                <a href="{{ route('admin.roles.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Roles</div>
                    <div class="dash-tile-text">
                        Create and manage roles. Assign roles to users from the Users page.
                    </div>
                    <div class="dash-tile-footer">Manage roles</div>
                </a>
                @endcanAccessSection

                @canAccessSection('users')
                <a href="{{ route('admin.users.index') }}" class="dash-tile">
                    <div class="dash-tile-title">Users</div>
                    <div class="dash-tile-text">
                        View users and assign roles for backend administration.
                    </div>
                    <div class="dash-tile-footer">Manage users</div>
                </a>
                @endcanAccessSection
            </div>

            <p class="dash-panel-subtitle" style="margin-top:1.5rem;">
                Export-ready data can be pulled from Members, Certificates, Academy, Dialogue, and Priority projects.
            </p>
        </section>
    </div>
@endsection

