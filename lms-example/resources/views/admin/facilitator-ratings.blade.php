@extends('layouts.admin')

@section('title', 'Facilitator ratings')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Facilitator ratings</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Facilitator ratings</h1>
    <p class="text-muted mb-4">See how delegates rated facilitators. Ratings are submitted when learners complete a course.</p>

    @if($byFacilitator->isNotEmpty())
    <h3 class="h5 mb-3">Summary by facilitator</h3>
    <div class="row g-3 mb-4">
        @foreach($byFacilitator as $row)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <strong>{{ $row->instructor->name ?? 'User #' . $row->instructor_id }}</strong>
                    <div class="mt-2">
                        <span class="text-warning">{{ str_repeat('★', (int) round($row->avg_rating)) }}{{ str_repeat('☆', 5 - (int) round($row->avg_rating)) }}</span>
                        <span class="ms-1">{{ number_format((float) $row->avg_rating, 1) }}</span>
                        <span class="text-muted ms-1">({{ $row->count }} rating{{ $row->count !== 1 ? 's' : '' }})</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <h3 class="h5 mb-3">All ratings</h3>
    @if($ratings->isEmpty())
    <div class="alert alert-info">No facilitator ratings yet.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Facilitator</th>
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
                    <td>{{ $r->instructor->name ?? '—' }}</td>
                    <td>{{ $r->enrollment->user->name ?? 'User #' . $r->enrollment->user_id }}</td>
                    <td><a href="{{ route('courses.show', $r->enrollment->course) }}" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($r->enrollment->course->title ?? '—', 30) }}</a></td>
                    <td class="text-center"><span class="text-warning">{{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}</span> {{ $r->rating }}/5</td>
                    <td>{{ $r->review ? \Illuminate\Support\Str::limit($r->review, 100) : '—' }}</td>
                    <td><small class="text-muted">{{ $r->created_at?->format('d M Y H:i') }}</small></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $ratings->links() }}
    </div>
    @endif
</div>
@endsection
