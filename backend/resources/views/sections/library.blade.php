@extends('layouts.dashboard')

@section('title', 'Digital Library')
@section('page_heading', 'Digital Library')

@section('content')
    <div class="dash-content">
        @if ($canManage ?? false)
            <div style="margin-bottom:1rem;">
                <a href="{{ route('admin.library.index') }}" class="dash-btn-ghost" style="text-decoration:none;">Manage Digital Library →</a>
            </div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Library</div>
                    <div class="dash-panel-subtitle">
                        Party documents, policy papers, speeches, resolutions, and archival material. Use the mobile app for the full experience.
                    </div>
                </div>
            </div>

            @if ($categories->isNotEmpty())
                <div class="dash-nav-group-label" style="margin-top:1rem;">Categories</div>
                <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1.25rem;">
                    <a href="{{ route('library.home') }}" class="dash-tag" style="text-decoration:none;{{ !$categoryId ? 'background:rgba(250,204,21,0.2);color:var(--zanupf-gold);' : '' }}">All</a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('library.home', ['category_id' => $cat->id]) }}" class="dash-tag" style="text-decoration:none;{{ $categoryId == $cat->id ? 'background:rgba(250,204,21,0.2);color:var(--zanupf-gold);' : '' }}">{{ $cat->name }} ({{ $cat->documents_count ?? 0 }})</a>
                    @endforeach
                </div>
            @endif

            <div class="dash-nav-group-label">Documents</div>
            @if ($documents->isEmpty())
                <p class="dash-panel-subtitle">No documents in this category yet. Check back later or use the mobile app.</p>
            @else
                <table class="dash-table">
                    <thead>
                        <tr><th>Title</th><th>Category</th><th>Type</th><th>Published</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $doc)
                            <tr>
                                <td><strong>{{ $doc->title }}</strong></td>
                                <td>{{ $doc->category?->name ?? '—' }}</td>
                                <td><span class="dash-tag" style="font-size:0.75rem;">{{ $doc->document_type }}</span></td>
                                <td>{{ $doc->published_at?->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('library.document', $doc) }}" style="font-size:0.85rem;color:var(--zanupf-gold);text-decoration:none;">Read</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection
