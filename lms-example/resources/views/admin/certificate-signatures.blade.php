@extends('layouts.admin')

@section('title', 'Certificate signatures')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Certificate signatures</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Certificate signatures</h1>
    <p class="text-muted mb-4">Upload signatures that appear on TTM Group certificates: <strong>Board of Faculty</strong> and <strong>Supervisor</strong>. These are shown on all certificates. Facilitators upload their own signature from their instructor area.</p>

    @if(session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-pen me-2"></i>Board of Faculty</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">This signature appears on certificates in the Board of Faculty position (centre bottom).</p>
                    @if($boardOfFaculty && $boardOfFaculty->path)
                    <p class="small text-success mb-2">Signature uploaded.</p>
                    <div class="mb-3">
                        <span class="small text-muted d-block mb-1">Preview:</span>
                        <img src="{{ route('admin.certificate-signatures.preview', ['type' => 'board_of_faculty']) }}?v={{ $boardOfFaculty->updated_at?->timestamp ?? time() }}" alt="Board of Faculty signature" class="border rounded p-1 bg-light" style="max-height: 80px; max-width: 100%; object-fit: contain;">
                    </div>
                    @endif
                    <form action="{{ route('admin.certificate-signatures.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="board_of_faculty">
                        <div class="mb-2">
                            <input type="file" name="signature" class="form-control form-control-sm" accept="image/*" required>
                            <small class="text-muted">PNG or JPG, max 2 MB</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-pen me-2"></i>Supervisor</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">This signature appears on certificates in the Supervisor position (bottom right).</p>
                    @if($supervisor && $supervisor->path)
                    <p class="small text-success mb-2">Signature uploaded.</p>
                    <div class="mb-3">
                        <span class="small text-muted d-block mb-1">Preview:</span>
                        <img src="{{ route('admin.certificate-signatures.preview', ['type' => 'supervisor']) }}?v={{ $supervisor->updated_at?->timestamp ?? time() }}" alt="Supervisor signature" class="border rounded p-1 bg-light" style="max-height: 80px; max-width: 100%; object-fit: contain;">
                    </div>
                    @endif
                    <form action="{{ route('admin.certificate-signatures.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="supervisor">
                        <div class="mb-2">
                            <input type="file" name="signature" class="form-control form-control-sm" accept="image/*" required>
                            <small class="text-muted">PNG or JPG, max 2 MB</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <p class="small text-muted">Facilitators can upload their own signature from <a href="{{ route('instructor.certificate-signature') }}">Instructor → Certificate signature</a>. That signature appears on certificates for courses they instruct.</p>
</div>
@endsection
