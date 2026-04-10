@extends('layouts.dashboard')

@section('title', 'Audit Logs')
@section('page_heading', 'Audit Logs')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Audit Logs</div>
                    <div class="dash-panel-subtitle">Authentication, academy, certificates, and <strong>constitution workflow</strong> (submit / approve / reject / direct publish). Filter by <code>constitution.</code> to review amendment channels.</div>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
            </div>

            <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="margin-bottom:1rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                <input
                    type="text"
                    name="action"
                    value="{{ request('action') }}"
                    placeholder="Filter by action"
                    style="padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);min-width:12rem;"
                >
                <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">Filter</button>
            </form>

            @if ($logs->isEmpty())
                <p class="dash-panel-subtitle">No audit logs found.</p>
            @else
                <div style="overflow-x:auto;">
                    <table class="dash-table" style="margin-top:0;">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Actor</th>
                                <th>Action</th>
                                <th>Target</th>
                                <th>Workflow / details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                @php
                                    $meta = $log->metadata ?? [];
                                    $channel = $meta['workflow_channel'] ?? null;
                                    $bypass = $meta['presidium_review_bypassed'] ?? null;
                                @endphp
                                <tr>
                                    <td style="white-space:nowrap;">{{ $log->created_at?->format('d M Y H:i') }}</td>
                                    <td>{{ $log->actor?->name ?? $log->actor?->email ?? 'System' }}</td>
                                    <td><code style="font-size:0.8rem;">{{ $log->action }}</code></td>
                                    <td>
                                        @if ($log->target_type)
                                            {{ class_basename($log->target_type) }} #{{ $log->target_id }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td style="font-size:0.78rem;max-width:22rem;">
                                        @if($channel || $bypass !== null)
                                            <span style="color:var(--text-muted);">Channel:</span> <strong>{{ $channel ?? '—' }}</strong>
                                            @if($bypass === true)
                                                <span style="margin-left:0.35rem;padding:0.1rem 0.35rem;border-radius:0.25rem;background:rgba(239,68,68,0.2);color:#f87171;font-size:0.72rem;">Review bypassed</span>
                                            @elseif($bypass === false)
                                                <span style="margin-left:0.35rem;padding:0.1rem 0.35rem;border-radius:0.25rem;background:rgba(34,197,94,0.2);color:#4ade80;font-size:0.72rem;">Presidium review</span>
                                            @endif
                                        @elseif(!empty($meta))
                                            <code style="font-size:0.7rem;word-break:break-all;">{{ \Illuminate\Support\Str::limit(json_encode($meta), 120) }}</code>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:1rem;">
                    {{ $logs->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
