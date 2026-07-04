@extends('layouts.dashboard')

@section('title', 'Audit Logs')
@section('page_heading', 'Audit Logs')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Audit Logs</div>
                    <div class="dash-panel-subtitle">
                        Immutable record of security-sensitive and workflow actions: sign-in, admin changes, academy certificates, constitution amendments, and oversight access.
                        Use category filters for faster review; export JSONL for archival analysis.
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
                    <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="dash-btn-ghost" style="text-decoration:none;">Export JSONL</a>
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="margin-bottom:1.25rem;display:grid;gap:0.75rem;">
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
                    @foreach ($categories as $key => $label)
                        @php $active = request('category') === $key; @endphp
                        <a
                            href="{{ route('admin.audit-logs.index', array_merge(request()->except('category', 'page'), $active ? [] : ['category' => $key])) }}"
                            style="padding:0.35rem 0.75rem;border-radius:999px;font-size:0.78rem;font-weight:600;text-decoration:none;border:1px solid var(--border-subtle);{{ $active ? 'background:var(--zanupf-green);color:#fff;border-color:var(--zanupf-green);' : 'background:rgba(15,23,42,0.6);color:var(--text-muted);' }}"
                        >{{ $label }}</a>
                    @endforeach
                    @if (request()->hasAny(['category', 'action', 'q', 'from', 'to']))
                        <a href="{{ route('admin.audit-logs.index') }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.78rem;padding:0.35rem 0.75rem;">Clear filters</a>
                    @endif
                </div>

                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:flex-end;">
                    <div>
                        <label for="audit-q" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">Person (name or email)</label>
                        <input id="audit-q" type="text" name="q" value="{{ request('q') }}" placeholder="e.g. member@example.org.zw"
                            style="padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);min-width:14rem;">
                    </div>
                    <div>
                        <label for="audit-action" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">Action code</label>
                        <input id="audit-action" type="text" name="action" value="{{ request('action') }}" placeholder="e.g. auth.api or certificate"
                            style="padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);min-width:12rem;">
                    </div>
                    <div>
                        <label for="audit-from" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">From</label>
                        <input id="audit-from" type="date" name="from" value="{{ request('from') }}"
                            style="padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div>
                        <label for="audit-to" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">To</label>
                        <input id="audit-to" type="date" name="to" value="{{ request('to') }}"
                            style="padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">Apply</button>
                </div>
            </form>

            @if ($logs->isEmpty())
                <p class="dash-panel-subtitle">No audit logs match your filters.</p>
            @else
                <div style="overflow-x:auto;">
                    <table class="dash-table" style="margin-top:0;">
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Category</th>
                                <th>Event</th>
                                <th>Who</th>
                                <th>Record</th>
                                <th>Summary</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                @php $view = $present($log); @endphp
                                <tr>
                                    <td style="white-space:nowrap;" title="{{ $log->created_at?->toIso8601String() }}">
                                        <div style="font-weight:600;">{{ $log->created_at?->format('d M Y') }}</div>
                                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $log->created_at?->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <span style="display:inline-block;padding:0.15rem 0.45rem;border-radius:0.25rem;font-size:0.72rem;font-weight:600;background:rgba(250,204,21,0.12);color:var(--zanupf-gold);">
                                            {{ $view['category_label'] }}
                                        </span>
                                    </td>
                                    <td style="min-width:10rem;">
                                        <div style="font-weight:600;font-size:0.85rem;">{{ $view['action_label'] }}</div>
                                        <code style="font-size:0.68rem;color:var(--text-muted);">{{ $log->action }}</code>
                                    </td>
                                    <td style="min-width:9rem;">
                                        <div style="font-weight:600;">{{ $view['actor_label'] }}</div>
                                        @if ($view['actor_hint'])
                                            <div style="font-size:0.72rem;color:var(--text-muted);">{{ $view['actor_hint'] }}</div>
                                        @endif
                                    </td>
                                    <td style="min-width:8rem;">
                                        @if ($view['target_label'])
                                            @if ($view['target_url'])
                                                <a href="{{ $view['target_url'] }}" style="color:var(--zanupf-gold);font-weight:600;text-decoration:none;">{{ $view['target_label'] }}</a>
                                            @else
                                                {{ $view['target_label'] }}
                                            @endif
                                        @else
                                            <span style="color:var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td style="min-width:14rem;max-width:20rem;">
                                        <div style="font-size:0.82rem;line-height:1.45;">{{ $view['summary'] }}</div>
                                        @if (! empty($view['details']))
                                            <details style="margin-top:0.35rem;">
                                                <summary style="cursor:pointer;font-size:0.72rem;color:var(--zanupf-gold);">Details</summary>
                                                <dl style="margin:0.35rem 0 0;font-size:0.72rem;line-height:1.5;">
                                                    @foreach ($view['details'] as $detail)
                                                        <div style="display:grid;grid-template-columns:7rem 1fr;gap:0.35rem;margin-bottom:0.2rem;">
                                                            <dt style="color:var(--text-muted);margin:0;">{{ $detail['label'] }}</dt>
                                                            <dd style="margin:0;word-break:break-word;">{{ $detail['value'] }}</dd>
                                                        </div>
                                                    @endforeach
                                                </dl>
                                            </details>
                                        @endif
                                    </td>
                                    <td style="font-size:0.75rem;color:var(--text-muted);white-space:nowrap;">
                                        @if ($view['show_ip'] && $log->ip_address)
                                            {{ $log->ip_address }}
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
