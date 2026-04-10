@extends('layouts.facilitator')

@section('title', 'Q&A – ' . $course->title)

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.instructor') }}">Instructing</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ \Illuminate\Support\Str::limit($course->title, 40) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Q&A</li>
        </ol>
    </nav>

    <h1 class="h2 mb-2">Chat with facilitator</h1>
    <p class="text-muted mb-4">View and manage questions from attendees. Reply, mark as answered, or dismiss. Post announcements to all.</p>

    @include('courses._facilitator_chat_panel', [
        'course' => $course,
        'unitId' => null,
        'isFacilitator' => true,
    ])

    <div class="mt-4 p-4 border rounded bg-light">
        <p class="text-muted small mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Click the <strong>Q&A</strong> tab on the right to view and manage all questions and announcements for this course.
        </p>
    </div>
</div>
@endsection
