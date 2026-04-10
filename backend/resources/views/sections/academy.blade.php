@extends('layouts.dashboard')

@section('title', 'ZANU PF Academy')
@section('page_heading', 'Academy')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Academy courses</div>
                    <div class="dash-panel-subtitle">
                        Mandatory and elective courses, modules, lessons, and assessments.
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;align-items:center;">
                    @if ($canManage)
                        <a href="{{ route('admin.academy.index') }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">Manage courses</a>
                    @endif
                    <span class="dash-tag">{{ $canManage ? 'Admin' : 'Student view' }}</span>
                </div>
            </div>

            @if ($courses->isEmpty())
                <p class="dash-panel-subtitle">
                    No courses available yet.
                    @if ($canManage)
                        <a href="{{ route('admin.academy.courses.create') }}">Create your first course</a>.
                    @endif
                </p>
            @else
                <table class="dash-table">
                    <thead>
                        <tr><th>Code</th><th>Title</th><th>Content</th><th>Status</th>@if ($canManage)<th></th>@endif</tr>
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
                                <td>{{ $c->modules_count ?? 0 }} modules, {{ $c->assessments_count ?? 0 }} assessments</td>
                                <td>
                                    <span class="status-pill {{ $statusClass }}">
                                        <span class="dot"></span>
                                        {{ ucfirst($c->status) }}
                                    </span>
                                </td>
                                @if ($canManage)
                                    <td>
                                        <a href="{{ route('admin.academy.assessments.index', $c) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Assessments</a>
                                        <a href="{{ route('admin.academy.courses.edit', $c) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection

