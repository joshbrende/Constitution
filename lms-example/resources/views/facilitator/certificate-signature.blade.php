@extends('layouts.facilitator')

@section('title', 'Certificate signature')

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Certificate signature</h1>
    <p class="text-muted mb-4">Upload your signature so it appears on TTM Group certificates for courses you instruct (Facilitator position, bottom left).</p>

    @if(session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card shadow-sm" style="max-width: 28rem;">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-pen me-2"></i>Facilitator signature</h5>
        </div>
        <div class="card-body">
            @if($signature && $signature->path)
            <p class="small text-success mb-2">Your signature is set. It will appear on certificates for courses you instruct.</p>
            @endif
            <form action="{{ route('instructor.certificate-signature.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="signature" class="form-label">Upload image</label>
                    <input type="file" name="signature" id="signature" class="form-control" accept="image/*" required>
                    <small class="text-muted">PNG or JPG, max 2 MB. Use a clear image of your signature (e.g. on white background).</small>
                </div>
                <button type="submit" class="btn btn-primary">Save signature</button>
            </form>
        </div>
    </div>
</div>
@endsection
