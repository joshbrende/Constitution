@extends('layouts.admin')

@section('title', $user->name . ' – Users')

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
        </ol>
    </nav>

    <h1 class="h2 mb-4">{{ $user->name }}@if($user->surname) {{ $user->surname }}@endif</h1>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Account</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Email</strong> {{ $user->email }}</p>
                    <p class="mb-1"><strong>Points</strong> {{ (int) $user->points }}</p>
                    <p class="mb-3"><strong>Role</strong> <span class="badge bg-secondary">{{ $currentRole }}</span></p>

                    <form action="{{ route('admin.users.update-role', $user) }}" method="post" class="row g-2 align-items-end">
                        @csrf
                        @method('PUT')
                        <div class="col-auto">
                            <label for="role" class="form-label mb-0 small">Set role</label>
                            <select name="role" id="role" class="form-select form-select-sm">
                                <option value="student" {{ $currentRole === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="facilitator" {{ $currentRole === 'facilitator' ? 'selected' : '' }}>Facilitator</option>
                                <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary">Update role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-bookmark-check me-2"></i>Enrollments</h5>
                </div>
                <div class="card-body">
                    @if($user->enrollments->isEmpty())
                    <p class="text-muted mb-0">No enrollments.</p>
                    @else
                    <ul class="list-group list-group-flush">
                        @foreach($user->enrollments as $e)
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <a href="{{ route('courses.show', $e->course) }}">{{ $e->course->title ?? '—' }}</a>
                            <span class="badge bg-{{ (int)($e->progress_percentage ?? 0) >= 100 ? 'success' : 'secondary' }}">{{ (int)($e->progress_percentage ?? 0) }}%</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back to users</a>
</div>
@endsection
