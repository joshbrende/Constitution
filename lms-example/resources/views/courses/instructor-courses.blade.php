@extends('layouts.facilitator')

@section('title', 'Instructing')

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2">Instructing Courses</h1>
    <p class="text-muted">@if(isset($canEdit) && $canEdit) Facilitators see courses they instruct; admins see all courses. Only facilitators and admins can edit. @else Courses you instruct. @endif</p>

    @if(isset($canEdit) && $canEdit)
    <a href="{{ route('courses.create') }}" class="btn btn-primary mb-3"><i class="bi bi-plus-lg me-1"></i> Create course</a>
    @endif

    @if($courses->isEmpty())
    <div class="alert alert-info">You have no instructing courses. @if(isset($canEdit) && $canEdit) <a href="{{ route('courses.create') }}">Create one</a>. @endif</div>
    @else
    <div class="row g-4">
        @foreach($courses as $c)
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
                    <p class="small text-muted mb-auto">{{ $c->enrollment_count }} enrolled @if($c->instructor) · {{ $c->instructor->name }} @endif</p>
                    <div class="d-flex gap-1 mt-2 flex-wrap">
                        <a href="{{ route('courses.show', $c) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @if(isset($canEdit) && $canEdit)
                        <a href="{{ route('instructor.course-learners', $c) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-people me-1"></i>Learners
                        </a>
                        <a href="{{ route('instructor.facilitator-chat', $c) }}" class="btn btn-sm btn-outline-secondary" title="Q&amp;A with attendees"><i class="bi bi-chat-dots"></i> Q&amp;A</a>
                        <a href="{{ route('courses.edit', $c) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
                        <form action="{{ route('courses.duplicate', $c) }}" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Duplicate course">Duplicate</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $courses->links() }}
    </div>
    @endif
</div>
@endsection
