@extends('layouts.auth')

@section('title', 'Verify Payment Receipt – ZANU PF')

@section('content')
    <h1 class="auth-title">Verify payment receipt</h1>
    <p class="auth-subtitle">
        Finance and provincial offices can confirm a certificate payment receipt before accepting cash.
    </p>

    <form method="GET" action="{{ route('receipt.verify') }}">
        <div class="form-group">
            <label for="receipt">Receipt number</label>
            <input
                id="receipt"
                type="text"
                name="receipt"
                value="{{ $receipt }}"
                placeholder="e.g. ZPF-REC-2026-HAR-000042"
            >
        </div>
        <div class="form-group">
            <label for="ref">Payment reference (optional)</label>
            <input
                id="ref"
                type="text"
                name="ref"
                value="{{ $paymentRef }}"
                placeholder="e.g. HAR-000042-K9"
                maxlength="16"
            >
        </div>
        <button type="submit" class="auth-button">Verify</button>
    </form>

    @if ($receipt !== '' || $paymentRef !== '' || $publicId !== '')
        <div style="margin-top:1.5rem;padding:1rem;border-radius:0.5rem;border:1px solid var(--border-subtle, #334155);">
            @if ($verified && $application)
                <p style="color:#4ade80;font-weight:600;margin:0 0 0.5rem 0;">Receipt found — valid in system</p>
                <table style="width:100%;font-size:0.9rem;color:var(--text-muted, #94a3b8);">
                    <tr><td style="padding:0.25rem 0;">Receipt</td><td><strong>{{ $application->receipt_number }}</strong></td></tr>
                    <tr><td style="padding:0.25rem 0;">Payment ref</td><td><strong>{{ $application->payment_reference_code }}</strong></td></tr>
                    <tr><td style="padding:0.25rem 0;">Amount due</td><td>{{ $application->fee_currency }} {{ number_format((float) $application->fee_amount, 2) }}</td></tr>
                    <tr><td style="padding:0.25rem 0;">Status</td><td>{{ $application->status->label() }}</td></tr>
                    <tr><td style="padding:0.25rem 0;">Exam passed</td><td>{{ optional($application->exam_passed_at)->format('d M Y') ?? '—' }}</td></tr>
                </table>
                <p style="font-size:0.8rem;margin:0.75rem 0 0 0;color:var(--text-muted, #94a3b8);">
                    Applicant identity is not shown on this public page. Confirm payment in the admin console after collecting fees.
                </p>
            @else
                <p style="color:#f87171;font-weight:600;margin:0;">Receipt not found. Check the number and try again.</p>
            @endif
        </div>
    @endif
@endsection
