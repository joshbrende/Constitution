@extends('layouts.facilitator')

@section('title', 'Create course')

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.instructor') }}">Instructing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create course</li>
        </ol>
    </nav>

    <h1 class="h2">Create course</h1>
    <p class="text-muted">Only facilitators and admins can create or edit courses.</p>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('courses.store') }}" method="post">
                @include('courses._form', ['tags' => $tags ?? collect()])
            </form>
        </div>
    </div>
</div>
@endsection
