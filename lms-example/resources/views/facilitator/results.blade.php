@extends('layouts.facilitator')

@section('title', 'Knowledge Check results')

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Knowledge Check results</h1>
    <p class="text-muted mb-4">Recent Knowledge Check attempts across your courses.</p>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
        <form action="{{ route('instructor.results') }}" method="get" class="d-flex align-items-center gap-2 flex-wrap">
            <label class="mb-0">Course:</label>
            <select name="course" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                <option value="0">All courses</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ (int) ($courseFilter ?? 0) === (int) $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('instructor.results.export', ['course' => (int) ($courseFilter ?? 0)]) }}" class="btn btn-sm btn-outline-success">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
    </div>

    @if($attempts->isEmpty())
    <div class="alert alert-info">No Knowledge Check attempts yet. Results appear here when learners complete module Knowledge Checks.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Learner</th>
                    <th>Course</th>
                    <th>Knowledge Check</th>
                    <th class="text-center">Score</th>
                    <th class="text-center">Status</th>
                    <th>Completed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $a)
                <tr>
                    <td>{{ $a->user->name ?? 'User #' . $a->user_id }}</td>
                    <td>
                        <a href="{{ route('courses.show', $a->course) }}" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($a->course?->title ?? '—', 30) }}</a>
                    </td>
                    <td>{{ $a->quiz?->title ?? 'Knowledge Check #' . $a->quiz_id }}</td>
                    <td class="text-center">{{ $a->percentage }}%</td>
                    <td class="text-center">
                        @if($a->status === 'passed')
                        <span class="badge bg-success">Passed</span>
                        @else
                        <span class="badge bg-warning text-dark">Failed</span>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $a->completed_at?->format('d M Y H:i') ?? '—' }}</small></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
        <p class="small text-muted mb-0">Showing {{ $attempts->firstItem() ?? 0 }}–{{ $attempts->lastItem() ?? 0 }} of {{ $attempts->total() }} attempts.</p>
        <div>{{ $attempts->withQueryString()->links() }}</div>
    </div>
    @endif
</div>
@endsection
