@extends('layouts.admin')

@section('title', 'Attendance register – ' . $course->title)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ \Illuminate\Support\Str::limit($course->title, 35) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attendance register</li>
        </ol>
    </nav>

    <h1 class="h2">Attendance register</h1>
    <p class="text-muted">{{ $course->title }}</p>

    @if($rows->isEmpty())
    <div class="alert alert-info">No attendance records yet.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Designation</th>
                    <th>Organisation</th>
                    <th>Contact number</th>
                    <th>Email</th>
                    <th>Registered at</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->title ?? '—' }}</td>
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->surname }}</td>
                    <td>{{ $r->designation ?? '—' }}</td>
                    <td>{{ $r->organisation ?? '—' }}</td>
                    <td>{{ $r->contact_number ?? '—' }}</td>
                    <td>{{ $r->email }}</td>
                    <td>{{ $r->created_at?->format('d M Y H:i') ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="d-flex flex-wrap gap-2 mt-3">
        @if($rows->isNotEmpty())
        <a href="{{ route('courses.attendance.export', $course) }}" class="btn btn-outline-success"><i class="bi bi-download me-1"></i>Export CSV</a>
        @endif
        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">Back to course</a>
    </div>
</div>
@endsection
