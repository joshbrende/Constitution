@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="container">
    <h1 class="h2">My Courses</h1>
    <p class="text-muted">Courses you are enrolled in.</p>

    @if($enrollments->isEmpty())
    <div class="alert alert-info">You are not enrolled in any courses. <a href="{{ route('courses.index') }}">Browse courses</a>.</div>
    @else
    <div class="row g-4">
        @foreach($enrollments as $e)
        @php $c = $e->course; @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                @if($c->featured_image)
                <img src="{{ asset('storage/' . $c->featured_image) }}" class="card-img-top" alt="" style="height:140px;object-fit:cover;">
                @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height:140px;">
                    <i class="bi bi-journal-text text-white display-4"></i>
                </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><a href="{{ route('courses.show', $c) }}" class="text-decoration-none text-dark">{{ $c->title }}</a></h5>
                    <div class="progress mb-2" style="height:6px;">
                        <div class="progress-bar" role="progressbar" style="width:{{ $e->progress_percentage }}%" aria-valuenow="{{ $e->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="small text-muted mb-auto">{{ $e->progress_percentage }}% complete</p>
                    <a href="{{ route('learn.show', ['course' => $c, 'start' => 1]) }}" class="btn btn-sm btn-outline-primary mt-2">Continue</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $enrollments->links() }}
    </div>
    @endif
</div>
@endsection
