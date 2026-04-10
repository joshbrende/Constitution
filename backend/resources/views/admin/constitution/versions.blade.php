@extends('layouts.dashboard')

@section('title', 'Versions – ' . $section->logical_number)
@section('page_heading', 'Versions – ' . $section->logical_number . ' – ' . $section->title)

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Versions</div>
                    <div class="dash-panel-subtitle">{{ $section->logical_number }} – {{ $section->title }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <a href="{{ route('admin.constitution.versions.create', $section) }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">New amendment</a>
                    <a href="{{ route('admin.constitution.sections', $section->chapter) }}" class="dash-btn-ghost" style="text-decoration:none;">← Sections</a>
                </div>
            </div>

            <table class="dash-table">
                <thead>
                    <tr><th>Version</th><th>Status</th><th>Effective date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach ($versions as $v)
                        @php
                            $statusClass = match($v->status) {
                                'published' => 'status-pill--active',
                                'in_review' => 'status-pill--review',
                                default => 'status-pill--pending',
                            };
                        @endphp
                        <tr>
                            <td>{{ $v->version_number }}</td>
                            <td>
                                <span class="status-pill {{ $statusClass }}">
                                    <span class="dot"></span>
                                    {{ ucfirst(str_replace('_', ' ', $v->status)) }}
                                </span>
                            </td>
                            <td>{{ $v->effective_from ? \Carbon\Carbon::parse($v->effective_from)->format('Y-m-d') : '—' }}</td>
                            <td>
                                @if ($v->status === 'draft')
                                    <a href="{{ route('admin.constitution.versions.edit', $v) }}" style="font-size:0.8rem;margin-right:0.5rem;">Edit</a>
                                    <form method="POST" action="{{ route('admin.constitution.versions.submit', $v) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="background:none;border:none;color:var(--zanupf-gold);cursor:pointer;font-size:0.8rem;">Submit for approval</button>
                                    </form>
                                @elseif ($v->status === 'in_review' && $canApprove)
                                    <form method="POST" action="{{ route('admin.constitution.versions.approve', $v) }}" style="display:inline;margin-right:0.5rem;">
                                        @csrf
                                        <button type="submit" style="background:none;border:none;color:var(--zanupf-green);cursor:pointer;font-size:0.8rem;">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.constitution.versions.reject', $v) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Reject</button>
                                    </form>
                                @elseif ($v->status === 'in_review')
                                    <span style="font-size:0.8rem;color:var(--text-muted);">Awaiting approval</span>
                                @else
                                    <span style="font-size:0.8rem;color:var(--text-muted);">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($versions->isEmpty())
                <p class="dash-panel-subtitle">No versions yet. <a href="{{ route('admin.constitution.versions.create', $section) }}">Create a new amendment</a>.</p>
            @endif
        </section>
    </div>
@endsection
