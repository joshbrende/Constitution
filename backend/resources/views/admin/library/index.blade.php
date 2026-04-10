@extends('layouts.dashboard')

@section('title', 'Digital Library')
@section('page_heading', 'Digital Library')

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
                    <div class="dash-panel-title">Digital Library</div>
                    <div class="dash-panel-subtitle">Manage party documents, categories, and visibility (public / member / leadership).</div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <a href="{{ route('admin.library.categories.index') }}" class="dash-btn-ghost" style="text-decoration:none;">Categories</a>
                    <a href="{{ route('admin.library.documents.index') }}" class="dash-btn-ghost" style="text-decoration:none;">Documents</a>
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:1rem;">
                <div style="background:rgba(15,23,42,0.6);border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1.25rem;">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--text-main);margin-bottom:0.5rem;">Categories</h3>
                    <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:1rem;">Organise documents by type, topic, or audience.</p>
                    <a href="{{ route('admin.library.categories.index') }}" style="display:inline-flex;align-items:center;gap:0.5rem;color:var(--zanupf-gold);text-decoration:none;font-weight:600;font-size:0.9rem;">
                        Manage categories ({{ $categories->count() }})
                    </a>
                </div>
                <div style="background:rgba(15,23,42,0.6);border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1.25rem;">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--text-main);margin-bottom:0.5rem;">Documents</h3>
                    <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:1rem;">Policy papers, speeches, resolutions, pamphlets, manuals.</p>
                    <a href="{{ route('admin.library.documents.index') }}" style="display:inline-flex;align-items:center;gap:0.5rem;color:var(--zanupf-gold);text-decoration:none;font-weight:600;font-size:0.9rem;">
                        Manage documents ({{ $documents->total() }})
                    </a>
                </div>
            </div>

            @if ($documents->count() > 0)
                <h3 style="font-size:0.9rem;font-weight:600;margin-top:1.5rem;margin-bottom:0.5rem;">Recent documents</h3>
                <table class="dash-table">
                    <thead>
                        <tr><th>Title</th><th>Category</th><th>Type</th><th>Access</th><th>Published</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($documents->take(5) as $doc)
                            <tr>
                                <td><strong>{{ $doc->title }}</strong></td>
                                <td>{{ $doc->category ? $doc->category->name : '—' }}</td>
                                <td><span class="dash-tag" style="font-size:0.75rem;">{{ $doc->document_type }}</span></td>
                                <td><span class="dash-tag" style="font-size:0.75rem;">{{ $doc->access_rule }}</span></td>
                                <td>{{ $doc->published_at ? $doc->published_at->format('d M Y') : 'Draft' }}</td>
                                <td>
                                    <a href="{{ route('admin.library.documents.edit', $doc) }}" style="font-size:0.8rem;color:var(--zanupf-gold);">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection
