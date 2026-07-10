@extends('layouts.dashboard')

@section('title', 'Member notifications')
@section('page_heading', 'Member notifications')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Mobile notification inbox</div>
                    <div class="dash-panel-subtitle">
                        Compose announcements or review automatic alerts when courses, constitution text, and library documents are published. Delivered to the bell icon in the mobile app.
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <a href="{{ route('admin.member-notifications.create') }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">
                        New notification
                    </a>
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
            @endif

            <table class="dash-table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Source</th>
                    <th>Audience</th>
                    <th>CTA</th>
                    <th>Status</th>
                    <th>Recipients</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($campaigns as $campaign)
                    <tr>
                        <td>{{ $campaign->title }}</td>
                        <td>
                            @if ($campaign->trigger)
                                <span style="font-size:0.75rem;font-weight:600;color:var(--zanupf-green);">Automatic</span>
                                <div style="font-size:0.7rem;color:var(--text-muted);margin-top:0.15rem;">{{ $campaign->trigger }}</div>
                            @else
                                <span style="font-size:0.75rem;color:var(--text-muted);">Manual</span>
                            @endif
                        </td>
                        <td>
                            @if ($campaign->audience_type === 'all')
                                All members
                            @elseif ($campaign->audience_type === 'province')
                                Province: {{ $campaign->province?->name ?? '—' }}
                            @else
                                Role: {{ $campaign->role?->name ?? '—' }}
                            @endif
                        </td>
                        <td>{{ $campaign->cta_type === 'none' ? '—' : ($campaign->cta_label ?? $campaign->cta_type) }}</td>
                        <td>
                            @if($campaign->isPublished())
                                <span class="status-pill status-pill--active"><span class="dot"></span>Published</span>
                            @else
                                <span class="status-pill status-pill--pending"><span class="dot"></span>Draft</span>
                            @endif
                        </td>
                        <td>{{ $campaign->isPublished() ? number_format($campaign->recipients_count) : '—' }}</td>
                        <td style="white-space:nowrap;">
                            @if (! $campaign->isPublished())
                                <a href="{{ route('admin.member-notifications.edit', $campaign) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--accent-link, var(--zanupf-gold));">Edit</a>
                                <form method="POST" action="{{ route('admin.member-notifications.publish', $campaign) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" style="background:none;border:none;color:var(--zanupf-green);cursor:pointer;font-size:0.8rem;margin-right:0.5rem;" onclick="return confirm('Send this notification now?');">
                                        Publish
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.member-notifications.destroy', $campaign) }}" style="display:inline;" onsubmit="return confirm('Delete this draft?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                </form>
                            @else
                                <span style="font-size:0.75rem;color:var(--text-muted);">{{ optional($campaign->published_at)->format('Y-m-d H:i') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="dash-panel-subtitle">No notifications yet. Create one to reach members through the mobile app bell icon.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
