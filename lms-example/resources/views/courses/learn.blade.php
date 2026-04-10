@extends('layouts.learn')

@section('title', $course->title . ' – Learn')

@section('learn-back')
<a href="{{ route('courses.show', $course) }}" class="text-light text-decoration-none small me-3">Back to course</a>
@endsection

@section('sidebar')
<aside class="learn-sidebar" id="learn-sidebar">
    <div class="learn-course-hero" @if($course->featured_image) style="background-image: url('{{ asset('storage/' . $course->featured_image) }}')" @endif>
        <h2>{{ $course->title }}</h2>
        <div class="learn-progress-pct">{{ $enrollment->progress_percentage ?? 0 }}% COMPLETE</div>
        <div class="learn-progress-bar mt-1">
            <div class="fill" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
        </div>
    </div>
    <nav class="py-2">
        @php
            $days = $structuredCurriculum['days'] ?? [];
            $trailing = $structuredCurriculum['trailing'] ?? collect();
            $currentId = $current ? (int) $current->id : null;
            $unlocked = $unlockedUnitIds ?? collect();
        @endphp
        @foreach([1, 2, 3] as $d)
        @php
            $day = $days[$d] ?? null;
            if (!$day) continue;
            $standalones = $day['standalones'] ?? collect();
            $modules = $day['modules'] ?? [];
            ksort($modules);
        @endphp
        <div class="learn-day" data-day="{{ $d }}">
            <div class="learn-day-header">DAY {{ $d }}</div>
            @foreach($standalones as $item)
            @php
                $active = $current && (int) $current->id === (int) $item['id'];
                $done = isset($progress[$item['id']]) && $progress[$item['id']]->completed_at;
                $locked = !$unlocked->contains($item['id']);
            @endphp
            @if($locked)
            <div class="learn-nav-item learn-nav-item-locked" title="Pass the previous module's Knowledge Check to unlock">
                <span class="icon"><i class="bi bi-lock"></i></span>
                <span class="flex-grow-1 text-truncate">{{ $item['title'] }}</span>
                <span class="circle"></span>
            </div>
            @else
            <a href="{{ route('learn.show', ['course' => $course, 'unit' => $item['id']]) }}"
               class="learn-nav-item {{ $active ? 'active' : '' }} {{ $done ? 'done' : '' }}">
                <span class="icon"><i class="bi {{ $item['icon'] ?? 'bi-file-text' }}"></i></span>
                <span class="flex-grow-1 text-truncate">{{ $item['title'] }}</span>
                <span class="circle">{!! $done ? '<i class="bi bi-check"></i>' : '' !!}</span>
            </a>
            @endif
            @endforeach
            @foreach($modules as $modIndex => $moduleItems)
            @php
                $hasActive = $currentId && $moduleItems->contains(fn ($i) => (int) $i['id'] === $currentId);
                $expandFirst = !$currentId && $d === 1 && $modIndex === array_key_first($modules);
                $isExpanded = $hasActive || $expandFirst;
            @endphp
            <div class="learn-module {{ $isExpanded ? '' : 'collapsed' }}" data-module="{{ $modIndex }}">
                <div class="learn-module-header" role="button" tabindex="0" aria-expanded="{{ $isExpanded ? 'true' : 'false' }}">
                    <i class="bi bi-chevron-down learn-module-caret"></i>
                    <span>MODULE {{ $modIndex }}</span>
                </div>
                <div class="learn-module-lessons">
                    @foreach($moduleItems as $item)
                    @php
                        $active = $current && (int) $current->id === (int) $item['id'];
                        $done = isset($progress[$item['id']]) && $progress[$item['id']]->completed_at;
                        $locked = !$unlocked->contains($item['id']);
                    @endphp
                    @if($locked)
                    <div class="learn-nav-item learn-nav-item-locked" title="Pass the previous module's Knowledge Check to unlock">
                        <span class="icon"><i class="bi bi-lock"></i></span>
                        <span class="flex-grow-1 text-truncate">{{ $item['title'] }}</span>
                        <span class="circle"></span>
                    </div>
                    @else
                    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $item['id']]) }}"
                       class="learn-nav-item {{ $active ? 'active' : '' }} {{ $done ? 'done' : '' }}">
                        <span class="icon"><i class="bi {{ $item['icon'] ?? 'bi-file-text' }}"></i></span>
                        <span class="flex-grow-1 text-truncate">{{ $item['title'] }}</span>
                        <span class="circle">{!! $done ? '<i class="bi bi-check"></i>' : '' !!}</span>
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
        @if($trailing->isNotEmpty())
        <div class="learn-trailing">
            @foreach($trailing as $item)
            @php
                $active = $current && (int) $current->id === (int) $item['id'];
                $done = isset($progress[$item['id']]) && $progress[$item['id']]->completed_at;
                $locked = !$unlocked->contains($item['id']);
            @endphp
            @if($locked)
            <div class="learn-nav-item learn-nav-item-locked" title="Pass the previous module's Knowledge Check to unlock">
                <span class="icon"><i class="bi bi-lock"></i></span>
                <span class="flex-grow-1 text-truncate">{{ $item['title'] }}</span>
                <span class="circle"></span>
            </div>
            @else
            <a href="{{ route('learn.show', ['course' => $course, 'unit' => $item['id']]) }}"
               class="learn-nav-item {{ $active ? 'active' : '' }} {{ $done ? 'done' : '' }}">
                <span class="icon"><i class="bi {{ $item['icon'] ?? 'bi-file-text' }}"></i></span>
                <span class="flex-grow-1 text-truncate">{{ $item['title'] }}</span>
                <span class="circle">{!! $done ? '<i class="bi bi-check"></i>' : '' !!}</span>
            </a>
            @endif
            @endforeach
        </div>
        @endif
    </nav>
</aside>
@endsection

@section('content')
<div class="learn-content-wrap">
    @if(!$current)
    <div class="text-center py-5">
        <p class="text-muted mb-3">Select a lesson from the sidebar to begin.</p>
        @if($totalUnits > 0)
        <a href="{{ route('learn.show', ['course' => $course, 'start' => 1]) }}" class="btn btn-danger">Start course</a>
        @else
        <p class="text-muted small">No lessons in this course yet.</p>
        @endif
    </div>
    @elseif($current->unit_type === 'quiz' || $current->unit_type === 'assignment')
    <p class="learn-meta">Unit {{ $currentIndex }} of {{ $totalUnits }}</p>
    <h1 class="learn-title">{{ $current->title }}</h1>
    <div class="learn-accent"></div>
    @if(!empty($currentLocked))
    <div class="alert alert-warning"><i class="bi bi-lock me-2"></i>This module is locked. Pass the previous module's Knowledge Check to unlock it.</div>
    @elseif($current->unit_type === 'assignment')
    @if($assignmentSubmission)
    <div class="alert alert-{{ $assignmentSubmission->isGraded() ? 'success' : 'info' }} mb-4">
        @if($assignmentSubmission->isGraded())
        <i class="bi bi-check-circle me-2"></i>Graded: {{ $assignmentSubmission->score }}/{{ $assignmentSubmission->max_points }}.
        @if($assignmentSubmission->instructor_feedback)
        <div class="mt-2"><strong>Feedback:</strong> {{ $assignmentSubmission->instructor_feedback }}</div>
        @endif
        @else
        <i class="bi bi-send me-2"></i>Your assignment has been submitted. Your instructor will grade it.
        @endif
    </div>
    @if(!empty($assignmentSubmission->attachments))
    <p class="text-muted small mb-2">Attachments:</p>
    <ul class="list-unstyled mb-3">
        @foreach($assignmentSubmission->attachments as $att)
        <li><a href="{{ asset('storage/' . ($att['path'] ?? '')) }}" target="_blank" rel="noopener">{{ $att['name'] ?? 'File' }}</a></li>
        @endforeach
    </ul>
    @endif
    @if($nextUnit && !$nextLocked)
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $next]) }}" class="btn btn-outline-primary">Continue to next &rarr;</a>
    @elseif($currentIndex >= $totalUnits)
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $current->id, 'finished' => 1]) }}" class="btn btn-success me-2">View course complete</a>
    <a href="{{ route('courses.my') }}" class="btn btn-outline-secondary">My courses</a>
    @endif
    @elseif($assignment)
    <div class="learn-body mb-4">
        @if($assignment->instructions){!! nl2br(e($assignment->instructions)) !!}@endif
        <p class="text-muted small mt-2">Max {{ $assignment->max_points }} points. @if($assignment->allow_file_upload) You may upload up to 5 files (PDF, DOC, DOCX, TXT, JPG, PNG; max 5 MB each). @endif</p>
    </div>
    <form action="{{ route('learn.assignment.submit', [$course, $current]) }}" method="post" enctype="multipart/form-data" class="learn-assignment-form">
        @csrf
        @if($errors->any())
        <div class="alert alert-warning mb-3">{{ $errors->first() }}</div>
        @endif
        <div class="mb-3">
            <label for="assign_content" class="form-label">Your response <span class="text-danger">*</span></label>
            <textarea name="content" id="assign_content" class="form-control" rows="8" required>{{ old('content') }}</textarea>
        </div>
        @if($assignment->allow_file_upload)
        <div class="mb-3">
            <label for="assign_files" class="form-label">Attachments (optional)</label>
            <input type="file" name="attachments[]" id="assign_files" class="form-control" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
        </div>
        @endif
        <button type="submit" class="btn btn-danger">Submit assignment</button>
    </form>
    @else
    <div class="alert alert-info">This assignment is not configured yet.</div>
    @endif
    @elseif(!empty($showQuizResults) && !empty($quizResultsAttempt))
    @php
        $attempt = $quizResultsAttempt;
        $passed = $attempt->status === 'passed';
        $ans = $attempt->answers ?? [];
    @endphp
    <div class="alert {{ $passed ? 'alert-success' : 'alert-warning' }} mb-4">
        <i class="bi {{ $passed ? 'bi-check-circle' : 'bi-exclamation-circle' }} me-2"></i>
        {{ $passed ? 'Knowledge Check passed!' : 'Knowledge Check not passed.' }} You scored {{ $attempt->score }}/{{ $attempt->total_points }} ({{ number_format($attempt->percentage, 1) }}%).
        @if($nextUnit && !$nextLocked && $passed) The next module is unlocked. @elseif($passed && !$nextUnit) This was the final Knowledge Check—you've completed the course! @endif
    </div>
    <h3 class="h6 mb-2">Your answers</h3>
    <div class="mb-4">
        @foreach($quiz->questions as $i => $q)
        @php
            $a = $ans[$q->id] ?? null;
            $correct = $a['correct'] ?? false;
            $val = $a['value'] ?? null;
        @endphp
        <div class="card mb-2">
            <div class="card-body py-2 {{ $correct ? 'border-start border-4 border-success' : 'border-start border-4 border-danger' }}">
                <div class="d-flex align-items-start">
                    <span class="me-2">{{ $correct ? '✓' : '✗' }}</span>
                    <div>
                        <strong>{{ $i + 1 }}. {{ $q->question }}</strong>
                        @php
                            $display = $val ?? '—';
                            if ($q->type === 'true_false') {
                                $display = $val === '1' ? 'True' : ($val === '0' ? 'False' : $display);
                            } elseif ($val !== null && $val !== '' && !empty($q->options) && is_array($q->options)) {
                                foreach ($q->options as $o) {
                                    if (($o['value'] ?? '') === $val) { $display = $o['text'] ?? $val; break; }
                                }
                            }
                        @endphp
                        <p class="mb-0 small text-muted">Your answer: {{ $display }}</p>
                        @if(!$correct && $q->explanation)<p class="mb-0 small mt-1">{{ $q->explanation }}</p>@endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @if($passed)
    @if($nextUnit && !$nextLocked)
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $next]) }}" class="btn btn-primary">Continue to next &rarr;</a>
    @else
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $current->id, 'finished' => 1]) }}" class="btn btn-success me-2">View course complete</a>
    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary me-2">Back to course</a>
    <a href="{{ route('courses.my') }}" class="btn btn-outline-secondary">My courses</a>
    @endif
    @else
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $current->id]) }}" class="btn btn-outline-primary">Try again</a>
    @endif
    @elseif(!empty($quizPassed))
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>You have passed this Knowledge Check. @if($nextUnit && !$nextLocked) The next module is unlocked. @else This was the final Knowledge Check—you've completed the course! @endif</div>
    @if($nextUnit && !$nextLocked)
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $next]) }}" class="btn btn-outline-primary mt-2">Continue to next &rarr;</a>
    @else
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $current->id, 'finished' => 1]) }}" class="btn btn-success mt-2 me-2">View course complete</a>
    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary mt-2 me-2">Back to course</a>
    <a href="{{ route('courses.my') }}" class="btn btn-outline-secondary mt-2">My courses</a>
    @endif
    @elseif($quiz && $quiz->questions->isNotEmpty())
    <div class="learn-body mb-4">
        @if($quiz->instructions)<p>{{ $quiz->instructions }}</p>@endif
        <p class="text-muted small mt-2">Pass mark: {{ $quiz->pass_percentage }}%. You may retake the Knowledge Check if you do not pass.</p>
    </div>
    <form action="{{ route('learn.quiz.submit', [$course, $current]) }}" method="post" class="learn-quiz-form">
        @csrf
        @if($errors->any())
        <div class="alert alert-warning mb-3">Please answer all questions.</div>
        @endif
        <div class="learn-quiz-questions">
            @foreach($quiz->questions as $i => $q)
            <div class="card mb-3 learn-quiz-q">
                <div class="card-body">
                    <p class="fw-semibold mb-3">{{ $i + 1 }}. {{ $q->question }}</p>
                    @if($q->type === 'true_false')
                    <div class="learn-quiz-opts">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" id="q{{ $q->id }}_t" value="1" required>
                            <label class="form-check-label" for="q{{ $q->id }}_t">True</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" id="q{{ $q->id }}_f" value="0">
                            <label class="form-check-label" for="q{{ $q->id }}_f">False</label>
                        </div>
                    </div>
                    @elseif($q->type === 'multiple_choice' && !empty($q->options))
                    <div class="learn-quiz-opts">
                        @foreach($q->options as $opt)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" id="q{{ $q->id }}_v{{ $loop->index }}" value="{{ $opt['value'] ?? '' }}" required>
                            <label class="form-check-label" for="q{{ $q->id }}_v{{ $loop->index }}">{{ $opt['text'] ?? '' }}</label>
                        </div>
                        @endforeach
                    </div>
                    @elseif($q->type === 'short_answer')
                    <div class="learn-quiz-opts">
                        <input type="text" class="form-control" name="answers[{{ $q->id }}]" id="q{{ $q->id }}_sa" value="{{ old('answers.'.$q->id) }}" required placeholder="Your answer" maxlength="500">
                    </div>
                    @endif
                    <small class="text-muted">{{ $q->points }} point{{ $q->points !== 1 ? 's' : '' }}</small>
                </div>
            </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-danger learn-btn-complete"><i class="bi bi-send me-1"></i> Submit Knowledge Check</button>
    </form>
    @else
    <div class="alert alert-info">No questions in this Knowledge Check yet.</div>
    @endif
    @else
    <p class="learn-meta">Lesson {{ $currentIndex }} of {{ $totalUnits }}</p>
    <h1 class="learn-title">{{ $current->title }}</h1>
    <div class="learn-accent"></div>
    @if($current->duration)
    <p class="text-muted small mb-3"><i class="bi bi-clock me-1"></i>{{ (int) $current->duration }} min</p>
    @endif
    <div class="learn-body">
        @php
            $body = $current->content ?: '<p class="text-muted">No content.</p>';
            $body = str_replace('**Time Slot:**', 'Time Slot:', $body);
        @endphp
        @if(!empty($steps) && count($steps) > 0)
        <div class="learn-stepper" data-total="{{ count($steps) }}">
            <div class="learn-stepper-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <span class="text-muted small">Step <span class="learn-step-num">1</span> of {{ count($steps) }}</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm learn-step-prev" disabled><i class="bi bi-chevron-left me-1"></i> Previous</button>
                    <button type="button" class="btn btn-primary btn-sm learn-step-next"><span class="learn-step-next-text">Next</span> <i class="bi bi-chevron-right ms-1"></i></button>
                </div>
            </div>
            @foreach($steps as $i => $s)
            <div class="learn-step-panel" data-step="{{ $i + 1 }}" style="display:{{ $i === 0 ? 'block' : 'none' }};">
                {!! $s['content'] !!}
            </div>
            @endforeach
            <p class="learn-step-done-hint text-muted small mt-3" id="learn-step-done-hint" style="display:none;">You've seen all steps. Mark as finished and we proceed to the next lesson.</p>
        </div>
        @push('scripts')
        <script>
        (function(){
            var st = document.querySelector('.learn-stepper');
            if (!st) return;
            var panels = st.querySelectorAll('.learn-step-panel');
            var num = st.querySelector('.learn-step-num');
            var prev = st.querySelector('.learn-step-prev');
            var next = st.querySelector('.learn-step-next');
            var txt = st.querySelector('.learn-step-next-text');
            var hint = st.querySelector('.learn-step-done-hint');
            var markWrap = document.getElementById('learn-mark-finished-wrap');
            var total = panels.length;
            var cur = 1;
            function go(n) {
                if (n < 1 || n > total) return;
                cur = n;
                panels.forEach(function(p, i){ p.style.display = (i+1) === cur ? 'block' : 'none'; });
                if (num) num.textContent = cur;
                if (prev) prev.disabled = cur <= 1;
                if (next && txt) {
                    if (cur >= total) { next.disabled = true; next.classList.add('btn-outline-secondary'); next.classList.remove('btn-primary'); txt.textContent = 'Finish'; } else { next.disabled = false; next.classList.remove('btn-outline-secondary'); next.classList.add('btn-primary'); txt.textContent = 'Next'; }
                }
                if (hint) hint.style.display = (cur >= total) ? 'block' : 'none';
                if (markWrap) markWrap.style.display = (cur >= total) ? 'block' : 'none';
            }
            if (prev) prev.addEventListener('click', function(){ go(cur-1); });
            if (next) next.addEventListener('click', function(){ if (cur < total) go(cur+1); });
            go(1);
        })();
        </script>
        @endpush
        @else
        {!! $body !!}
        @endif
    </div>

    @if($current)
    <div class="card mt-4">
        <div class="card-header">
            <i class="bi bi-pencil-square me-2"></i>My notes
        </div>
        <div class="card-body">
            <form action="{{ route('learn.notes.store', [$course, $current]) }}" method="post" id="learn-notes-form">
                @csrf
                <div class="mb-2">
                    <textarea
                        name="body"
                        rows="5"
                        class="form-control"
                        placeholder="Write your personal notes for this module (only you can see these)..."
                        maxlength="20000"
                    >{{ old('body', $currentNote->body ?? '') }}</textarea>
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-save me-1"></i>Save notes
                </button>
                @if(!empty($currentNote))
                <small class="text-muted ms-2" id="learn-notes-status">
                    Last updated {{ $currentNote->updated_at?->diffForHumans() }}
                </small>
                @else
                <small class="text-muted ms-2" id="learn-notes-status"></small>
                @endif
            </form>
            <p class="text-muted small mb-0 mt-2">
                <i class="bi bi-info-circle me-1"></i>Notes autosave as you type. Only you can see these notes. Clear the text and save to remove them.
            </p>
        </div>
    </div>
    @endif

    @if(!empty($showAttendanceRegister) && $current)
    <div class="learn-attendance mt-4 mb-4">
        @if(!empty($attendanceSubmitted))
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Your attendance has been recorded.</div>
        @else
        <h3 class="h5 mb-3">Attendance register</h3>
        <p class="text-muted small mb-3">Please mark your attendance for this session.</p>
        <form action="{{ route('learn.attendance.store', $course) }}" method="post" class="learn-attendance-form">
            @csrf
            <input type="hidden" name="unit_id" value="{{ $current->id }}">
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-6">
                    <label for="att_title" class="form-label">Title</label>
                    <input type="text" class="form-control form-control-sm" id="att_title" name="title" value="{{ old('title') }}" placeholder="e.g. Mr, Ms, Dr">
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="att_name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_surname" class="form-label">Surname <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm @error('surname') is-invalid @enderror" id="att_surname" name="surname" value="{{ old('surname', auth()->user()->surname ?? '') }}" required>
                    @error('surname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_designation" class="form-label">Designation</label>
                    <input type="text" class="form-control form-control-sm" id="att_designation" name="designation" value="{{ old('designation') }}" placeholder="e.g. Manager, Director">
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_organisation" class="form-label">Organisation</label>
                    <input type="text" class="form-control form-control-sm" id="att_organisation" name="organisation" value="{{ old('organisation') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_contact" class="form-label">Contact number</label>
                    <input type="text" class="form-control form-control-sm" id="att_contact" name="contact_number" value="{{ old('contact_number') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="att_email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-person-check me-1"></i> Mark attendance</button>
        </form>
        @endif
    </div>
    @endif

    @if(!empty($courseFinished))
    <div class="alert alert-success mb-4">
        <h2 class="h5 mb-2"><i class="bi bi-trophy me-2"></i>You've completed the course!</h2>
        <p class="mb-0">Congratulations on finishing all lessons. You can revisit any lesson from the sidebar or return to your courses.</p>
        @if(!empty($certificate))
        <a href="{{ route('certificates.show', $certificate) }}" class="btn btn-success mt-3 me-2"><i class="bi bi-award me-1"></i> View certificate</a>
        @endif
        <a href="{{ route('courses.show', $course) }}" class="btn {{ $certificate ?? null ? 'btn-outline-success' : 'btn-success' }} mt-3 me-2">Back to course</a>
        <a href="{{ route('courses.my') }}" class="btn btn-outline-success mt-3">My courses</a>
    </div>
    @endif

    @if($current->video_url)
    <div class="learn-media-wrap">
        @php
            $v = $current->video_url;
            if (preg_match('/youtube\.com\/watch\?v=([\w-]+)/', $v, $m)) { $embed = 'https://www.youtube.com/embed/' . $m[1]; }
            elseif (preg_match('/youtu\.be\/([\w-]+)/', $v, $m)) { $embed = 'https://www.youtube.com/embed/' . $m[1]; }
            elseif (preg_match('/vimeo\.com\/(\d+)/', $v, $m)) { $embed = 'https://player.vimeo.com/video/' . $m[1]; }
            else { $embed = null; }
        @endphp
        @if($embed)
        <iframe src="{{ $embed }}" height="400" allowfullscreen></iframe>
        @else
        <video controls src="{{ $current->video_url }}" class="w-100"></video>
        @endif
    </div>
    @endif
    @if($current->audio_url)
    <div class="learn-media-wrap p-3">
        <audio controls src="{{ $current->audio_url }}" class="w-100"></audio>
    </div>
    @endif
    @php $currentDone = isset($progress[$current->id]) && $progress[$current->id]->completed_at; @endphp
    @if(!$currentDone)
    <div id="learn-mark-finished-wrap" class="mt-4" @if(!empty($steps) && count($steps) > 0) style="display:none" @endif>
        <form action="{{ route('learn.unit.complete', [$course, $current]) }}" method="post">
            @csrf
            <button type="submit" class="learn-btn-complete"><i class="bi bi-check-lg me-1"></i> Mark as finished</button>
        </form>
    </div>
    @elseif(empty($courseFinished))
    <p class="text-muted mt-4 mb-0"><i class="bi bi-check-circle me-1"></i> You've marked this as finished. Proceed to the next lesson below or via the sidebar.</p>
    @endif
    @endif
</div>
@endsection

@section('bottom-bar')
@if($current)
<div class="learn-bottom-bar">
    @if($prevUnit)
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $prev]) }}" class="learn-bar-prev"><i class="bi bi-chevron-left me-1"></i> Previous</a>
    @else
    <span></span>
    @endif
    @if($nextUnit)
    @if($nextLocked ?? false)
    <span class="text-muted" title="Pass the current module's Knowledge Check to unlock">Next: {{ $nextUnit->title }} <i class="bi bi-lock"></i></span>
    @else
    <a href="{{ route('learn.show', ['course' => $course, 'unit' => $next]) }}" class="learn-bar-next">
        {{ $current->unit_type === 'quiz' ? 'Next' : 'Lesson ' . ($currentIndex + 1) }} – {{ $nextUnit->title }} <i class="bi bi-chevron-right"></i>
    </a>
    @endif
    @else
    <span></span>
    @endif
</div>
@endif
@endsection

@section('chat')
@include('courses._facilitator_chat_panel', [
    'course' => $course,
    'unitId' => $current->id ?? null,
    'isFacilitator' => $isFacilitator ?? false,
])
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('learn-notes-form');
    if (!form) return;

    const textarea = form.querySelector('textarea[name="body"]');
    const statusEl = document.getElementById('learn-notes-status');
    if (!textarea) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let timeoutId = null;
    let lastSentValue = textarea.value;

    function setStatus(text) {
        if (statusEl) {
            statusEl.textContent = text || '';
        }
    }

    async function autosave() {
        const body = textarea.value;
        if (body === lastSentValue) {
            return;
        }
        lastSentValue = body;
        setStatus('Saving…');
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                },
                body: new URLSearchParams({ body }),
            });
            if (!res.ok) {
                throw new Error('Failed');
            }
            const data = await res.json().catch(() => ({}));
            if (data.saved_at) {
                const d = new Date(data.saved_at);
                setStatus('Saved at ' + d.toLocaleTimeString());
            } else if (body.trim() === '') {
                setStatus('Notes cleared');
            } else {
                setStatus('Saved');
            }
        } catch (e) {
            setStatus('Could not autosave (check connection).');
        }
    }

    textarea.addEventListener('input', function () {
        setStatus('Saving…');
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(autosave, 1500);
    });

    textarea.addEventListener('blur', function () {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        autosave();
    });
})();
</script>
@endpush
