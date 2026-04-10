@extends('layouts.facilitator')

@section('title', 'My facilitator ratings')

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">My facilitator ratings</h1>
    <p class="text-muted mb-4">See how delegates rated your teaching. Ratings are submitted by learners who complete a course you facilitate.</p>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3">
                        <i class="bi bi-star-fill display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ number_format($avg, 1) }}</div>
                        <small class="text-muted">Average rating</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="bi bi-chat-quote display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ $count }}</div>
                        <small class="text-muted">Total ratings</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($ratings->isEmpty())
    <div class="alert alert-info">No ratings yet. Ratings appear when delegates complete a course you facilitate and rate you.</div>
    @else
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Recent ratings</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Delegate</th>
                            <th>Course</th>
                            <th class="text-center">Rating</th>
                            <th>Feedback</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ratings as $r)
                        <tr>
                            <td>{{ $r->enrollment->user->name ?? 'User #' . $r->enrollment->user_id }}</td>
                            <td><a href="{{ route('courses.show', $r->enrollment->course) }}" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($r->enrollment->course->title ?? '—', 35) }}</a></td>
                            <td class="text-center"><span class="text-warning">{{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}</span> {{ $r->rating }}/5</td>
                            <td>{{ $r->review ? \Illuminate\Support\Str::limit($r->review, 80) : '—' }}</td>
                            <td><small class="text-muted">{{ $r->created_at?->format('d M Y') }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $ratings->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
