@extends('layouts.app')

@section('title', 'All Courses')

@section('content')
<div class="container">
    <section class="mb-4">
        <h1 class="h2">All Courses</h1>
        <p class="text-muted">Browse and enroll in courses.</p>
    </section>

    <div class="row mb-3">
        <div class="col-12">
            <form action="{{ route('courses.index') }}" method="get" class="d-flex gap-2 flex-wrap">
                <input type="hidden" name="order" value="{{ request('order', 'newest') }}">
                <input type="hidden" name="tag" value="{{ request('tag') }}">
                <input type="search" name="q" class="form-control" style="max-width:280px" placeholder="Search courses…" value="{{ request('q') }}" aria-label="Search courses">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>

    @if(($tags ?? collect())->isNotEmpty())
    <div class="mb-3">
        <span class="me-2 text-muted">Filter by tag:</span>
        <a href="{{ route('courses.index', array_filter(['q' => request('q'), 'order' => request('order')])) }}" class="badge text-bg-{{ request('tag') ? 'secondary' : 'dark' }} text-decoration-none me-1">All</a>
        @foreach($tags as $t)
        <a href="{{ route('courses.index', array_filter(['tag' => $t->slug, 'q' => request('q'), 'order' => request('order')])) }}" class="badge text-bg-{{ request('tag') === $t->slug ? 'primary' : 'secondary' }} text-decoration-none me-1">{{ $t->name }}</a>
        @endforeach
    </div>
    @endif

    <div class="row align-items-center mb-3">
        <div class="col-md-6 mb-2 mb-md-0">
            <span class="text-muted">{{ $courses->total() }} course(s)</span>
        </div>
        <div class="col-md-6 text-md-end">
            <form action="{{ route('courses.index') }}" method="get" class="d-inline">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="tag" value="{{ request('tag') }}">
                <label class="me-2">Order:</label>
                <select name="order" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                    <option value="newest" {{ request('order') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="alphabetical" {{ request('order') === 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                    <option value="popular" {{ request('order') === 'popular' ? 'selected' : '' }}>Most Members</option>
                </select>
            </form>
        </div>
    </div>

    @php $enrolledIds = $enrolledCourseIds ?? collect(); @endphp
    @if($courses->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        @if(request('q') || request('tag'))
            No courses match @if(request('q')) &lsquo;{{ e(request('q')) }}&rsquo; @endif @if(request('tag')) tag &lsquo;{{ e(request('tag')) }}&rsquo; @endif. <a href="{{ route('courses.index') }}" class="alert-link">Clear filters</a>
        @else
            No courses available yet. Check back soon or <a href="{{ route('courses.create') }}" class="alert-link">create one</a> if you're an instructor.
        @endif
    </div>
    @else
    <ul class="list-unstyled row g-4">
        @foreach($courses as $c)
        @php
            $locked = !$enrolledIds->contains($c->id);
        @endphp
        <li class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm {{ $locked ? 'border-secondary' : '' }}" style="{{ $locked ? 'opacity: 0.92;' : '' }}">
                @if($c->featured_image)
                <div class="position-relative">
                    <img src="{{ asset('storage/' . $c->featured_image) }}" class="card-img-top" alt="" style="height:160px;object-fit:cover;">
                    @if($locked)
                    <span class="position-absolute top-0 end-0 m-2 rounded-circle bg-dark bg-opacity-75 p-1" title="Enroll to unlock">
                        <i class="bi bi-lock-fill text-white"></i>
                    </span>
                    @endif
                </div>
                @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center position-relative" style="height:160px;">
                    @if($locked)
                    <span class="position-absolute top-0 end-0 m-2 rounded-circle bg-dark bg-opacity-75 p-1" title="Enroll to unlock">
                        <i class="bi bi-lock-fill text-white"></i>
                    </span>
                    @endif
                    <i class="bi bi-journal-text text-white display-4"></i>
                </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">
                        @if($locked)
                        <span class="text-dark">{{ $c->title }}</span>
                        <span class="text-muted small fw-normal d-block"><i class="bi bi-lock me-1"></i> Locked — enroll to unlock</span>
                        @else
                        <a href="{{ route('courses.show', $c) }}" class="text-decoration-none text-dark">{{ $c->title }}</a>
                        @endif
                    </h5>
                    <p class="card-text text-muted small flex-grow-1">{{ Str::limit($c->short_description ?? $c->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                        <small class="text-muted">{{ $c->enrollment_count }} enrolled · {{ $c->instructor->name ?? '—' }}</small>
                        @if($locked)
                        @guest
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Login to enroll</a>
                        @else
                        <a href="{{ route('courses.show', $c) }}" class="btn btn-sm btn-primary">Enroll to unlock</a>
                        @endguest
                        @else
                        <a href="{{ route('courses.show', $c) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @endif
                    </div>
                </div>
            </div>
        </li>
        @endforeach
    </ul>
    <div class="d-flex justify-content-center mt-4">
        {{ $courses->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
