@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $course->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <h1 class="h2">{{ $course->title }}</h1>
            @if($course->short_description)
            <p class="lead text-muted">{{ $course->short_description }}</p>
            @endif
            <p class="text-muted small">
                <i class="bi bi-people"></i> {{ $course->enrollment_count }} students enrolled
                · <i class="bi bi-person"></i> {{ $course->instructor?->name ?? 'Facilitator to be assigned' }}
                @if($course->rating_count > 0)
                · <span class="text-warning">{{ str_repeat('★', (int) round($course->rating)) }}{{ str_repeat('☆', 5 - (int) round($course->rating)) }}</span> {{ number_format($course->rating, 1) }} ({{ $course->rating_count }})
                @endif
            </p>

            <div class="mt-4">
                <h3 class="h5">Course Description</h3>
                <div class="course-description">
                    {!! nl2br(e($course->description ?? 'No description.')) !!}
                </div>
            </div>

            <h3 class="h5 mt-4">Course Curriculum</h3>
            <div class="course-curriculum table-responsive">
                <table class="table table-sm">
                    <tbody>
                    @foreach($course->curriculum as $item)
                    <tr>
                        <td class="text-muted" style="width:40px;"><i class="{{ $item['icon'] ?? 'bi bi-file-text' }}"></i></td>
                        <td>{{ $item['title'] }}</td>
                        <td class="text-muted small">{{ $item['duration'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if($course->curriculum->isEmpty())
            <p class="text-muted">No curriculum yet.</p>
            @endif

            <h3 class="h5 mt-4">Reviews</h3>
            @if($course->rating_count > 0)
            <p class="text-muted small">
                <span class="text-warning">{{ str_repeat('★', (int) round($course->rating)) }}{{ str_repeat('☆', 5 - (int) round($course->rating)) }}</span>
                {{ number_format($course->rating, 1) }} · {{ $course->rating_count }} rating{{ $course->rating_count !== 1 ? 's' : '' }}
            </p>
            @endif
            @auth
            @if($enrolled && !$myReview)
            <form action="{{ route('courses.reviews.store', $course) }}" method="post" class="mb-4">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Your rating</label>
                    <div>
                        @foreach([1,2,3,4,5] as $s)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rating" id="r{{ $s }}" value="{{ $s }}" {{ (int) old('rating') === $s ? 'checked' : '' }} required>
                            <label class="form-check-label" for="r{{ $s }}">{{ $s }} ★</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-2">
                    <label for="review_text" class="form-label">Review (optional)</label>
                    <textarea name="review" id="review_text" class="form-control" rows="3" maxlength="2000">{{ old('review') }}</textarea>
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm">Submit review</button>
            </form>
            @elseif($myReview)
            <p class="text-muted small mb-2">Your rating: <span class="text-warning">{{ str_repeat('★', $myReview->rating) }}{{ str_repeat('☆', 5 - $myReview->rating) }}</span></p>
            @if($myReview->review)<p class="small">{{ $myReview->review }}</p>@endif
            @endif
            @endauth
            @if($reviews->isNotEmpty())
            <ul class="list-unstyled">
                @foreach($reviews as $rev)
                <li class="mb-3 pb-3 border-bottom">
                    <span class="text-warning">{{ str_repeat('★', $rev->rating) }}{{ str_repeat('☆', 5 - $rev->rating) }}</span>
                    <strong>{{ $rev->user->name ?? 'User' }}</strong>
                    <span class="text-muted small">{{ $rev->created_at?->format('d M Y') }}</span>
                    @if($rev->review)<p class="mb-0 small mt-1">{{ $rev->review }}</p>@endif
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted small">No reviews yet.</p>
            @endif
        </div>
        <div class="col-lg-4">
            @if($course->instructor)
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Your facilitator</h6>
                </div>
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
                        <i class="bi bi-person fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <strong>{{ $course->instructor->name }}</strong>
                        <p class="small mb-0 mt-1">This course is facilitated by <strong>{{ $course->instructor->name }}</strong>. Delegates will know who is leading the course.</p>
                    </div>
                </div>
                @if($enrolled && $enrollment && (int) $enrollment->progress_percentage >= 100)
                <div class="card-footer bg-transparent border-top pt-2">
                    @if($hasRated && $facilitatorRating)
                    <p class="small mb-0 text-muted"><span class="text-warning">{{ str_repeat('★', $facilitatorRating->rating) }}{{ str_repeat('☆', 5 - $facilitatorRating->rating) }}</span> You rated your facilitator. @if($facilitatorRating->review)<br><em>{{ $facilitatorRating->review }}</em>@endif</p>
                    @else
                    <form action="{{ route('courses.rate-facilitator', $course) }}" method="post">
                        @csrf
                        <label class="form-label small mb-1">Rate how your facilitator taught this course</label>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            @foreach([1,2,3,4,5] as $s)
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="rating" id="fr{{ $s }}" value="{{ $s }}" {{ (int) old('rating') === $s ? 'checked' : '' }} required>
                                <label class="form-check-label small" for="fr{{ $s }}">{{ $s }} ★</label>
                            </div>
                            @endforeach
                        </div>
                        <textarea name="review" class="form-control form-control-sm mt-2" rows="2" placeholder="Optional feedback" maxlength="2000">{{ old('review') }}</textarea>
                        <button type="submit" class="btn btn-sm btn-outline-primary mt-2">Submit rating</button>
                    </form>
                    @endif
                </div>
                @endif
            </div>
            @endif

            @if($enrolled && $enrollment && (int) $enrollment->progress_percentage >= 100)
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Course evaluation</h6>
                </div>
                <div class="card-body">
                    @if($courseEvaluation)
                    <p class="small mb-1">
                        Your overall rating:
                        <span class="text-warning">
                            {{ str_repeat('★', (int) $courseEvaluation->rating) }}{{ str_repeat('☆', 5 - (int) $courseEvaluation->rating) }}
                        </span>
                    </p>
                    @if($courseEvaluation->difficulty)
                    <p class="small mb-1">
                        Difficulty: {{ $courseEvaluation->difficulty }}/5
                    </p>
                    @endif
                    <p class="small mb-1">
                        Would you recommend this course?
                        <strong>{{ $courseEvaluation->would_recommend ? 'Yes' : 'No' }}</strong>
                    </p>
                    @if($courseEvaluation->comments)
                    <p class="small mb-0 mt-1"><strong>Your comments:</strong> {{ $courseEvaluation->comments }}</p>
                    @else
                    <p class="small mb-0 mt-1 text-muted">Thank you for your evaluation. You can update it below.</p>
                    @endif
                    <hr>
                    @endif
                    <form action="{{ route('courses.evaluate', $course) }}" method="post" class="small">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label mb-1">Overall, how satisfied were you with this course?</label>
                            <div>
                                @foreach([1,2,3,4,5] as $s)
                                <div class="form-check form-check-inline mb-0">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="rating"
                                        id="ce_rating_{{ $s }}"
                                        value="{{ $s }}"
                                        {{ (int) old('rating', $courseEvaluation->rating ?? 5) === $s ? 'checked' : '' }}
                                        required
                                    >
                                    <label class="form-check-label" for="ce_rating_{{ $s }}">{{ $s }} ★</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">How challenging was this course?</label>
                            <select name="difficulty" class="form-select form-select-sm">
                                <option value="">Select…</option>
                                @foreach([1 => 'Too easy', 2 => 'Easy', 3 => 'About right', 4 => 'Challenging', 5 => 'Very challenging'] as $val => $label)
                                <option value="{{ $val }}" {{ (int) old('difficulty', $courseEvaluation->difficulty ?? 3) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2 form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                id="ce_recommend"
                                name="would_recommend"
                                {{ old('would_recommend', $courseEvaluation->would_recommend ?? true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="ce_recommend">
                                I would recommend this course to a colleague.
                            </label>
                        </div>
                        <div class="mb-2">
                            <label for="ce_comments" class="form-label mb-1">Any comments to help us improve?</label>
                            <textarea
                                name="comments"
                                id="ce_comments"
                                class="form-control form-control-sm"
                                rows="2"
                                maxlength="2000"
                            >{{ old('comments', $courseEvaluation->comments ?? '') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            {{ $courseEvaluation ? 'Update evaluation' : 'Submit evaluation' }}
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="card shadow-sm sticky-top" style="top: 1rem;">
                @if($course->featured_image)
                <img src="{{ asset('storage/' . $course->featured_image) }}" class="card-img-top" alt="">
                @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height:180px;">
                    <i class="bi bi-journal-text text-white display-3"></i>
                </div>
                @endif
                <div class="card-body">
                    @if(isset($canEditThisCourse) && $canEditThisCourse)
                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-primary w-100 mb-2"><i class="bi bi-pencil me-1"></i> Edit course</a>
                    @endif
                    @auth
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('courses.attendance', $course) }}" class="btn btn-outline-secondary w-100 mb-2"><i class="bi bi-person-lines-fill me-1"></i> Attendance register</a>
                    @endif
                    @endauth
                    @if($enrolled)
                    <a href="{{ route('learn.show', ['course' => $course, 'start' => 1]) }}" class="btn btn-primary w-100 {{ !empty($certificate) ? 'mb-2' : '' }}">Continue Learning</a>
                    @if(!empty($certificate))
                    <a href="{{ route('certificates.show', $certificate) }}" class="btn btn-outline-success w-100"><i class="bi bi-award me-1"></i> View certificate</a>
                    @endif
                    @else
                    <p class="text-muted small mb-2"><i class="bi bi-lock me-1"></i> This course is locked. Enroll to unlock.</p>
                    @auth
                    <form action="{{ route('courses.enroll', $course) }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">Enroll to unlock</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">Login to Enroll</a>
                    @endauth
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
