@extends('layouts.dashboard')

@section('title', 'Presidium publications')
@section('page_heading', 'Presidium publications')

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Publications</div>
                    <div class="dash-panel-subtitle">
                        Books and publications shown in the app (featured items appear on Home + Presidium).
                    </div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.presidium-publications.create') }}" class="dash-btn-ghost" style="text-decoration:none;">+ Add publication</a>
                    <a href="{{ route('admin.presidium.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Presidium</a>
                </div>
            </div>

            @if ($publications->isEmpty())
                <p style="color:var(--text-muted);margin-top:1rem;">No publications yet.</p>
            @else
                <table class="dash-table" style="margin-top:1rem;">
                    <thead>
                    <tr>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Featured</th>
                        <th>Published</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($publications as $publication)
                        <tr>
                            <td>{{ $publication->order }}</td>
                            <td>
                                <strong>{{ $publication->title }}</strong>
                                @if($publication->author)
                                    <div style="font-size:0.8rem;color:var(--text-muted);">{{ $publication->author }}</div>
                                @endif
                            </td>
                            <td><code style="font-size:0.8rem;">{{ $publication->slug }}</code></td>
                            <td>{{ $publication->is_featured ? 'Yes' : 'No' }}</td>
                            <td>{{ $publication->is_published ? 'Yes' : 'No' }}</td>
                            <td style="white-space:nowrap;">
                                <a href="{{ route('admin.presidium-publications.edit', $publication) }}" style="font-size:0.8rem;color:var(--zanupf-gold);margin-right:0.75rem;">Edit</a>
                                <form method="POST" action="{{ route('admin.presidium-publications.destroy', $publication) }}" style="display:inline;" onsubmit="return confirm('Delete this publication?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="font-size:0.8rem;color:var(--zanupf-red);background:none;border:none;cursor:pointer;padding:0;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            <div style="margin-top:1rem;">
                {{ $publications->links() }}
            </div>
        </section>
    </div>
@endsection

