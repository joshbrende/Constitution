@extends('layouts.facilitator')

@section('title', 'Assignment submissions')

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Assignment submissions</h1>
    <p class="text-muted mb-4">View and grade assignment submissions from your courses.</p>

    <form action="{{ route('instructor.submissions.index') }}" method="get" class="mb-4 d-flex flex-wrap align-items-center gap-2">
        <label class="mb-0">Course:</label>
        <select name="course" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
            <option value="0">All courses</option>
            @foreach($courses as $c)
            <option value="{{ $c->id }}" {{ (int) ($courseFilter ?? 0) === (int) $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
            @endforeach
        </select>
        <label class="mb-0 ms-2">Status:</label>
        <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
            <option value="" {{ ($statusFilter ?? '') === '' ? 'selected' : '' }}>All</option>
            <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="graded" {{ ($statusFilter ?? '') === 'graded' ? 'selected' : '' }}>Graded</option>
        </select>
        @if(($courseFilter ?? 0) || ($statusFilter ?? ''))
        <a href="{{ route('instructor.submissions.index') }}" class="btn btn-sm btn-outline-secondary">Clear filters</a>
        @endif
    </form>

    @if($submissions->isEmpty())
    <div class="alert alert-info">No assignment submissions yet.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Learner</th>
                    <th>Assignment / Course</th>
                    <th>Submitted</th>
                    <th class="text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $s)
                <tr>
                    <td>{{ $s->user->name ?? 'User #' . $s->user_id }}</td>
                    <td>
                        <strong>{{ $s->assignment->title ?? '—' }}</strong>
                        <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($s->course?->title ?? '—', 40) }}</small>
                    </td>
                    <td><small>{{ $s->submitted_at?->format('d M Y H:i') ?? '—' }}</small></td>
                    <td class="text-center">
                        @if($s->status === 'graded' || $s->status === 'returned')
                        <span class="badge bg-success">Graded</span> {{ $s->score }}/{{ $s->max_points }}
                        @else
                        <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('instructor.submissions.grade', $s) }}" class="btn btn-sm btn-outline-primary">View / Grade</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $submissions->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
