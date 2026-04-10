@extends('layouts.facilitator')

@section('title', 'Edit Knowledge Check: ' . ($quiz->title ?? $unit->title))

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.instructor') }}">Instructing</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ \Illuminate\Support\Str::limit($course->title, 35) }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.edit', $course) }}">Edit course</a></li>
            <li class="breadcrumb-item"><a href="{{ route('units.edit', [$course, $unit]) }}">Edit unit</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Knowledge Check</li>
        </ol>
    </nav>

    <h1 class="h2">Edit Knowledge Check</h1>
    <p class="text-muted">{{ $unit->title }}</p>

    <form action="{{ route('units.quiz.update', [$course, $unit]) }}" method="post" class="mb-4" id="quiz-edit-form">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">Settings</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="quiz_title" class="form-label">Knowledge Check title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('quiz_title') is-invalid @enderror" id="quiz_title" name="quiz_title"
                           value="{{ old('quiz_title', $quiz->title) }}" required>
                    @error('quiz_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="2">{{ old('instructions', $quiz->instructions) }}</textarea>
                    @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="pass_percentage" class="form-label">Pass mark (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('pass_percentage') is-invalid @enderror" id="pass_percentage" name="pass_percentage"
                               value="{{ old('pass_percentage', $quiz->pass_percentage ?? 70) }}" min="1" max="100" required>
                        @error('pass_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 d-flex align-items-end pb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="randomize_questions" id="randomize_questions" value="1"
                                   {{ old('randomize_questions', $quiz->randomize_questions) ? 'checked' : '' }}>
                            <label class="form-check-label" for="randomize_questions">Randomize question order</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Questions</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-question"><i class="bi bi-plus-lg me-1"></i>Add question</button>
            </div>
            <div class="card-body">
                <div id="questions-container">
                    @php
                        $qList = old('questions', $quiz->questions->map(function ($q) {
                            $type = $q->type ?? 'multiple_choice';
                            if ($type === 'true_false') {
                                $ca = (array)($q->correct_answers ?? []);
                                $correctIdx = (isset($ca[0]) && $ca[0] === '1') ? 0 : 1;
                                return [
                                    'question' => $q->question,
                                    'type' => 'true_false',
                                    'options' => [],
                                    'correct_index' => $correctIdx,
                                    'correct_text' => '',
                                    'points' => $q->points ?? 1,
                                ];
                            }
                            if ($type === 'short_answer') {
                                return [
                                    'question' => $q->question,
                                    'type' => 'short_answer',
                                    'options' => [],
                                    'correct_index' => 0,
                                    'correct_text' => implode("\n", (array)($q->correct_answers ?? [])),
                                    'points' => $q->points ?? 1,
                                ];
                            }
                            $idx = collect($q->options ?? [])->search(fn ($o) => in_array($o['value'] ?? null, (array)($q->correct_answers ?? []), true));
                            return [
                                'question' => $q->question,
                                'type' => 'multiple_choice',
                                'options' => array_map(fn ($o) => $o['text'] ?? '', $q->options ?? []),
                                'correct_index' => $idx !== false ? min(3, max(0, (int)$idx)) : 0,
                                'correct_text' => '',
                                'points' => $q->points ?? 1,
                            ];
                        })->values()->all());
                    @endphp
                    @foreach($qList as $qi => $q)
                    @php
                        $qType = $q['type'] ?? 'multiple_choice';
                        $opts = $q['options'] ?? [];
                        if (!is_array($opts)) { $opts = []; }
                        $opts = array_pad($opts, 4, '');
                        $correctIdx = (int)($q['correct_index'] ?? 0);
                        if ($qType === 'true_false') { if ($correctIdx !== 1) { $correctIdx = 0; } } elseif ($qType === 'short_answer') { $correctIdx = 0; } else { if ($correctIdx < 0 || $correctIdx >= 4) { $correctIdx = 0; } }
                        $correctText = $q['correct_text'] ?? '';
                    @endphp
                    <div class="question-block border rounded p-3 mb-3" data-index="{{ $qi }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong class="text-muted">Question {{ $qi + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-question" aria-label="Remove">×</button>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Question text <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="questions[{{ $qi }}][question]" value="{{ $q['question'] ?? '' }}" required placeholder="e.g. What is the main purpose of...">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Type</label>
                            <select class="form-select form-select-sm question-type-select" name="questions[{{ $qi }}][type]" style="max-width:180px" aria-label="Question type">
                                <option value="multiple_choice" {{ $qType === 'multiple_choice' ? 'selected' : '' }}>Multiple choice</option>
                                <option value="true_false" {{ $qType === 'true_false' ? 'selected' : '' }}>True / False</option>
                                <option value="short_answer" {{ $qType === 'short_answer' ? 'selected' : '' }}>Short answer</option>
                            </select>
                        </div>
                        <div class="mb-2 question-mc-opts" style="display:{{ in_array($qType, ['true_false','short_answer']) ? 'none' : 'block' }}">
                            <label class="form-label small mb-1">Options (at least 2; select the correct one)</label>
                            @foreach([0,1,2,3] as $oi)
                            <div class="input-group input-group-sm mb-1">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="questions[{{ $qi }}][correct_index]" value="{{ $oi }}" {{ $correctIdx === $oi ? 'checked' : '' }} {{ $qType === 'multiple_choice' ? 'required' : '' }}>
                                </div>
                                <input type="text" class="form-control" name="questions[{{ $qi }}][options][{{ $oi }}]" value="{{ $opts[$oi] ?? '' }}" placeholder="Option {{ $oi + 1 }}">
                            </div>
                            @endforeach
                        </div>
                        <div class="mb-2 question-tf-opts" style="display:{{ $qType === 'true_false' ? 'block' : 'none' }}">
                            <label class="form-label small mb-1">Correct answer</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="questions[{{ $qi }}][correct_index]" id="q{{ $qi }}_tf_t" value="0" {{ $correctIdx === 0 ? 'checked' : '' }} {{ $qType === 'true_false' ? 'required' : '' }}>
                                <label class="form-check-label" for="q{{ $qi }}_tf_t">True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="questions[{{ $qi }}][correct_index]" id="q{{ $qi }}_tf_f" value="1" {{ $correctIdx === 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="q{{ $qi }}_tf_f">False</label>
                            </div>
                        </div>
                        <div class="mb-2 question-sa-opts" style="display:{{ $qType === 'short_answer' ? 'block' : 'none' }}">
                            <label class="form-label small mb-1">Correct answer(s) <span class="text-muted">(one per line)</span></label>
                            <textarea class="form-control form-control-sm" name="questions[{{ $qi }}][correct_text]" rows="2" placeholder="e.g. Paris">{{ $correctText }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label small mb-1">Points</label>
                                <input type="number" class="form-control form-control-sm" name="questions[{{ $qi }}][points]" value="{{ $q['points'] ?? 1 }}" min="1" max="100">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if(empty($qList))
                <p class="text-muted small mb-0" id="no-questions-msg">No questions yet. Click <strong>Add question</strong> to create one.</p>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Knowledge Check</button>
            <a href="{{ route('units.edit', [$course, $unit]) }}" class="btn btn-outline-secondary">Back to unit</a>
            <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-secondary">Back to course</a>
        </div>
    </form>
</div>

<template id="question-tpl">
@verbatim
    <div class="question-block border rounded p-3 mb-3" data-index="{{ idx }}">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <strong class="text-muted">Question {{ idx1 }}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger remove-question" aria-label="Remove">×</button>
        </div>
        <div class="mb-2">
            <label class="form-label small mb-1">Question text <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" name="questions[{{ idx }}][question]" value="" required placeholder="e.g. What is the main purpose of...">
        </div>
        <div class="mb-2">
            <label class="form-label small mb-1">Type</label>
            <select class="form-select form-select-sm question-type-select" name="questions[{{ idx }}][type]" style="max-width:180px" aria-label="Question type">
                <option value="multiple_choice" selected>Multiple choice</option>
                <option value="true_false">True / False</option>
                <option value="short_answer">Short answer</option>
            </select>
        </div>
        <div class="mb-2 question-mc-opts">
            <label class="form-label small mb-1">Options (at least 2; select the correct one)</label>
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-text"><input class="form-check-input mt-0" type="radio" name="questions[{{ idx }}][correct_index]" value="0" required></div>
                <input type="text" class="form-control" name="questions[{{ idx }}][options][0]" value="" placeholder="Option 1">
            </div>
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-text"><input class="form-check-input mt-0" type="radio" name="questions[{{ idx }}][correct_index]" value="1"></div>
                <input type="text" class="form-control" name="questions[{{ idx }}][options][1]" value="" placeholder="Option 2">
            </div>
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-text"><input class="form-check-input mt-0" type="radio" name="questions[{{ idx }}][correct_index]" value="2"></div>
                <input type="text" class="form-control" name="questions[{{ idx }}][options][2]" value="" placeholder="Option 3">
            </div>
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-text"><input class="form-check-input mt-0" type="radio" name="questions[{{ idx }}][correct_index]" value="3"></div>
                <input type="text" class="form-control" name="questions[{{ idx }}][options][3]" value="" placeholder="Option 4">
            </div>
        </div>
        <div class="mb-2 question-tf-opts" style="display:none">
            <label class="form-label small mb-1">Correct answer</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="questions[{{ idx }}][correct_index]" id="q{{ idx }}_tf_t" value="0" required>
                <label class="form-check-label" for="q{{ idx }}_tf_t">True</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="questions[{{ idx }}][correct_index]" id="q{{ idx }}_tf_f" value="1">
                <label class="form-check-label" for="q{{ idx }}_tf_f">False</label>
            </div>
        </div>
        <div class="mb-2 question-sa-opts" style="display:none">
            <label class="form-label small mb-1">Correct answer(s) <span class="text-muted">(one per line)</span></label>
            <textarea class="form-control form-control-sm" name="questions[{{ idx }}][correct_text]" rows="2" placeholder="e.g. Paris"></textarea>
        </div>
        <div class="row">
            <div class="col-4">
                <label class="form-label small mb-1">Points</label>
                <input type="number" class="form-control form-control-sm" name="questions[{{ idx }}][points]" value="1" min="1" max="100">
            </div>
        </div>
    </div>
@endverbatim
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('questions-container');
    var tpl = document.getElementById('question-tpl');
    if (!container || !tpl) return;

    document.getElementById('add-question').addEventListener('click', function() {
        document.getElementById('no-questions-msg')?.remove();
        var idx = container.querySelectorAll('.question-block').length;
        var html = tpl.innerHTML
            .replace(/\{\{\s*idx\s*\}\}/g, String(idx))
            .replace(/\{\{\s*idx1\s*\}\}/g, String(idx + 1));
        var div = document.createElement('div');
        div.innerHTML = html;
        container.appendChild(div.firstElementChild);
        reindex();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-question')) {
            var block = e.target.closest('.question-block');
            if (block && container.querySelectorAll('.question-block').length > 1) {
                block.remove();
                reindex();
            }
        }
    });

    container.addEventListener('change', function(e) {
        var sel = e.target.closest('.question-type-select');
        if (!sel) return;
        var block = sel.closest('.question-block');
        if (!block) return;
        var mc = block.querySelector('.question-mc-opts');
        var tf = block.querySelector('.question-tf-opts');
        var sa = block.querySelector('.question-sa-opts');
        if (!mc || !tf) return;
        var v = sel.value;
        mc.style.display = (v === 'multiple_choice') ? 'block' : 'none';
        tf.style.display = (v === 'true_false') ? 'block' : 'none';
        if (sa) sa.style.display = (v === 'short_answer') ? 'block' : 'none';
        block.querySelectorAll('.question-mc-opts [type=radio]').forEach(function(r){ r.checked = false; });
        block.querySelectorAll('.question-tf-opts [type=radio]').forEach(function(r){ r.checked = false; });
        if (v === 'true_false' && !block.querySelector('.question-tf-opts [type=radio]:checked')) {
            block.querySelector('.question-tf-opts [type=radio][value="0"]').checked = true;
        }
        if (v === 'multiple_choice' && !block.querySelector('.question-mc-opts [type=radio]:checked')) {
            block.querySelector('.question-mc-opts [type=radio][value="0"]').checked = true;
        }
    });

    function reindex() {
        container.querySelectorAll('.question-block').forEach(function(block, i) {
            block.dataset.index = i;
            block.querySelector('.text-muted').textContent = 'Question ' + (i + 1);
            block.querySelectorAll('[name^="questions["]').forEach(function(el) {
                el.name = el.name.replace(/questions\[\d+\]/, 'questions[' + i + ']');
            });
        });
    }
});
</script>
@endpush
@endsection
