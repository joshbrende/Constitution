@extends('layouts.dashboard')

@section('title', 'Application '.$application->receipt_number)
@section('page_heading', 'Certificate Application')

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
                    <div class="dash-panel-title">{{ $application->receipt_number }}</div>
                    <div class="dash-panel-subtitle">
                        Status: <strong>{{ $application->status->label() }}</strong>
                        · Payment ref: <strong>{{ $application->payment_reference_code }}</strong>
                    </div>
                </div>
                <a href="{{ route('admin.certificate-applications.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Queue</a>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;margin-bottom:1.5rem;">
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">Applicant</div>
                    <div style="font-weight:600;">{{ $application->user?->name }} {{ $application->user?->surname }}</div>
                    <div style="font-size:0.85rem;color:var(--text-muted);">{{ $application->user?->email }}</div>
                    <div style="font-size:0.85rem;">Province: {{ $application->user?->province?->name ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">Course</div>
                    <div style="font-weight:600;">{{ $application->course?->title }}</div>
                    <div style="font-size:0.85rem;">Fee: {{ $application->fee_currency }} {{ number_format((float) $application->fee_amount, 2) }}</div>
                    <div style="font-size:0.85rem;">
                        @if ($application->admission_source && $application->admission_source !== 'exam')
                            Admission: {{ str_replace('_', ' ', $application->admission_source) }} (exam bypassed)
                        @else
                            Exam passed: {{ optional($application->exam_passed_at)->format('d M Y H:i') ?? '—' }}
                        @endif
                    </div>
                </div>
                @if ($application->certificate)
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">Certificate</div>
                    <div style="font-weight:600;">{{ $application->certificate->certificate_number }}</div>
                    <div style="font-size:0.85rem;">PDF: {{ $application->certificate->pdf_status ?? 'pending' }}</div>
                </div>
                @endif
            </div>

            <div style="display:flex;flex-direction:column;gap:1rem;max-width:40rem;">
                @can('admin.action', 'academy_payment_confirm')
                    @if ($application->status->value === 'payment_pending')
                        <form method="POST" action="{{ route('admin.certificate-applications.confirm-payment', $application) }}" style="padding:1rem;border:1px solid var(--border-subtle);border-radius:0.5rem;">
                            @csrf
                            <div style="font-weight:600;margin-bottom:0.5rem;">Confirm payment received</div>
                            <label style="display:block;font-size:0.85rem;margin-bottom:0.35rem;">Government / teller reference (optional)</label>
                            <input type="text" name="payment_reference_note" maxlength="255" class="form-input" style="width:100%;margin-bottom:0.75rem;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:rgba(15,23,42,0.9);color:var(--text-main);">
                            <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;font-weight:600;">Confirm payment</button>
                        </form>
                    @endif
                @endcan

                @can('admin.action', 'academy_certificate_presidium_approve')
                    @if ($application->status->value === 'presidium_pending')
                        <form method="POST" action="{{ route('admin.certificate-applications.presidium-approve', $application) }}" style="padding:1rem;border:1px solid rgba(250,204,21,0.35);border-radius:0.5rem;">
                            @csrf
                            <div style="font-weight:600;margin-bottom:0.5rem;color:var(--zanupf-gold);">Presidium approve for printing</div>
                            <label style="display:block;font-size:0.85rem;margin-bottom:0.35rem;">Note (optional)</label>
                            <textarea name="presidium_note" rows="2" maxlength="1000" style="width:100%;margin-bottom:0.75rem;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:rgba(15,23,42,0.9);color:var(--text-main);"></textarea>
                            <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-gold);color:#020617;border:none;border-radius:0.4rem;font-weight:600;">Approve for printing</button>
                        </form>
                    @endif
                @endcan

                @can('admin.action', 'academy_certificate_print')
                    @if ($application->status->value === 'print_ready')
                        <div style="padding:1rem;border:1px solid var(--border-subtle);border-radius:0.5rem;display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center;">
                            <div style="font-weight:600;">Print certificate</div>
                            @if ($application->certificate)
                                <a href="{{ route('admin.certificate-applications.certificate-pdf', $application) }}" target="_blank" rel="noopener"
                                   style="padding:0.45rem 0.85rem;background:var(--zanupf-gold);color:#020617;border-radius:0.4rem;text-decoration:none;font-weight:600;font-size:0.85rem;">
                                    Download PDF
                                </a>
                            @endif
                            <form method="POST" action="{{ route('admin.certificate-applications.mark-printed', $application) }}">
                                @csrf
                                <button type="submit" style="padding:0.45rem 0.85rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;font-weight:600;font-size:0.85rem;">Mark as printed</button>
                            </form>
                        </div>
                    @endif
                @endcan

                @can('admin.action', 'academy_certificate_collection')
                    @if ($application->status->value === 'printed')
                        <form method="POST" action="{{ route('admin.certificate-applications.ready-for-collection', $application) }}" style="padding:1rem;border:1px solid var(--border-subtle);border-radius:0.5rem;">
                            @csrf
                            <div style="font-weight:600;margin-bottom:0.5rem;">Ready for collection</div>
                            <label style="display:block;font-size:0.85rem;margin-bottom:0.35rem;">Collection office (optional)</label>
                            <input type="text" name="collection_office" maxlength="255" style="width:100%;margin-bottom:0.75rem;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:rgba(15,23,42,0.9);color:var(--text-main);">
                            <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;font-weight:600;">Mark ready for collection</button>
                        </form>
                    @endif
                    @if ($application->status->value === 'ready_for_collection')
                        <form method="POST" action="{{ route('admin.certificate-applications.mark-collected', $application) }}" style="padding:1rem;border:1px solid var(--border-subtle);border-radius:0.5rem;">
                            @csrf
                            <div style="font-weight:600;margin-bottom:0.5rem;">Confirm collected by applicant</div>
                            @if ($application->collection_office)
                                <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:0.75rem;">Office: {{ $application->collection_office }}</p>
                            @endif
                            <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;font-weight:600;">Mark collected</button>
                        </form>
                    @endif
                @endcan
            </div>

            @if ($application->payment_confirmed_at || $application->presidium_approved_at || $application->printed_at || $application->collected_at)
                <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--border-subtle);">
                    <div style="font-weight:600;margin-bottom:0.5rem;">Audit trail</div>
                    <ul style="font-size:0.85rem;color:var(--text-muted);line-height:1.6;">
                        @if ($application->payment_confirmed_at)
                            <li>Payment confirmed {{ $application->payment_confirmed_at->format('d M Y H:i') }}
                                @if ($application->paymentConfirmedBy) by {{ $application->paymentConfirmedBy->name }} {{ $application->paymentConfirmedBy->surname }} @endif
                                @if ($application->payment_reference_note) · Ref: {{ $application->payment_reference_note }} @endif
                            </li>
                        @endif
                        @if ($application->presidium_approved_at)
                            <li>Presidium approved {{ $application->presidium_approved_at->format('d M Y H:i') }}
                                @if ($application->presidiumApprovedBy) by {{ $application->presidiumApprovedBy->name }} @endif
                            </li>
                        @endif
                        @if ($application->printed_at)
                            <li>Printed {{ $application->printed_at->format('d M Y H:i') }}
                                @if ($application->printedBy) by {{ $application->printedBy->name }} @endif
                            </li>
                        @endif
                        @if ($application->ready_for_collection_at)
                            <li>Ready for collection {{ $application->ready_for_collection_at->format('d M Y H:i') }}</li>
                        @endif
                        @if ($application->collected_at)
                            <li>Collected {{ $application->collected_at->format('d M Y H:i') }}
                                @if ($application->collectedBy) by {{ $application->collectedBy->name }} @endif
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </section>
    </div>
@endsection
