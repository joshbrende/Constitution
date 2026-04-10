@extends('layouts.dashboard')

@section('title', 'Academy badges – ' . $course->title)
@section('page_heading', 'Academy badges')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Badges for: {{ $course->title }}</div>
                    <div class="dash-panel-subtitle">Admins define the achievement criteria per course.</div>
                </div>
                    <div style="display:flex;gap:0.5rem;">
                    <form method="GET" action="{{ route('admin.academy.badges.index', $course) }}" style="display:flex;gap:0.5rem;align-items:center;">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search title or rule"
                            style="padding:0.4rem 0.6rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);min-width:16rem;"
                        >
                        <button type="submit" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;font-size:0.8rem;">
                            Search
                        </button>
                    </form>
                    <a href="{{ route('admin.academy.badges.create', $course) }}" class="dash-btn-ghost" style="text-decoration:none;">+ Add badge</a>
                    <a href="{{ route('admin.academy.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Courses</a>
                </div>
            </div>

            @if ($badges->isEmpty())
                <p class="dash-panel-subtitle" style="margin-top:1rem;">
                    No badges configured yet. Add one to enable locked/unlocked achievements for this course.
                </p>
            @else
                <table class="dash-table" style="margin-top:1rem;">
                    <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Rule</th>
                        <th>Target</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($badges as $b)
                        <tr>
                            <td style="font-size:1rem;white-space:nowrap;">
                                <span style="display:inline-flex;align-items:center;gap:0.4rem;">
                                    <x-icons.workflow-icon key="academy.badge" size="16" color="var(--zanupf-gold)" />
                                    <span>{{ $b->icon ?? '★' }}</span>
                                </span>
                            </td>
                            <td><strong>{{ $b->title }}</strong></td>
                            <td><code style="font-size:0.8rem;">{{ $b->rule_type }}</code></td>
                            <td>{{ $b->target_value }}</td>
                            <td style="white-space:nowrap;">
                                <a href="{{ route('admin.academy.badges.edit', [$course, $b]) }}" style="font-size:0.8rem;color:var(--zanupf-gold);margin-right:0.75rem;">Edit</a>
                                <form method="POST" action="{{ route('admin.academy.badges.destroy', [$course, $b]) }}" style="display:inline;" onsubmit="return confirm('Delete this badge?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="font-size:0.8rem;color:var(--zanupf-red);background:none;border:none;cursor:pointer;padding:0;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div style="margin-top:1rem;">
                    {{ $badges->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection

