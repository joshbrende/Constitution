@extends('layouts.facilitator')

@section('title', 'Bulk enroll: ' . $course->title)

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.instructor') }}">Instructing</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ \Illuminate\Support\Str::limit($course->title, 40) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bulk enroll</li>
        </ol>
    </nav>

    <h1 class="h2">Bulk enroll</h1>
    <p class="text-muted">Upload a CSV with one email per row. The first column is treated as email. An optional header row with <code>email</code> is ignored. Max 512 KB. Users must already exist.</p>

    @if(session('message'))
    <div class="alert alert-info">{{ session('message') }}</div>
    @endif

    <form action="{{ route('courses.enroll-bulk.store', $course) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="csv" class="form-label">CSV file</label>
            <input type="file" class="form-control @error('csv') is-invalid @enderror" id="csv" name="csv" accept=".csv,.txt" required>
            @error('csv')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Enroll from CSV</button>
        <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-secondary">Back to course</a>
    </form>
</div>
@endsection
