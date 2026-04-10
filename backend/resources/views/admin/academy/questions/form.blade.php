@extends('layouts.dashboard')

@section('title', $question ? 'Edit Question' : 'Add Question')
@section('page_heading', $question ? 'Edit Question' : 'Add Question')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $question ? 'Edit Question' : 'Add Question' }}</div>
                    <div class="dash-panel-subtitle">{{ $assessment->title }}</div>
                </div>
                <a href="{{ route('admin.academy.assessments.show', $assessment) }}" class="dash-btn-ghost" style="text-decoration:none;">← Assessment</a>
            </div>

            <form method="POST" action="{{ $question ? route('admin.academy.questions.update', $question) : route('admin.academy.questions.store', $assessment) }}" id="question-form">
                @csrf
                @if ($question) @method('PUT') @endif
                <div style="display:grid;gap:1rem;max-width:42rem;">
                    <div>
                        <label for="body" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Question text</label>
                        <textarea id="body" name="body" rows="4" required
                            style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);font-family:inherit;resize:vertical;">{{ old('body', $question?->body) }}</textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label for="module_id" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Module (topic)</label>
                            <select id="module_id" name="module_id"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                                <option value="">— No module —</option>
                                @foreach ($assessment->course->modules ?? [] as $m)
                                    <option value="{{ $m->id }}" {{ old('module_id', $question?->module_id) == $m->id ? 'selected' : '' }}>{{ $m->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="difficulty" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Difficulty</label>
                            <select id="difficulty" name="difficulty"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                                <option value="easy" {{ old('difficulty', $question?->difficulty ?? 'medium') === 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('difficulty', $question?->difficulty ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('difficulty', $question?->difficulty ?? 'medium') === 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label for="order" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Order</label>
                            <input id="order" type="number" name="order" min="0" value="{{ old('order', $question?->order ?? $assessment->questions()->max('order') + 1) }}"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                        </div>
                        <div>
                            <label for="marks" style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.25rem;">Marks</label>
                            <input id="marks" type="number" name="marks" min="1" value="{{ old('marks', $question?->marks ?? 1) }}"
                                style="width:100%;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:0.75rem;color:var(--text-muted);display:block;margin-bottom:0.5rem;">Options (select the correct answer)</label>
                        <div id="options-container" style="display:flex;flex-direction:column;gap:0.5rem;">
                            @if ($question && $question->options->isNotEmpty())
                                @foreach ($question->options as $idx => $opt)
                                    <div class="option-row" style="display:flex;gap:0.5rem;align-items:center;">
                                        <input type="radio" name="correct_index" value="{{ $idx }}" {{ $opt->is_correct ? 'checked' : '' }} required>
                                        <input type="hidden" name="options[{{ $idx }}][id]" value="{{ $opt->id }}">
                                        <input type="text" name="options[{{ $idx }}][body]" value="{{ old('options.'.$idx.'.body', $opt->body) }}" required
                                            placeholder="Option {{ $idx + 1 }}"
                                            style="flex:1;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                                        <button type="button" class="remove-option" style="background:none;border:none;color:#f87171;cursor:pointer;padding:0.25rem;">✕</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="option-row" style="display:flex;gap:0.5rem;align-items:center;">
                                    <input type="radio" name="correct_index" value="0" required>
                                    <input type="text" name="options[0][body]" value="{{ old('options.0.body') }}" required placeholder="Option 1"
                                        style="flex:1;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                                    <button type="button" class="remove-option" style="background:none;border:none;color:#f87171;cursor:pointer;padding:0.25rem;">✕</button>
                                </div>
                                <div class="option-row" style="display:flex;gap:0.5rem;align-items:center;">
                                    <input type="radio" name="correct_index" value="1">
                                    <input type="text" name="options[1][body]" value="{{ old('options.1.body') }}" required placeholder="Option 2"
                                        style="flex:1;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                                    <button type="button" class="remove-option" style="background:none;border:none;color:#f87171;cursor:pointer;padding:0.25rem;">✕</button>
                                </div>
                            @endif
                        </div>
                        <button type="button" id="add-option" style="margin-top:0.5rem;padding:0.35rem 0.75rem;background:var(--border-subtle);color:var(--text-main);border:none;border-radius:0.4rem;cursor:pointer;font-size:0.85rem;">+ Add option</button>
                    </div>

                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">{{ $question ? 'Save question' : 'Add question' }}</button>
                        <a href="{{ route('admin.academy.assessments.show', $assessment) }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <script>
        (function() {
            const container = document.getElementById('options-container');
            const addBtn = document.getElementById('add-option');

            addBtn.addEventListener('click', function() {
                const rows = container.querySelectorAll('.option-row');
                const idx = rows.length;
                const div = document.createElement('div');
                div.className = 'option-row';
                div.style.cssText = 'display:flex;gap:0.5rem;align-items:center;';
                div.innerHTML = '<input type="radio" name="correct_index" value="' + idx + '">' +
                    '<input type="text" name="options[' + idx + '][body]" required placeholder="Option ' + (idx + 1) + '" style="flex:1;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">' +
                    '<button type="button" class="remove-option" style="background:none;border:none;color:#f87171;cursor:pointer;padding:0.25rem;">✕</button>';
                container.appendChild(div);
                reindexOptions();
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-option')) {
                    const row = e.target.closest('.option-row');
                    if (container.querySelectorAll('.option-row').length > 2) {
                        row.remove();
                        reindexOptions();
                    }
                }
            });

            function reindexOptions() {
                const rows = container.querySelectorAll('.option-row');
                rows.forEach((row, i) => {
                    const radio = row.querySelector('input[type="radio"]');
                    const hidden = row.querySelector('input[type="hidden"]');
                    const text = row.querySelector('input[type="text"]');
                    radio.name = 'correct_index';
                    radio.value = i;
                    if (hidden) hidden.name = 'options[' + i + '][id]';
                    text.name = 'options[' + i + '][body]';
                    text.placeholder = 'Option ' + (i + 1);
                });
            }
        })();
    </script>
@endsection
