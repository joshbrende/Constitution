@extends('layouts.dashboard')

@section('title', 'Library – Categories')
@section('page_heading', 'Library – Categories')

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
                    <div class="dash-panel-title">Categories</div>
                    <div class="dash-panel-subtitle">Organise library documents by type, topic, or audience.</div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <a href="{{ route('admin.library.categories.create') }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">Add category</a>
                    <a href="{{ route('admin.library.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Library</a>
                </div>
            </div>

            <table class="dash-table">
                <thead>
                    <tr><th>Name</th><th>Slug</th><th>Documents</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($categories as $cat)
                        <tr>
                            <td><strong>{{ $cat->name }}</strong></td>
                            <td><code style="font-size:0.8rem;">{{ $cat->slug }}</code></td>
                            <td>{{ $cat->documents_count ?? 0 }}</td>
                            <td>
                                <a href="{{ route('admin.library.categories.edit', $cat) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                <form method="POST" action="{{ route('admin.library.categories.destroy', $cat) }}" style="display:inline;" onsubmit="return confirm('Delete this category? Documents will be uncategorised.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($categories->isEmpty())
                <p class="dash-panel-subtitle">No categories yet. <a href="{{ route('admin.library.categories.create') }}">Add a category</a>.</p>
            @endif
        </section>
    </div>
@endsection
