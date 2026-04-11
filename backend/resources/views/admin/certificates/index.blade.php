@extends('layouts.dashboard')

@section('title', 'Certificate Verification')
@section('page_heading', 'Certificate Verification')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Certificates</div>
                    <div class="dash-panel-subtitle">
                        Verify membership certificates by unique certificate number and verification code. Each certificate has a unique ID and QR code for fraud prevention.
                        <a href="{{ route('certificate.verify') }}" target="_blank" style="color:var(--zanupf-green);margin-left:0.5rem;">Verify a certificate →</a>
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;align-items:center;">
                    <a href="{{ route('certificate.preview') }}" target="_blank" rel="noopener" style="padding:0.4rem 0.75rem;background:var(--zanupf-gold);color:#020617;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.85rem;font-weight:600;">Preview certificate</a>
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:-0.25rem;margin-bottom:0.5rem;">
                <a href="{{ route('admin.certificates.index') }}"
                   style="padding:0.4rem 0.75rem;background:rgba(148,163,184,0.12);color:var(--text-main);border:1px solid rgba(148,163,184,0.35);border-radius:0.4rem;text-decoration:none;font-size:0.85rem;font-weight:600;">
                    Clear filters
                </a>
            </div>

            <form method="GET" action="{{ route('admin.certificates.index') }}" style="margin-bottom:1rem;" role="search" aria-label="Filter certificates">
                <fieldset style="border:0;margin:0;padding:0;min-width:0;">
                    <legend style="font-size:0.8rem;font-weight:600;color:var(--text-main);margin-bottom:0.5rem;padding:0;">Search and filters</legend>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0.5rem;max-width:36rem;">
                        <div>
                            <label for="cert_filter_number" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">Certificate number</label>
                            <input id="cert_filter_number" type="text" name="number" value="{{ request('number') }}" placeholder="Optional"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                        </div>

                        <div>
                            <label for="cert_filter_search_mode" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">Match field</label>
                            <select id="cert_filter_search_mode" name="search_mode"
                                    style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                                <option value="member_name" {{ request('search_mode') === 'member_name' ? 'selected' : '' }}>Member name</option>
                                <option value="certificate_number" {{ request('search_mode') === 'certificate_number' ? 'selected' : '' }}>Certificate #</option>
                                <option value="verification_code" {{ request('search_mode') === 'verification_code' ? 'selected' : '' }}>Verification code</option>
                            </select>
                        </div>

                        <div>
                            <label for="cert_filter_q" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">Search text</label>
                            <input id="cert_filter_q" type="search" name="q" value="{{ request('q') }}" placeholder="Optional" autocomplete="off"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                        </div>

                        <div>
                            <label for="cert_filter_from" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">Issued from</label>
                            <input id="cert_filter_from" type="date" name="from" value="{{ request('from') }}"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                        </div>

                        <div>
                            <label for="cert_filter_to" style="display:block;font-size:0.72rem;color:var(--text-muted);margin-bottom:0.25rem;">Issued to</label>
                            <input id="cert_filter_to" type="date" name="to" value="{{ request('to') }}"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                        </div>

                        <div style="display:flex;align-items:flex-end;">
                            <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">
                                Search
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>

            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Certificate No.</th>
                        <th>Verify Code</th>
                        <th>Status</th>
                        <th>Recipient</th>
                        <th>Course</th>
                        <th>Issued</th>
                        <th>Expires</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($certificates as $c)
                        @php
                            $status = $c->verificationStatus();
                            $statusColor = $status === 'active'
                                ? '#22c55e'
                                : ($status === 'expired' ? '#fbbf24' : '#f87171');
                        @endphp
                        <tr>
                            <td><code style="font-size:0.85rem;">{{ $c->certificate_number }}</code></td>
                            <td><code style="font-size:0.8rem;">{{ $c->verification_code ?? '—' }}</code></td>
                            <td>
                                <span style="display:inline-block;padding:0.2rem 0.45rem;border-radius:0.35rem;background:rgba(15,23,42,0.9);border:1px solid {{ $statusColor }};color:{{ $statusColor }};font-size:0.78rem;text-transform:uppercase;font-weight:700;">
                                    {{ $status }}
                                </span>
                            </td>
                            <td>{{ $c->user->name }} {{ $c->user->surname }}</td>
                            <td>{{ $c->course->title ?? $c->course->code }}</td>
                            <td>{{ $c->issued_at->format('d M Y') }}</td>
                            <td>{{ $c->expires_at ? $c->expires_at->format('d M Y') : '—' }}</td>
                            <td>
                                @if ($c->revoked_at)
                                    <form method="POST" action="{{ route('admin.certificates.unrevoke', $c) }}">
                                        @csrf
                                        <button type="submit" style="padding:0.35rem 0.6rem;background:#14532d;color:#dcfce7;border:1px solid #15803d;border-radius:0.35rem;cursor:pointer;font-size:0.78rem;font-weight:700;">
                                            Reinstate
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.certificates.revoke', $c) }}" onsubmit="return confirm('Revoke this certificate?');">
                                        @csrf
                                        <label for="cert_revoke_reason_{{ $c->id }}" style="display:block;font-size:0.68rem;color:var(--text-muted);margin-bottom:0.2rem;">Reason (optional)</label>
                                        <input id="cert_revoke_reason_{{ $c->id }}" type="text" name="reason" placeholder="Reason"
                                               style="margin-bottom:0.35rem;width:170px;padding:0.3rem 0.4rem;border:1px solid var(--border-subtle);border-radius:0.35rem;background:rgba(15,23,42,0.9);color:var(--text-main);font-size:0.75rem;">
                                        <button type="submit" style="padding:0.35rem 0.6rem;background:#7f1d1d;color:#fee2e2;border:1px solid #b91c1c;border-radius:0.35rem;cursor:pointer;font-size:0.78rem;font-weight:700;">
                                            Revoke
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($certificates->hasPages())
                <div style="margin-top:1rem;">
                    {{ $certificates->links() }}
                </div>
            @endif

            @if ($certificates->isEmpty())
                <p class="dash-panel-subtitle">No certificates yet. Certificates are issued when members pass the membership assessment.</p>
            @endif
        </section>
    </div>
@endsection
