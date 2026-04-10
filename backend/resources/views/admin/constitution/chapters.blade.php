@extends('layouts.dashboard')

@section('title', 'Chapters – Part ' . $part->number)
@section('page_heading', 'Part ' . $part->number . ' – ' . $part->title)

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
                    <div class="dash-panel-title">Chapters</div>
                    <div class="dash-panel-subtitle">Part {{ $part->number }}: {{ $part->title }}</div>
                </div>
                <a href="{{ route('admin.constitution.parts') }}" class="dash-btn-ghost" style="text-decoration:none;">← Parts</a>
            </div>

            <form method="POST" action="{{ route('admin.constitution.chapters.store', $part) }}" style="display:grid;grid-template-columns:1fr 2fr 80px auto;gap:0.5rem;align-items:end;margin-bottom:1rem;">
                @csrf
                <div>
                    <label for="chapter_number" style="font-size:0.75rem;color:var(--text-muted);display:block;">Number</label>
                    <input id="chapter_number" type="number" name="number" min="1" required style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                </div>
                <div>
                    <label for="chapter_title" style="font-size:0.75rem;color:var(--text-muted);display:block;">Title</label>
                    <input id="chapter_title" type="text" name="title" required style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                </div>
                <div>
                    <label for="chapter_order" style="font-size:0.75rem;color:var(--text-muted);display:block;">Order</label>
                    <input id="chapter_order" type="number" name="order" min="0" style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                </div>
                <button type="submit" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">Add</button>
            </form>

            <table class="dash-table">
                <thead>
                    <tr><th>#</th><th>Title</th><th>Sections</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($chapters as $ch)
                        <tr>
                            <td>{{ $ch->number }}</td>
                            <td>
                                <a href="{{ route('admin.constitution.sections', $ch) }}" style="color:var(--zanupf-gold);text-decoration:none;">{{ $ch->title }}</a>
                            </td>
                            <td>{{ $ch->sections_count }}</td>
                            <td>
                                <a href="{{ route('admin.constitution.chapters.edit', $ch) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                <a href="{{ route('admin.constitution.sections', $ch) }}" style="font-size:0.8rem;margin-right:0.5rem;">Sections</a>
                                <form method="POST" action="{{ route('admin.constitution.chapters.destroy', $ch) }}" style="display:inline;" onsubmit="return confirm('Delete this chapter?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($chapters->isEmpty())
                <p class="dash-panel-subtitle">No chapters yet. Add one above.</p>
            @endif
        </section>
    </div>
@endsection
