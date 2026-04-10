@extends('layouts.app')

@section('title', 'My notes')

@section('content')
<div class="container py-4">
    <h1 class="h2 mb-1">My notes</h1>
    <p class="text-muted mb-4">Your personal notes across courses and modules.</p>

    <form method="get" class="row g-2 align-items-center mb-3">
        <div class="col-md-6">
            <label for="q" class="visually-hidden">Search notes</label>
            <input
                type="search"
                name="q"
                id="q"
                class="form-control"
                value="{{ $search }}"
                placeholder="Search by course, module, or note text..."
            >
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search me-1"></i>Search
            </button>
        </div>
    </form>

    @if($notes->isEmpty())
    <div class="alert alert-info mb-0">
        <i class="bi bi-journal-text me-1"></i>
        You don't have any notes yet. Open a course and use the <strong>My notes</strong> box in the learn view to start capturing your thoughts.
    </div>
    @else
    <div class="list-group mb-3">
        @foreach($notes as $note)
        <a
            href="{{ route('learn.show', ['course' => $note->course, 'unit' => $note->unit_id]) }}"
            class="list-group-item list-group-item-action"
        >
            <div class="d-flex w-100 justify-content-between">
                <div class="me-3">
                    <h5 class="mb-1 text-truncate">{{ $note->course->title ?? 'Course' }}</h5>
                    <p class="mb-1 text-truncate">
                        <span class="badge bg-light text-secondary border me-1">
                            {{ $note->unit->title ?? 'Module' }}
                        </span>
                        <span class="text-muted small">
                            {{ \Illuminate\Support\Str::limit(strip_tags($note->body), 120) }}
                        </span>
                    </p>
                </div>
                <small class="text-muted text-nowrap ms-auto">
                    {{ $note->updated_at?->diffForHumans() }}
                </small>
            </div>
        </a>
        @endforeach
    </div>

    {{ $notes->links() }}
    @endif
</div>
@endsection

