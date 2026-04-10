@extends('layouts.admin')

@section('title', 'Certificate templates')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Certificate templates</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Certificate templates</h1>
    <p class="text-muted mb-4">Add PDF templates for certificates (upload or select a file already on the server). Each course can use one template; the certificate title comes from the course. Assign a template to a course from the course edit page.</p>

    @if(session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add template</h5>
        </div>
        <div class="card-body">
            <p class="small text-muted mb-3">Upload a PDF via the form, or upload via FTP to <code>public/asset/</code> and select it below (good for large files, e.g. 11 MB+).</p>

            <form action="{{ route('admin.certificate-templates.store') }}" method="post" enctype="multipart/form-data" id="form-upload">
                @csrf
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Template name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" placeholder="e.g. Performance training" value="{{ old('name') }}" required>
                        <small class="text-muted">Used when assigning to a course.</small>
                    </div>
                    <div class="col-md-4">
                        <label for="template" class="form-label">PDF file (upload)</label>
                        <input type="file" name="template" id="template" class="form-control form-control-sm" accept=".pdf">
                        <small class="text-muted">PDF, max 100 MB. Leave empty if selecting from server below.</small>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                    </div>
                </div>
                @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('template')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </form>

            @if(!empty($existingPdfs))
            <hr class="my-3">
            <p class="small fw-semibold mb-2">Or select a file already on the server (e.g. uploaded via FTP to <code>public/asset/</code>):</p>
            <form action="{{ route('admin.certificate-templates.store') }}" method="post">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="name_existing" class="form-label">Template name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name_existing" class="form-control form-control-sm" placeholder="e.g. Performance training" required>
                    </div>
                    <div class="col-md-4">
                        <label for="existing_file_path" class="form-label">PDF file</label>
                        <select name="existing_file" id="existing_file_path" class="form-select form-select-sm" required>
                            <option value="">— Choose a file —</option>
                            @foreach($existingPdfs as $basename => $path)
                            <option value="{{ $path }}">{{ $basename }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary btn-sm">Use this file</button>
                    </div>
                </div>
                @error('existing_file')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </form>
            @else
            <p class="small text-muted mb-0">No PDF files found in <code>public/asset/</code>. Upload PDFs there via FTP to see them here.</p>
            @endif
        </div>
    </div>

    <h5 class="mb-2">Templates</h5>
    @if($templates->isEmpty())
    <p class="text-muted">No certificate templates yet. Upload one above.</p>
    @else
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>File</th>
                    <th>Courses using</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $t)
                <tr>
                    <td>{{ $t->name }}</td>
                    <td>
                        @if($t->fileExists())
                        <span class="text-success">PDF present</span>
                        @if($t->isPublicPath())
                        <span class="badge bg-secondary ms-1">On server</span>
                        @endif
                        @else
                        <span class="text-danger">File missing</span>
                        @endif
                    </td>
                    <td>{{ $t->courses()->count() }}</td>
                    <td>
                        <form action="{{ route('admin.certificate-templates.destroy', $t) }}" method="post" class="d-inline" onsubmit="return confirm('Remove this template? Courses using it will fall back to the default certificate.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
