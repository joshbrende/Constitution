@extends('layouts.dashboard')

@section('title', 'Analytics & reports')
@section('page_heading', 'Analytics & reports')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2; min-width: 0;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Key metrics</div>
                    <div class="dash-panel-subtitle">
                        High‑level view for Presidium, stakeholders, and reporting decks.
                    </div>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-top:1rem;">
                <div class="dash-stat">
                    <div class="dash-stat-label">Registered members</div>
                    <div class="dash-stat-value">{{ number_format($totalMembers) }}</div>
                    <div class="dash-stat-sub">
                        +{{ number_format($newMembersLast30) }} in last 30 days
                        @if(isset($membersImprovement))
                            <span class="dash-stat-badge dash-stat-badge--{{ $membersImprovement >= 0 ? 'pos' : 'neg' }}">
                                {{ $membersImprovement >= 0 ? '↑' : '↓' }} {{ number_format(abs($membersImprovement), 1) }}%
                            </span>
                        @endif
                    </div>
                </div>

                <div class="dash-stat">
                    <div class="dash-stat-label">Academy courses</div>
                    <div class="dash-stat-value">{{ number_format($totalCourses) }}</div>
                    <div class="dash-stat-sub">
                        {{ number_format($publishedCourses) }} published
                    </div>
                </div>

                <div class="dash-stat">
                    <div class="dash-stat-label">Certificates issued</div>
                    <div class="dash-stat-value">{{ number_format($totalCertificates) }}</div>
                    <div class="dash-stat-sub">
                        +{{ number_format($certificatesLast30) }} in last 30 days
                        @if(isset($certificatesImprovement))
                            <span class="dash-stat-badge dash-stat-badge--{{ $certificatesImprovement >= 0 ? 'pos' : 'neg' }}">
                                {{ $certificatesImprovement >= 0 ? '↑' : '↓' }} {{ number_format(abs($certificatesImprovement), 1) }}%
                            </span>
                        @endif
                    </div>
                </div>

                <div class="dash-stat">
                    <div class="dash-stat-label">Dialogue messages</div>
                    <div class="dash-stat-value">{{ number_format($totalDialogueMessages) }}</div>
                    <div class="dash-stat-sub">
                        +{{ number_format($dialogueMessagesLast30) }} in last 30 days
                    </div>
                </div>

                <div class="dash-stat">
                    <div class="dash-stat-label">Priority projects</div>
                    <div class="dash-stat-value">{{ number_format($publishedProjects) }}</div>
                    <div class="dash-stat-sub">
                        {{ number_format($totalProjectLikes) }} total likes
                    </div>
                </div>

                @if($membershipCourse)
                    <div class="dash-stat">
                        <div class="dash-stat-label">Membership course enrolments</div>
                        <div class="dash-stat-value">{{ number_format($membershipCourseEnrolments) }}</div>
                        <div class="dash-stat-sub">
                            {{ number_format($membershipCourseCompletions) }} completions
                            @if(isset($completionsImprovement))
                                <span class="dash-stat-badge dash-stat-badge--{{ $completionsImprovement >= 0 ? 'pos' : 'neg' }}">
                                    {{ $completionsImprovement >= 0 ? '↑' : '↓' }} {{ number_format(abs($completionsImprovement), 1) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="dash-stat">
                    <div class="dash-stat-label">Assessment attempts</div>
                    <div class="dash-stat-value">{{ number_format($totalAttempts) }}</div>
                    <div class="dash-stat-sub">
                        @if($passRate !== null)
                            {{ number_format($passRate, 1) }}% pass rate · avg {{ number_format($avgScore ?? 0, 1) }}%
                        @else
                            No graded attempts yet
                        @endif
                    </div>
                </div>
            </div>

            <h3 style="font-size:0.9rem;font-weight:600;margin:1.5rem 0 0.5rem;">Academy</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-top:0.5rem;">
                <div class="dash-stat">
                    <div class="dash-stat-label">Average completion %</div>
                    <div class="dash-stat-value">
                        {{ $avgCompletionPct !== null ? number_format($avgCompletionPct, 1) . '%' : '—' }}
                    </div>
                    <div class="dash-stat-sub">
                        {{ number_format($completedEnrolments ?? 0) }} / {{ number_format($totalEnrolments ?? 0) }} completed
                        @if(isset($completionsImprovement))
                            <span class="dash-stat-badge dash-stat-badge--{{ $completionsImprovement >= 0 ? 'pos' : 'neg' }}">
                                {{ $completionsImprovement >= 0 ? '↑' : '↓' }} {{ number_format(abs($completionsImprovement), 1) }}% completions vs prior 30d
                            </span>
                        @endif
                    </div>
                </div>
                <div class="dash-stat">
                    <div class="dash-stat-label">Total enrolments</div>
                    <div class="dash-stat-value">{{ number_format($totalEnrolments ?? 0) }}</div>
                    <div class="dash-stat-sub">All Academy courses</div>
                </div>
                <div class="dash-stat">
                    <div class="dash-stat-label">Assessments passed</div>
                    <div class="dash-stat-value">{{ number_format($passedAttempts ?? 0) }}</div>
                    <div class="dash-stat-sub">{{ number_format($failedAttempts ?? 0) }} failed</div>
                </div>
                <div class="dash-stat">
                    <div class="dash-stat-label">Inactive users</div>
                    <div class="dash-stat-value">{{ number_format($inactiveUsersCount ?? 0) }}</div>
                    <div class="dash-stat-sub">No activity in last 30 days</div>
                </div>
            </div>

            <h3 style="font-size:0.9rem;font-weight:600;margin:1.5rem 0 0.5rem;">Academy by province</h3>
            <p class="dash-panel-subtitle" style="margin-bottom:0.5rem;">Registered members (passed assessment) by province. Top provinces by assessments passed.</p>
            <div style="overflow-x:auto;">
                <table class="dash-table" style="margin-top:0;">
                    <thead>
                        <tr>
                            <th>Province</th>
                            <th style="text-align:right;">Members</th>
                            <th style="text-align:right;">Passed</th>
                            <th style="text-align:right;">Attempts</th>
                            <th style="text-align:right;">Pass rate</th>
                            <th style="text-align:right;">Enrolments</th>
                            <th style="text-align:right;">Certificates</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($provinceStats ?? [] as $s)
                            @php
                                $rankRow = ($provinceLeaderboard ?? collect())->first(fn ($r) => $r['province']->id === $s['province']->id);
                                $rank = $rankRow['rank'] ?? null;
                            @endphp
                            <tr>
                                <td>
                                    @if($rank)
                                        <span class="dash-stat-badge dash-stat-badge--pos" style="margin-right:0.5rem;">#{{ $rank }}</span>
                                    @endif
                                    {{ $s['province']->name }}
                                </td>
                                <td style="text-align:right;">{{ number_format($s['members']) }}</td>
                                <td style="text-align:right;">{{ number_format($s['passed']) }}</td>
                                <td style="text-align:right;">{{ number_format($s['attempts']) }}</td>
                                <td style="text-align:right;">{{ $s['pass_rate'] !== null ? number_format($s['pass_rate'], 1) . '%' : '—' }}</td>
                                <td style="text-align:right;">{{ number_format($s['enrolments']) }}</td>
                                <td style="text-align:right;">{{ number_format($s['certificates']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(($provinceStats ?? collect())->isEmpty())
                <p class="dash-panel-subtitle">No province data. Ensure users have province set in their profile.</p>
            @endif

            <hr style="border-color:var(--border-subtle);margin:1.5rem 0;">

            <h3 style="font-size:0.9rem;font-weight:600;margin-bottom:0.5rem;">Certificates issued – last 6 months</h3>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                @forelse($certificatesByMonth as $ym => $count)
                    <div style="min-width:120px;padding:0.5rem 0.75rem;border-radius:0.4rem;background:rgba(15,23,42,0.9);border:1px solid var(--border-subtle);">
                        <div style="font-size:0.8rem;color:var(--text-muted);">
                            {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $ym)->format('M Y') }}
                        </div>
                        <div style="font-size:1rem;font-weight:600;">
                            {{ number_format($count) }}
                        </div>
                    </div>
                @empty
                    <p class="dash-panel-subtitle">No certificates issued yet.</p>
                @endforelse
            </div>

            <h3 style="font-size:0.9rem;font-weight:600;margin:1.5rem 0 0.5rem;">Assessment attempts – last 6 months</h3>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                @forelse($attemptsByMonth as $ym => $count)
                    <div style="min-width:120px;padding:0.5rem 0.75rem;border-radius:0.4rem;background:rgba(15,23,42,0.9);border:1px solid var(--border-subtle);">
                        <div style="font-size:0.8rem;color:var(--text-muted);">
                            {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $ym)->format('M Y') }}
                        </div>
                        <div style="font-size:1rem;font-weight:600;">
                            {{ number_format($count) }}
                        </div>
                    </div>
                @empty
                    <p class="dash-panel-subtitle">No assessment attempts yet.</p>
                @endforelse
            </div>

            <h3 style="font-size:0.9rem;font-weight:600;margin:1.5rem 0 0.5rem;">Membership growth – last 6 months</h3>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                @forelse($membersByMonth ?? [] as $ym => $count)
                    <div style="min-width:120px;padding:0.5rem 0.75rem;border-radius:0.4rem;background:rgba(15,23,42,0.9);border:1px solid var(--border-subtle);">
                        <div style="font-size:0.8rem;color:var(--text-muted);">
                            {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $ym)->format('M Y') }}
                        </div>
                        <div style="font-size:1rem;font-weight:600;">
                            {{ number_format($count) }}
                        </div>
                    </div>
                @empty
                    <p class="dash-panel-subtitle">No new members yet.</p>
                @endforelse
            </div>

            <h3 style="font-size:0.9rem;font-weight:600;margin:1.5rem 0 0.5rem;">Active users (logins) per day – last 7 days</h3>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                @php
                    $last7Days = collect();
                    for ($i = 6; $i >= 0; $i--) {
                        $d = now()->subDays($i)->format('Y-m-d');
                        $last7Days->put($d, $activeUsersByDay[$d] ?? 0);
                    }
                @endphp
                @foreach($last7Days as $dt => $count)
                    <div style="min-width:100px;padding:0.5rem 0.75rem;border-radius:0.4rem;background:rgba(15,23,42,0.9);border:1px solid var(--border-subtle);">
                        <div style="font-size:0.8rem;color:var(--text-muted);">
                            {{ \Illuminate\Support\Carbon::parse($dt)->format('D M j') }}
                        </div>
                        <div style="font-size:1rem;font-weight:600;">
                            {{ number_format($count) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="dash-panel-subtitle" style="margin-top:1.5rem;">
                For deeper analysis, export raw data from Members, Certificates, Academy, and Dialogue sections and combine in your reporting tools.
            </p>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:0.75rem;">
                <a href="{{ route('admin.analytics.export.enrolments') }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">Export enrolments CSV</a>
                <a href="{{ route('admin.analytics.export.attempts') }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">Export attempts CSV</a>
            </div>

            <h3 style="font-size:0.9rem;font-weight:600;margin:2rem 0 0.5rem;">Recent Activity</h3>
            <div style="margin-top:0.5rem;max-height:320px;overflow-y:auto;">
                @forelse($recentActivity ?? [] as $log)
                    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:baseline;padding:0.5rem 0;border-bottom:1px solid var(--border-subtle);font-size:0.85rem;">
                        <span style="font-weight:500;">{{ $log->actor?->name ?? $log->actor?->email ?? 'System' }}</span>
                        <span style="color:var(--text-muted);">{{ $log->created_at?->diffForHumans() }}</span>
                        <span>{{ match($log->action ?? '') {
                            'auth.web.logged_in' => 'Logged in (web)',
                            'auth.api.logged_in' => 'Logged in (API)',
                            'auth.web.logged_out' => 'Logged out (web)',
                            'auth.api.logged_out' => 'Logged out (API)',
                            'auth.web.registered' => 'Registered (web)',
                            'auth.api.registered' => 'Registered (API)',
                            'auth.web.login_failed' => 'Login failed (web)',
                            'auth.api.login_failed' => 'Login failed (API)',
                            'auth.api.refresh_failed' => 'Token refresh failed',
                            'auth.api.refresh_succeeded' => 'Token refreshed',
                            'auth.api.password_reset_rate_limited' => 'Password reset rate limited',
                            'auth.api.password_reset_requested' => 'Password reset requested',
                            'academy.attempt_started' => 'Started assessment',
                            'academy.attempt_submitted' => 'Submitted assessment',
                            'certificate.revoked' => 'Revoked certificate',
                            'certificate.reinstated' => 'Reinstated certificate',
                            default => \Illuminate\Support\Str::replace(['.', '_'], [' ', ' '], $log->action ?? 'Unknown'),
                        } }}</span>
                    </div>
                @empty
                    <p class="dash-panel-subtitle">No recent activity.</p>
                @endforelse
            </div>

            <h3 style="font-size:0.9rem;font-weight:600;margin:2rem 0 0.5rem;">Activity calendar – last 35 days</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(2.5rem,1fr));gap:0.25rem;margin-top:0.5rem;">
                @php
                    $activityMax = ($activityByDate ?? collect())->isEmpty() ? 1 : max(1, ($activityByDate ?? collect())->max());
                @endphp
                @foreach($activityByDate ?? [] as $d => $cnt)
                    @php
                        $opacity = $activityMax > 0 ? (0.15 + 0.7 * ($cnt / $activityMax)) : 0.15;
                        $dayNum = \Illuminate\Support\Carbon::parse($d)->format('j');
                    @endphp
                    <div class="activity-calendar-cell" style="padding:0.4rem;border-radius:0.3rem;background:rgba(34,197,94,{{ $opacity }});border:1px solid var(--border-subtle);text-align:center;min-height:2.5rem;" title="{{ \Illuminate\Support\Carbon::parse($d)->format('F j, Y') }}: {{ $cnt }} {{ $cnt === 1 ? 'activity' : 'activities' }}">
                        <div style="font-size:0.75rem;font-weight:600;">{{ $dayNum }}</div>
                        <div style="font-size:0.7rem;color:var(--text-muted);">{{ $cnt }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <style>
        .dash-stat {
            padding:0.8rem 1rem;
            border-radius:0.5rem;
            background:rgba(15,23,42,0.9);
            border:1px solid var(--border-subtle);
        }
        .dash-stat-label {
            font-size:0.8rem;
            color:var(--text-muted);
        }
        .dash-stat-value {
            font-size:1.4rem;
            font-weight:700;
            margin-top:0.15rem;
        }
        .dash-stat-sub {
            font-size:0.8rem;
            color:var(--text-muted);
            margin-top:0.25rem;
        }
        .dash-stat-badge {
            display:inline-block;
            margin-left:0.5rem;
            padding:0.1rem 0.35rem;
            border-radius:0.25rem;
            font-size:0.75rem;
            font-weight:600;
        }
        .dash-stat-badge--pos {
            background:rgba(34,197,94,0.25);
            color:#4ade80;
        }
        .dash-stat-badge--neg {
            background:rgba(239,68,68,0.25);
            color:#f87171;
        }
        .activity-calendar-cell {
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
        }
    </style>
@endsection

