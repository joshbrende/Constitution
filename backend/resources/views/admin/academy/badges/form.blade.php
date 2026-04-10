@extends('layouts.dashboard')

@section('title', $badge ? 'Edit badge – ' . $badge->title : 'Add badge')
@section('page_heading', 'Academy badge criteria')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $badge ? 'Edit badge' : 'Add badge' }}</div>
                    <div class="dash-panel-subtitle">
                        Course: {{ $course->title }}. This badge defines when mobile learners unlock it.
                    </div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.academy.badges.index', $course) }}" class="dash-btn-ghost" style="text-decoration:none;">← Badges</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="dash-alert dash-alert--error">
                    <ul style="margin:0;padding-left:1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $badge ? route('admin.academy.badges.update', [$course, $badge]) : route('admin.academy.badges.store', $course) }}">
                @csrf
                @if ($badge)
                    @method('PUT')
                @endif

                <div style="display:grid;gap:1rem;max-width:44rem;">
                    <div>
                        <label class="form-label">Icon (optional)</label>
                        <input type="text" name="icon" class="form-input" value="{{ old('icon', $badge->icon ?? '') }}" placeholder="e.g. 🏆 or medal">
                    </div>

                    <div>
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-input" value="{{ old('slug', $badge->slug ?? '') }}" required>
                        <p class="form-help">Unique identifier for this badge.</p>
                    </div>

                    <div>
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-input" value="{{ old('title', $badge->title ?? '') }}" required>
                    </div>

                    <div>
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-input">{{ old('description', $badge->description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="form-label">Rule type</label>
                        <select name="rule_type" class="form-input" required>
                            @php
                                $currentRule = old('rule_type', $badge->rule_type ?? 'enrolled_n');
                                $targetRequired = in_array($currentRule, ['enrolled_n','completed_n','pass_score_at_least','assessment_started_n','assessment_submitted_n'], true);
                            @endphp
                            <option value="enrolled_n" {{ $currentRule === 'enrolled_n' ? 'selected' : '' }}>Enrolled in N courses</option>
                            <option value="completed_n" {{ $currentRule === 'completed_n' ? 'selected' : '' }}>Completed N courses</option>
                            <option value="pass_score_at_least" {{ $currentRule === 'pass_score_at_least' ? 'selected' : '' }}>Pass assessment with score >= threshold</option>
                            <option value="assessment_started_n" {{ $currentRule === 'assessment_started_n' ? 'selected' : '' }}>Started N assessments</option>
                            <option value="assessment_submitted_n" {{ $currentRule === 'assessment_submitted_n' ? 'selected' : '' }}>Submitted N assessments</option>
                            <option value="membership_granted" {{ $currentRule === 'membership_granted' ? 'selected' : '' }}>Membership granted</option>
                            <option value="certificate_issued" {{ $currentRule === 'certificate_issued' ? 'selected' : '' }}>Certificate issued</option>
                            <option value="perfect_attempt" {{ $currentRule === 'perfect_attempt' ? 'selected' : '' }}>Perfect attempt (score 100)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Target value</label>
                        <input type="number" name="target_value" class="form-input" min="0"
                               value="{{ old('target_value', $badge->target_value ?? 1) }}" {{ $targetRequired ? 'required' : '' }}>
                        <p class="form-help">
                            For <code>enrolled_n</code>/<code>completed_n</code>: N.
                            For <code>assessment_started_n</code>/<code>assessment_submitted_n</code>: N.
                            For <code>pass_score_at_least</code>: score threshold (e.g. 70 or 85).
                            For <code>membership_granted</code>/<code>certificate_issued</code>/<code>perfect_attempt</code>: target value is optional.
                        </p>
                    </div>
                </div>

                <div style="margin-top:1.25rem;display:flex;gap:0.75rem;">
                    <button type="submit" class="form-btn-primary">
                        {{ $badge ? 'Save badge' : 'Create badge' }}
                    </button>
                    <a href="{{ route('admin.academy.badges.index', $course) }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection

