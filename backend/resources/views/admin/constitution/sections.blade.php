@extends('layouts.dashboard')

@section('title', 'Sections – Chapter ' . $chapter->number)
@section('page_heading', 'Sections – Chapter ' . $chapter->number . ' – ' . $chapter->title)

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
                    <div class="dash-panel-title">Sections</div>
                    <div class="dash-panel-subtitle">Chapter {{ $chapter->number }}: {{ $chapter->title }}</div>
                </div>
                @if($chapter->part)
                    <a href="{{ route('admin.constitution.chapters', $chapter->part) }}" class="dash-btn-ghost" style="text-decoration:none;">← Chapters</a>
                @else
                    <a href="{{ route('admin.constitution.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Constitution</a>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.constitution.sections.store', $chapter) }}" style="margin-bottom:1rem;">
                @csrf
                <div style="display:grid;grid-template-columns:80px 1fr auto;gap:0.5rem;align-items:end;margin-bottom:0.5rem;">
                    <div>
                        <label for="new_section_logical_number" style="font-size:0.75rem;color:var(--text-muted);display:block;">#</label>
                        <input id="new_section_logical_number" type="text" name="logical_number" placeholder="e.g. 1A" required style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <div>
                        <label for="new_section_title" style="font-size:0.75rem;color:var(--text-muted);display:block;">Title</label>
                        <input id="new_section_title" type="text" name="title" required style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                    </div>
                    <button type="submit" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">Add</button>
                </div>
                <div>
                    <label for="new_section_body" style="font-size:0.75rem;color:var(--text-muted);display:block;">Body</label>
                    <textarea id="new_section_body" name="body" rows="3" required placeholder="Initial content" style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);font-family:inherit;resize:vertical;">{{ old('body') }}</textarea>
                </div>
            </form>

            <table class="dash-table">
                <thead>
                    <tr><th>#</th><th>Title</th><th>Versions</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($sections as $sec)
                        @php
                            $latestVersion = $sec->versions->sortByDesc('version_number')->first();
                            $status = $latestVersion?->status ?? 'draft';
                            $statusClass = match($status) {
                                'published' => 'status-pill--active',
                                'in_review' => 'status-pill--review',
                                default => 'status-pill--pending',
                            };
                        @endphp
                        <tr>
                            <td>{{ $sec->logical_number }}</td>
                            <td>{{ $sec->title }}</td>
                            <td>{{ $sec->versions->count() }}</td>
                            <td>
                                <span class="status-pill {{ $statusClass }}">
                                    <span class="dot"></span>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.constitution.sections.edit', $sec) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                <a href="{{ route('admin.constitution.sections.versions', $sec) }}" style="font-size:0.8rem;margin-right:0.5rem;">Versions</a>
                                <form method="POST" action="{{ route('admin.constitution.sections.destroy', $sec) }}" style="display:inline;" onsubmit="return confirm('Delete this section?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($sections->isEmpty())
                <p class="dash-panel-subtitle">No sections yet. Add one above.</p>
            @endif
        </section>
    </div>
@endsection
