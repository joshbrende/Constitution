@extends('layouts.dashboard')

@section('title', $course ? 'Edit Course – ' . $course->title : 'Create Course')
@section('page_heading', $course ? 'Edit Course' : 'Create Course')

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        @if (session('show_next_steps') && $course)
            <section class="dash-panel" style="grid-column: span 2;margin-bottom:1.5rem;border-left:4px solid var(--zanupf-gold);">
                <div class="dash-panel-header">
                    <div>
                        <div class="dash-panel-title" style="font-size:1rem;">Next steps</div>
                        <div class="dash-panel-subtitle">Complete your course setup to make it ready for learners.</div>
                    </div>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:0.75rem;margin-top:0.75rem;">
                    <a href="{{ route('admin.academy.assessments.index', $course) }}" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border-radius:0.4rem;text-decoration:none;font-size:0.9rem;font-weight:600;display:inline-flex;align-items:center;gap:0.5rem;">
                        Add assessment
                    </a>
                    <a href="{{ route('admin.academy.badges.index', $course) }}" style="padding:0.5rem 1rem;background:rgba(250,204,21,0.12);color:var(--zanupf-gold);border:1px solid rgba(250,204,21,0.45);border-radius:0.4rem;text-decoration:none;font-size:0.9rem;font-weight:600;display:inline-flex;align-items:center;gap:0.5rem;">
                        Manage achievements
                    </a>
                    <span style="color:var(--text-muted);font-size:0.85rem;align-self:center;">A course needs at least one assessment. Add modules and lessons via seeders or future module management.</span>
                </div>
            </section>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $course ? 'Edit course' : 'Create new course' }}</div>
                    <div class="dash-panel-subtitle">
                        {{ $course ? $course->code : 'Define the course identity, settings, and publishing status.' }}
                    </div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    @if ($course)
                        <a href="{{ route('admin.academy.assessments.index', $course) }}" class="dash-btn-ghost" style="text-decoration:none;">Assessments</a>
                    @endif
                    <a href="{{ route('admin.academy.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Courses</a>
                </div>
            </div>

            <form method="POST" action="{{ $course ? route('admin.academy.courses.update', $course) : route('admin.academy.courses.store') }}">
                @csrf
                @if ($course) @method('PUT') @endif

                {{-- Section: Course identity --}}
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-size:0.85rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--zanupf-gold);margin-bottom:1rem;padding-bottom:0.35rem;border-bottom:1px solid var(--border-subtle);">
                        Course identity
                    </h3>
                    <div style="display:grid;gap:1rem;max-width:40rem;">
                        <div>
                            <label for="code" class="form-label">Code <span style="color:var(--zanupf-red);">*</span></label>
                            <input id="code" type="text" name="code" value="{{ old('code', $course?->code) }}" required
                                placeholder="e.g. ZP-CONST-101, ADV-CADRE-201"
                                class="form-input"
                                style="font-family:ui-monospace,monospace;">
                            <p class="form-help">Short unique identifier for reports and enrolment. Letters, numbers, hyphens, underscores only.</p>
                        </div>
                        <div>
                            <label for="title" class="form-label">Title <span style="color:var(--zanupf-red);">*</span></label>
                            <input id="title" type="text" name="title" value="{{ old('title', $course?->title) }}" required
                                placeholder="e.g. ZANU PF Constitution Basics"
                                class="form-input">
                            <p class="form-help">Full display name shown to learners.</p>
                        </div>
                        <div>
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" rows="4" maxlength="2000"
                                placeholder="Brief overview of what the course covers and who it is for."
                                class="form-input">{{ old('description', $course?->description) }}</textarea>
                            <p class="form-help">1–2 paragraphs. Helps learners understand the scope and expectations. Max 2000 characters.</p>
                        </div>
                        <div>
                            <label for="level" class="form-label">Level <span style="color:var(--zanupf-red);">*</span></label>
                            <select id="level" name="level" class="form-input" style="max-width:12rem;">
                                <option value="basic" {{ old('level', $course?->level ?? 'basic') === 'basic' ? 'selected' : '' }}>Basic</option>
                                <option value="intermediate" {{ old('level', $course?->level ?? 'basic') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level', $course?->level ?? 'basic') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            <p class="form-help">Indicates the expected prior knowledge and complexity of the material.</p>
                        </div>
                    </div>
                </div>

                {{-- Section: Course settings --}}
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-size:0.85rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--zanupf-gold);margin-bottom:1rem;padding-bottom:0.35rem;border-bottom:1px solid var(--border-subtle);">
                        Settings
                    </h3>
                    <div style="display:flex;flex-direction:column;gap:0.75rem;">
                        <label class="form-check">
                            <input type="hidden" name="is_mandatory" value="0">
                            <input type="checkbox" name="is_mandatory" value="1" {{ old('is_mandatory', $course?->is_mandatory) ? 'checked' : '' }}>
                            <span><strong>Mandatory course</strong> – Required for certain roles or structures (e.g. Youth League members).</span>
                        </label>
                        <label class="form-check">
                            <input type="hidden" name="grants_membership" value="0">
                            <input type="checkbox" name="grants_membership" value="1" {{ old('grants_membership', $course?->grants_membership) ? 'checked' : '' }}>
                            <span><strong>Grants membership on pass</strong> – Successful completion of the assessment confers membership.</span>
                        </label>
                        <div style="max-width:28rem;">
                            <label for="certificate_title" class="form-label">Certificate title</label>
                            <input id="certificate_title" type="text" name="certificate_title" value="{{ old('certificate_title', $course?->certificate_title) }}"
                                placeholder="e.g. Certificate of Competence, Certificate of Attendance"
                                class="form-input">
                            <p class="form-help">Shown at the top of the PDF certificate when issued for this course. Leave blank for "Certificate of Completion".</p>
                            <p style="margin-top:0.5rem;">
                                <button type="button" id="preview-certificate-btn" class="dash-btn-ghost" style="font-size:0.85rem;padding:0.4rem 0.75rem;">
                                    Preview certificate
                                </button>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Section: Publishing --}}
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-size:0.85rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--zanupf-gold);margin-bottom:1rem;padding-bottom:0.35rem;border-bottom:1px solid var(--border-subtle);">
                        Publishing
                    </h3>
                    <div style="max-width:16rem;">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-input">
                            <option value="draft" {{ old('status', $course?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft – Not visible to learners</option>
                            <option value="published" {{ old('status', $course?->status) === 'published' ? 'selected' : '' }}>Published – Visible and enrollable</option>
                            <option value="archived" {{ old('status', $course?->status) === 'archived' ? 'selected' : '' }}>Archived – Hidden, no new enrolments</option>
                        </select>
                        <p class="form-help">New courses default to Draft until modules and assessment are ready.</p>
                    </div>
                </div>

                <div style="display:flex;gap:0.75rem;padding-top:0.5rem;">
                    <button type="submit" class="form-btn-primary">
                        {{ $course ? 'Save changes' : 'Create course' }}
                    </button>
                    <a href="{{ route('admin.academy.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                </div>
            </form>
        </section>

        <style>
            .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-main); margin-bottom:0.35rem; }
            .form-input { width:100%; padding:0.5rem 0.65rem; border:1px solid var(--border-subtle); border-radius:0.4rem; background:rgba(15,23,42,0.9); color:var(--text-main); font-size:0.95rem; }
            .form-input:focus { outline:none; border-color:var(--zanupf-gold); }
            .form-help { font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; line-height:1.4; }
            .form-check { display:flex; align-items:flex-start; gap:0.6rem; cursor:pointer; font-size:0.9rem; }
            .form-check input[type="checkbox"] { width:1.1rem; height:1.1rem; margin-top:0.15rem; flex-shrink:0; }
            .form-btn-primary { padding:0.5rem 1.25rem; background:var(--zanupf-green); color:#fff; border:none; border-radius:0.4rem; cursor:pointer; font-weight:600; font-size:0.9rem; }
            .form-btn-primary:hover { filter:brightness(1.1); }
        </style>
        <script>
            document.getElementById('preview-certificate-btn')?.addEventListener('click', function () {
                var base = '{{ route('certificate.preview') }}';
                var courseTitle = (document.getElementById('title')?.value || '').trim() || 'Foundational Constitutional Studies Certificate';
                var certTitle = (document.getElementById('certificate_title')?.value || '').trim();
                var url = base + '?course_title=' + encodeURIComponent(courseTitle);
                if (certTitle) url += '&certificate_title=' + encodeURIComponent(certTitle);
                window.open(url, '_blank', 'noopener');
            });
        </script>
    </div>
@endsection
