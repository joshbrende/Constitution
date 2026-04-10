@extends('layouts.dashboard')

@section('title', 'Priority projects')
@section('page_heading', 'Priority projects')

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Priority projects</div>
                    <div class="dash-panel-subtitle">
                        Vision 2030-aligned programmes, surfaced in the mobile app. Mark a project as published to show it to members.
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <a href="{{ route('admin.priority-projects.create') }}"
                       style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">
                        Add project
                    </a>
                </div>
            </div>

            <table class="dash-table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Published</th>
                    <th>Likes</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($projects as $project)
                    <tr>
                        <td>
                            <strong>{{ $project->title }}</strong>
                            @if($project->summary)
                                <div style="font-size:0.8rem;color:var(--text-muted);max-width:420px;white-space:normal;">
                                    {{ $project->summary }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($project->is_published && $project->published_at)
                                <span class="dash-tag" style="font-size:0.75rem;">{{ $project->published_at->format('d M Y') }}</span>
                            @else
                                <span class="dash-tag" style="font-size:0.75rem;background:rgba(148,163,184,0.12);color:var(--text-muted);">Draft</span>
                            @endif
                        </td>
                        <td>{{ $project->likes_count }}</td>
                        <td>
                            <a href="{{ route('admin.priority-projects.edit', $project) }}"
                               style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">
                                Edit
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.priority-projects.destroy', $project) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Delete this project?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if ($projects->isEmpty())
                <p class="dash-panel-subtitle" style="margin-top:1rem;">
                    No priority projects yet. Use “Add project” to create the first one.
                </p>
            @else
                <div style="margin-top:1rem;">
                    {{ $projects->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection

