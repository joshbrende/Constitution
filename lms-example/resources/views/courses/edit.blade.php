@extends('layouts.facilitator')

@section('title', 'Edit: ' . $course->title)

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.instructor') }}">Instructing</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ \Illuminate\Support\Str::limit($course->title, 40) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <h1 class="h2">Edit course</h1>
    <p class="text-muted">Only facilitators and admins can create or edit courses.</p>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('courses.update', $course) }}" method="post">
                @include('courses._form', ['course' => $course, 'tags' => $tags ?? collect(), 'certificateTemplates' => $certificateTemplates ?? collect()])
            </form>

            @if(auth()->user()->canEditCourse($course))
            <a href="{{ route('instructor.facilitator-chat', $course) }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-chat-dots me-1"></i> Q&amp;A with attendees</a>
            <a href="{{ route('courses.enroll-bulk', $course) }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-people me-1"></i> Bulk enroll</a>
            @endif
            @if(auth()->user()->isAdmin())
            <a href="{{ route('courses.attendance', $course) }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-person-lines-fill me-1"></i> View attendance register</a>
            @endif

            <hr class="my-4">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form action="{{ route('courses.duplicate', $course) }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Duplicate course</button>
                </form>
                <form action="{{ route('courses.destroy', $course) }}" method="post" class="d-inline"
                      onsubmit="return confirm('Delete this course? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete course</button>
                </form>
            </div>

            <hr class="my-4">
            <h2 class="h5 mb-3">Curriculum – edit each module</h2>
            <p class="text-muted small mb-3">Click <strong>Edit</strong> on a unit to change its title, content, duration, type, media URLs, or order.</p>
            @include('courses._curriculum_edit', ['course' => $course])
        </div>
    </div>
</div>
@endsection
