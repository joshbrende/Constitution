@extends('layouts.app')

@section('title', 'Certificate – ' . ($certificate->course->title ?? 'Course'))

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.my') }}">My courses</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $certificate->course) }}">{{ \Illuminate\Support\Str::limit($certificate->course->title ?? 'Course', 40) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Certificate</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-2 certificate-card" id="certificate-print">
                <div class="card-body text-center py-5 px-4">
                    <p class="text-muted text-uppercase small mb-2">Certificate of Completion</p>
                    <h1 class="h2 mb-3">{{ $certificate->course->title ?? 'Course' }}</h1>
                    <p class="lead mb-1">This is to certify that</p>
                    <p class="h4 mb-4">{{ trim(($certificate->user->name ?? '') . ' ' . ($certificate->user->surname ?? '')) ?: 'Participant' }}</p>
                    <p class="text-muted">has successfully completed the above course.</p>
                    <p class="small text-muted mt-4">Certificate no. {{ $certificate->certificate_number }}</p>
                    <p class="small text-muted">{{ $certificate->issued_at?->format('d F Y') }}</p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-center mt-4 no-print">
                <a href="{{ route('certificates.download-pdf', $certificate) }}" class="btn btn-outline-success"><i class="bi bi-download me-1"></i>Download PDF</a>
                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
                <a href="{{ route('courses.show', $certificate->course) }}" class="btn btn-outline-secondary">Back to course</a>
                <a href="{{ route('courses.my') }}" class="btn btn-outline-secondary">My courses</a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, nav, .breadcrumb, footer, .navbar { display: none !important; }
    .certificate-card { box-shadow: none !important; border: 1px solid #ccc !important; }
    body { background: #fff !important; }
}
</style>
@endsection
