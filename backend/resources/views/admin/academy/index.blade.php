@extends('layouts.dashboard')

@section('title', 'Academy – Courses')
@section('page_heading', 'Academy – Courses')

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title" style="display:flex;align-items:center;gap:0.5rem;">
                        <x-icons.workflow-icon key="academy.course" size="18" color="var(--zanupf-gold)" />
                        Courses
                    </div>
                    <div class="dash-panel-subtitle">Manage Academy courses. Create, edit, publish, or archive.</div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <form method="GET" action="{{ route('admin.academy.index') }}" style="display:flex;gap:0.5rem;align-items:center;">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search code or title"
                            style="padding:0.4rem 0.6rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);min-width:18rem;"
                        >
                        <button type="submit" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;font-size:0.8rem;">
                            Search
                        </button>
                    </form>
                    <a href="{{ route('admin.academy.courses.create') }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">Add course</a>
                    @if($membershipCourse)
                        <a href="{{ route('admin.academy.badges.index', $membershipCourse) }}" style="padding:0.4rem 0.75rem;background:rgba(250,204,21,0.15);color:var(--zanupf-gold);border:1px solid rgba(250,204,21,0.45);border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">
                            <span style="display:inline-flex;align-items:center;gap:0.4rem;">
                                <x-icons.workflow-icon key="academy.badge" size="16" color="var(--zanupf-gold)" />
                                Manage achievements
                            </span>
                        </a>
                    @endif
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            <table class="dash-table">
                <thead>
                    <tr><th>Code</th><th>Title</th><th>Level</th><th>Content</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($courses as $c)
                        @php
                            $statusClass = match($c->status) {
                                'published' => 'status-pill--active',
                                'archived' => 'status-pill--pending',
                                default => 'status-pill--review',
                            };
                        @endphp
                        <tr>
                            <td><code style="font-size:0.8rem;">{{ $c->code }}</code></td>
                            <td>
                                <strong>{{ $c->title }}</strong>
                                @if ($c->is_mandatory)
                                    <span class="dash-tag" style="margin-left:0.5rem;">Mandatory</span>
                                @endif
                            </td>
                            <td><span class="dash-tag" style="font-size:0.75rem;">{{ ucfirst($c->level ?? 'basic') }}</span></td>
                            <td>{{ $c->modules_count ?? 0 }} modules, {{ $c->assessments_count ?? 0 }} assessments</td>
                            <td>
                                <span class="status-pill {{ $statusClass }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.academy.badges.index', $c) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);display:inline-flex;align-items:center;gap:0.25rem;">
                                    <x-icons.workflow-icon key="academy.badge" size="14" color="var(--zanupf-gold)" />
                                    Badges
                                </a>
                                <a href="{{ route('admin.academy.assessments.index', $c) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);display:inline-flex;align-items:center;gap:0.25rem;">
                                    <x-icons.workflow-icon key="academy.assessment" size="14" color="var(--zanupf-gold)" />
                                    Assessments
                                </a>
                                <a href="{{ route('admin.academy.courses.edit', $c) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                <form method="POST" action="{{ route('admin.academy.courses.destroy', $c) }}" style="display:inline;" onsubmit="return confirm('Delete this course?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($courses->isEmpty())
                <p class="dash-panel-subtitle">No courses yet. <a href="{{ route('admin.academy.courses.create') }}">Add a course</a>.</p>
            @endif

            @if (! $courses->isEmpty())
                <div style="margin-top:1rem;">
                    {{ $courses->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
