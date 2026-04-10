@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page_heading', 'Overview')
@section('kpi_articles', 'Complete')
@section('kpi_learners', '0')

@section('content')
    <div class="dash-tiles">
        @include('partials.dashboard-role-workflows')

        <a href="{{ route('constitution.home', ['doc' => 'zanupf']) }}" class="dash-tile">
            <div class="dash-tile-title">ZANU PF Constitution</div>
            <div class="dash-tile-text">
                Read and study the Constitution of ZANU PF, article by article, with youth-friendly tools.
            </div>
            <div class="dash-tile-footer">Open ZANU PF constitution</div>
        </a>

        <a href="{{ route('constitution.home', ['doc' => 'zimbabwe']) }}" class="dash-tile">
            <div class="dash-tile-title">Constitution of Zimbabwe</div>
            <div class="dash-tile-text">
                Read the Constitution of the Republic of Zimbabwe (2013), chapter by chapter.
            </div>
            <div class="dash-tile-footer">Open Zimbabwe constitution</div>
        </a>

        <a href="{{ route('constitution.home', ['doc' => 'amendment3']) }}" class="dash-tile">
            <div class="dash-tile-title">Amendment Bill No. 3</div>
            <div class="dash-tile-text">
                Review {{ config('constitution.amendment3_chapter_title') }} — memorandum and clauses (clause text is maintained in Constitution management).
            </div>
            <div class="dash-tile-footer">Open Amendment Bill</div>
        </a>

        <a href="{{ route('academy.home') }}" class="dash-tile">
            <div class="dash-tile-title">Academy</div>
            <div class="dash-tile-text">
                Access structured learning paths, lessons, and constitutional assessments.
            </div>
            <div class="dash-tile-footer">Open Academy</div>
        </a>

        <a href="{{ route('library.home') }}" class="dash-tile">
            <div class="dash-tile-title">Digital Library</div>
            <div class="dash-tile-text">
                View party documents, policy papers, and historical material for deeper context.
            </div>
            <div class="dash-tile-footer">Open library</div>
        </a>

        <a href="{{ route('dialogue.home') }}" class="dash-tile">
            <div class="dash-tile-title">Opinion & Dialogue</div>
            <div class="dash-tile-text">
                Join moderated discussions anchored to articles and key constitutional questions.
            </div>
            <div class="dash-tile-footer">Open dialogue</div>
        </a>

        <div class="dash-tile">
            <div class="dash-tile-title">Learner status (system‑wide)</div>
            <div class="dash-tile-text">
                Snapshot of current learner activity across the Academy.
            </div>
            <div class="dash-metric-row" style="margin-top:0.5rem;">
                <div>
                    <div class="dash-metric-item">
                        <span class="dash-metric-label">Academy enrolments</span>
                        <span class="dash-metric-value">
                            {{ number_format($totalEnrolments ?? 0) }}
                        </span>
                    </div>
                    <div class="dash-metric-bar">
                        @php
                            $enrolPercent = ($totalEnrolments ?? 0) > 0 ? min(100, (($completedEnrolments ?? 0) / max(1, $totalEnrolments ?? 1)) * 100) : 0;
                        @endphp
                        <div class="dash-metric-bar-fill" style="width: {{ $enrolPercent }}%;"></div>
                    </div>
                </div>
                <div>
                    <div class="dash-metric-item">
                        <span class="dash-metric-label">Assessments completed</span>
                        <span class="dash-metric-value">
                            {{ number_format($assessmentAttempts ?? 0) }}
                        </span>
                    </div>
                    <div class="dash-metric-bar">
                        <div class="dash-metric-bar-fill" style="width: 100%;"></div>
                    </div>
                </div>
                <div>
                    <div class="dash-metric-item">
                        <span class="dash-metric-label">Active learners</span>
                        <span class="dash-metric-value">
                            {{ number_format($activeLearners ?? 0) }}
                        </span>
                    </div>
                    <div class="dash-metric-bar">
                        <div class="dash-metric-bar-fill" style="width: 100%;"></div>
                    </div>
                </div>
                <div>
                    <div class="dash-metric-item">
                        <span class="dash-metric-label">Certificates issued</span>
                        <span class="status-pill status-pill--active">
                            <span class="dot"></span>
                            {{ number_format($certificatesIssued ?? 0) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('party-organs.home') }}" class="dash-tile">
            <div class="dash-tile-title">Party organs</div>
            <div class="dash-tile-text">
                Quick reference to the principal organs of the Party (Congress, Central Committee, Politburo, Leagues, and structures).
            </div>
            <div class="dash-tile-footer">View party organs</div>
        </a>

        @canAccessSection('priority_projects')
        <a href="{{ route('admin.priority-projects.index') }}" class="dash-tile">
            <div class="dash-tile-title">Priority projects</div>
            <div class="dash-tile-text">
                Create, edit, and publish current strategic programmes and projects aligned with Vision 2030.
            </div>
            <div class="dash-tile-footer">Manage priority projects</div>
        </a>
        @endcanAccessSection

        <a href="{{ route('party.home') }}" class="dash-tile">
            <div class="dash-tile-title">The Party</div>
            <div class="dash-tile-text">
                Name, legal status, flag, vision and mission of ZANU PF, drawn from Article 1 of the Party Constitution.
            </div>
            <div class="dash-tile-footer">Open The Party</div>
        </a>

        <a href="{{ url('/party') }}#presidium" class="dash-tile">
            <div class="dash-tile-title">Presidium</div>
            <div class="dash-tile-text">
                View the Presidium as captured in this system, linked to constitutional articles.
            </div>
            <div class="dash-tile-footer">View Presidium (internal)</div>
        </a>

        @canAccessSection('analytics')
            <a href="{{ route('admin.analytics.index') }}" class="dash-tile">
                <div class="dash-tile-title">Analytics & reports</div>
                <div class="dash-tile-text">
                    View high‑level metrics for members, academy, dialogue, projects, and certificates.
                </div>
                <div class="dash-tile-footer">Open analytics</div>
            </a>
        @endcanAccessSection
    </div>
@endsection

