@extends('layouts.auth')

@section('title', 'Verify Certificate – ZANU PF')

@section('content')
    <h1 class="auth-title">Verify Certificate</h1>
    <p class="auth-subtitle">
        Enter the certificate number and verification code to confirm authenticity.
    </p>

    <form method="GET" action="{{ url('/verify-certificate') }}">
        @if (!empty($publicId))
            <input type="hidden" name="id" value="{{ $publicId }}">
        @endif
        @if (!empty($token))
            <input type="hidden" name="token" value="{{ $token }}">
        @endif
        <div class="form-group">
            <label for="number">Certificate Number</label>
            <input
                id="number"
                type="text"
                name="number"
                value="{{ $number }}"
                placeholder="e.g. ZP-MEM-2026-00001"
                required
            >
        </div>

        <div class="form-group">
            <label for="code">Verification Code</label>
            <input
                id="code"
                type="text"
                name="code"
                value="{{ $code }}"
                placeholder="8-character code"
                maxlength="12"
                required
            >
        </div>

        <div class="actions-row" style="margin-top:1.25rem;">
            <button type="submit" class="btn-primary">Verify</button>
        </div>
    </form>

    @if ($number !== '')
        <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #374151;">
            @if ($invalid)
                <p style="color:#fecaca;font-size:0.95rem;font-weight:500;">
                    Certificate not found, revoked/expired, verification details do not match, or signed token is invalid.
                </p>
            @elseif ($certificate)
                <div style="background:rgba(34,197,94,0.12);border:1px solid #15803d;border-radius:0.5rem;padding:1rem;">
                    <p style="color:{{ $isActive ? '#22c55e' : '#fbbf24' }};font-weight:600;margin-bottom:0.75rem;">
                        {{ $isActive ? '✓ Certificate is valid' : '⚠ Certificate record found (not active)' }}
                    </p>
                    <p style="font-size:0.9rem;color:#e5e7eb;margin:0.25rem 0;">
                        <strong>Recipient:</strong> {{ $certificate->user->name }} {{ $certificate->user->surname }}
                    </p>
                    <p style="font-size:0.9rem;color:#e5e7eb;margin:0.25rem 0;">
                        <strong>Course:</strong> {{ $certificate->course->title ?? $certificate->course->code }}
                    </p>
                    <p style="font-size:0.9rem;color:#e5e7eb;margin:0.25rem 0;">
                        <strong>Issued:</strong> {{ $certificate->issued_at->format('d F Y') }}
                    </p>
                    <p style="font-size:0.9rem;color:#e5e7eb;margin:0.25rem 0;">
                        <strong>Verification status:</strong> {{ strtoupper($status ?? 'active') }}
                    </p>
                    @if($certificate->expires_at)
                        <p style="font-size:0.9rem;color:#e5e7eb;margin:0.25rem 0;">
                            <strong>Expires:</strong> {{ $certificate->expires_at->format('d F Y') }}
                        </p>
                    @endif
                    @if($certificate->revoked_at)
                        <p style="font-size:0.9rem;color:#fecaca;margin:0.25rem 0;">
                            <strong>Revoked:</strong> {{ $certificate->revoked_at->format('d F Y H:i') }}
                        </p>
                    @endif
                    @if($tokenValid !== null)
                        <p style="font-size:0.9rem;color:#e5e7eb;margin:0.25rem 0;">
                            <strong>Signed token:</strong> {{ $tokenValid ? 'Valid' : 'Invalid' }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    @endif
@endsection
