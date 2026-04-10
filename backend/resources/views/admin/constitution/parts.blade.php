@extends('layouts.dashboard')

@section('title', 'Parts – Constitution')
@section('page_heading', 'Parts – Constitution')

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
                    <div class="dash-panel-title">Parts</div>
                    <div class="dash-panel-subtitle">Manage constitution parts</div>
                </div>
                <a href="{{ route('admin.constitution.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Constitution</a>
            </div>

            <form method="POST" action="{{ route('admin.constitution.parts.store') }}" style="display:grid;grid-template-columns:1fr 2fr 80px auto;gap:0.5rem;align-items:end;margin-bottom:1rem;">
                @csrf
                <div>
                    <label for="part_number" style="font-size:0.75rem;color:var(--text-muted);display:block;">Number</label>
                    <input id="part_number" type="number" name="number" min="1" required style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                </div>
                <div>
                    <label for="part_title" style="font-size:0.75rem;color:var(--text-muted);display:block;">Title</label>
                    <input id="part_title" type="text" name="title" required style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                </div>
                <div>
                    <label for="part_order" style="font-size:0.75rem;color:var(--text-muted);display:block;">Order</label>
                    <input id="part_order" type="number" name="order" min="0" style="width:100%;padding:0.4rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);">
                </div>
                <button type="submit" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;">Add</button>
            </form>

            <table class="dash-table">
                <thead>
                    <tr><th>#</th><th>Title</th><th>Chapters</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($parts as $p)
                        <tr>
                            <td>{{ $p->number }}</td>
                            <td>
                                <a href="{{ route('admin.constitution.chapters', $p) }}" style="color:var(--zanupf-gold);text-decoration:none;">{{ $p->title }}</a>
                            </td>
                            <td>{{ $p->chapters_count ?? 0 }}</td>
                            <td>
                                <a href="{{ route('admin.constitution.parts.edit', $p) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                <a href="{{ route('admin.constitution.chapters', $p) }}" style="font-size:0.8rem;margin-right:0.5rem;">Chapters</a>
                                <form method="POST" action="{{ route('admin.constitution.parts.destroy', $p) }}" style="display:inline;" onsubmit="return confirm('Delete this part?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($parts->isEmpty())
                <p class="dash-panel-subtitle">No parts yet. Add one above.</p>
            @endif
        </section>
    </div>
@endsection
