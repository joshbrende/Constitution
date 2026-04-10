@extends('layouts.dashboard')

@section('title', 'Dialogue reports')
@section('page_heading', 'Dialogue reports')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Reports</div>
                    <div class="dash-panel-subtitle">User reports for messages and threads (UGC moderation).</div>
                </div>
                <a href="{{ route('admin.dialogue.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Dialogue</a>
            </div>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.75rem;">
                @foreach (['open' => 'Open', 'reviewed' => 'Reviewed', 'resolved' => 'Resolved', 'rejected' => 'Rejected', 'all' => 'All'] as $k => $label)
                    <a href="{{ route('admin.dialogue.reports.index', ['status' => $k]) }}"
                       class="dash-tag"
                       style="text-decoration:none;{{ $status === $k ? 'border-color:rgba(250,204,21,0.7);color:var(--zanupf-gold);' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <table class="dash-table">
                <thead>
                <tr>
                    <th>When</th>
                    <th>Reporter</th>
                    <th>Reason</th>
                    <th>Reported user</th>
                    <th>Thread / Message</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($reports as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ $r->created_at?->diffForHumans() }}</td>
                        <td>
                            {{ $r->reporter?->name }} {{ $r->reporter?->surname }}
                            <div style="color:var(--text-muted);font-size:0.75rem;">{{ $r->reporter?->email }}</div>
                        </td>
                        <td>
                            <span class="dash-tag">{{ strtoupper($r->reason) }}</span>
                            @if ($r->details)
                                <div style="color:var(--text-muted);font-size:0.75rem;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $r->details }}
                                </div>
                            @endif
                        </td>
                        <td>
                            {{ $r->reportedUser?->name }} {{ $r->reportedUser?->surname }}
                            <div style="color:var(--text-muted);font-size:0.75rem;">{{ $r->reportedUser?->email }}</div>
                        </td>
                        <td style="max-width:420px;">
                            @if ($r->thread)
                                <div style="font-weight:600;">
                                    <a href="{{ route('admin.dialogue.threads.show', ['thread' => $r->thread->id]) }}" style="color:var(--zanupf-gold);text-decoration:none;">
                                        {{ $r->thread->title }}
                                    </a>
                                </div>
                            @endif
                            @if ($r->message)
                                <div style="color:var(--text-muted);font-size:0.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $r->message->is_deleted ? '[Removed]' : '' }} {{ $r->message->body }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="dash-tag">{{ $r->status }}</span>
                            @if ($r->resolution_action)
                                <div style="color:var(--text-muted);font-size:0.75rem;">{{ $r->resolution_action }}</div>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">
                            <form method="POST" action="{{ route('admin.dialogue.reports.resolve', ['report' => $r->id]) }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="status" value="reviewed">
                                <button class="dash-btn-ghost" type="submit" style="text-decoration:none;">Mark reviewed</button>
                            </form>
                            <span style="color:var(--text-muted);">·</span>
                            <form method="POST" action="{{ route('admin.dialogue.reports.resolve', ['report' => $r->id]) }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="status" value="resolved">
                                <input type="hidden" name="resolution_action" value="none">
                                <button class="dash-btn-ghost" type="submit" style="text-decoration:none;">Resolve</button>
                            </form>
                            @if ($r->message && ! $r->message->is_deleted)
                                <span style="color:var(--text-muted);">·</span>
                                <form method="POST" action="{{ route('admin.dialogue.reports.remove-message', ['report' => $r->id]) }}" style="display:inline;">
                                    @csrf
                                    <button class="dash-btn-ghost" type="submit" style="text-decoration:none;color:#f87171;">Remove message</button>
                                </form>
                            @endif
                            @if ($r->thread && $r->thread->status !== 'locked')
                                <span style="color:var(--text-muted);">·</span>
                                <form method="POST" action="{{ route('admin.dialogue.reports.lock-thread', ['report' => $r->id]) }}" style="display:inline;">
                                    @csrf
                                    <button class="dash-btn-ghost" type="submit" style="text-decoration:none;color:#facc15;">Lock thread</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div style="margin-top:0.75rem;">
                {{ $reports->links() }}
            </div>
        </section>
    </div>
@endsection

