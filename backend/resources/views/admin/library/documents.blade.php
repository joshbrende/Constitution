@extends('layouts.dashboard')

@section('title', 'Library – Documents')
@section('page_heading', 'Library – Documents')

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
                    <div class="dash-panel-title">Documents</div>
                    <div class="dash-panel-subtitle">Party policy, speeches, resolutions. Set published_at to show in the app.</div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <a href="{{ route('admin.library.documents.create') }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">Add document</a>
                    <a href="{{ route('admin.library.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Library</a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.library.documents.index') }}" style="display:flex;gap:0.75rem;margin-bottom:1rem;flex-wrap:wrap;">
                <select name="category_id" class="form-input" style="max-width:12rem;">
                    <option value="">All categories</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="type" class="form-input" style="max-width:10rem;">
                    <option value="">All types</option>
                    @foreach ($documentTypes as $k => $v)
                        <option value="{{ $k }}" {{ request('type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <button type="submit" class="dash-btn-ghost" style="cursor:pointer;">Filter</button>
            </form>

            <table class="dash-table">
                <thead>
                    <tr><th>Title</th><th>Category</th><th>Type</th><th>Access</th><th>Published</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($documents as $doc)
                        <tr>
                            <td><strong>{{ $doc->title }}</strong></td>
                            <td>{{ $doc->category ? $doc->category->name : '—' }}</td>
                            <td><span class="dash-tag" style="font-size:0.75rem;">{{ $doc->document_type }}</span></td>
                            <td><span class="dash-tag" style="font-size:0.75rem;">{{ $doc->access_rule }}</span></td>
                            <td>{{ $doc->published_at ? $doc->published_at->format('d M Y') : 'Draft' }}</td>
                            <td>
                                <a href="{{ route('admin.library.documents.edit', $doc) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                <form method="POST" action="{{ route('admin.library.documents.destroy', $doc) }}" style="display:inline;" onsubmit="return confirm('Delete this document?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($documents->isEmpty())
                <p class="dash-panel-subtitle">No documents yet. <a href="{{ route('admin.library.documents.create') }}">Add a document</a>.</p>
            @else
                <div style="margin-top:1rem;">{{ $documents->withQueryString()->links() }}</div>
            @endif
        </section>
    </div>
@endsection
