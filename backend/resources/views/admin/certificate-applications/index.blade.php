@extends('layouts.dashboard')

@section('title', 'Certificate Applications')
@section('page_heading', 'Certificate Applications')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Government certificate workflow</div>
                    <div class="dash-panel-subtitle">
                        Review exam passes, confirm offline payments, Presidium approval, printing, and collection.
                        <a href="{{ route('admin.certificates.index') }}" style="color:var(--zanupf-green);margin-left:0.5rem;">Issued certificates →</a>
                    </div>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
            </div>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1rem;">
                @php
                    $tabs = [
                        'payment_pending' => 'Awaiting payment',
                        'presidium' => 'Presidium',
                        'print' => 'Ready to print',
                        'collection' => 'Collection',
                        'completed' => 'Completed',
                        'all' => 'All',
                    ];
                @endphp
                @foreach ($tabs as $key => $label)
                    @php $count = $key === 'all' ? null : ($tabCounts[$key] ?? 0); @endphp
                    <a href="{{ route('admin.certificate-applications.index', ['tab' => $key]) }}"
                       style="padding:0.4rem 0.75rem;border-radius:0.4rem;text-decoration:none;font-size:0.85rem;font-weight:600;
                              {{ $tab === $key ? 'background:var(--zanupf-green);color:#fff;' : 'background:rgba(148,163,184,0.12);color:var(--text-main);border:1px solid rgba(148,163,184,0.35);' }}">
                        {{ $label }}@if ($count !== null) ({{ $count }})@endif
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.certificate-applications.index') }}" style="margin-bottom:1rem;">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div style="display:flex;gap:0.5rem;max-width:28rem;">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Receipt, reference, name, email…"
                        style="flex:1;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;font-weight:600;">Search</button>
                </div>
            </form>

            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Receipt</th>
                        <th>Applicant</th>
                        <th>Province</th>
                        <th>Course</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th>Exam passed</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $app)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $app->receipt_number }}</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">{{ $app->payment_reference_code }}</div>
                            </td>
                            <td>
                                {{ $app->user?->name }} {{ $app->user?->surname }}
                                <div style="font-size:0.75rem;color:var(--text-muted);">{{ $app->user?->email }}</div>
                            </td>
                            <td>{{ $app->user?->province?->name ?? '—' }}</td>
                            <td>{{ $app->course?->title }}</td>
                            <td>{{ $app->fee_currency }} {{ number_format((float) $app->fee_amount, 2) }}</td>
                            <td><span class="dash-badge">{{ $app->status->label() }}</span></td>
                            <td>{{ optional($app->exam_passed_at)->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.certificate-applications.show', $app) }}" style="color:var(--zanupf-gold);font-weight:600;text-decoration:none;">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="color:var(--text-muted);padding:1.5rem;">No applications in this queue.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $applications->links() }}
        </section>
    </div>
@endsection
