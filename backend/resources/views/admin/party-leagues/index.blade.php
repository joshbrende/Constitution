@extends('layouts.dashboard')

@section('title', 'Party Leagues')
@section('page_heading', 'Party Leagues')

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
                    <div class="dash-panel-title">Party Leagues</div>
                    <div class="dash-panel-subtitle">Veterans, Women's, Youth and any additional leagues. Shown on The Party page and in the app.</div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.party-leagues.create') }}" class="dash-btn-ghost" style="text-decoration:none;">+ Add league</a>
                    <a href="{{ route('admin.party.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← The Party</a>
                </div>
            </div>

            @if ($leagues->isEmpty())
                <p style="color:var(--text-muted);margin-top:1rem;">No leagues yet. Add Veterans, Women's, Youth or other leagues.</p>
                <a href="{{ route('admin.party-leagues.create') }}" style="display:inline-block;margin-top:0.75rem;color:var(--zanupf-gold);font-weight:600;">Create first league</a>
            @else
                <table class="dash-table" style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Leader</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leagues as $league)
                            <tr>
                                <td>{{ $league->sort_order }}</td>
                                <td><strong>{{ $league->name }}</strong></td>
                                <td><code style="font-size:0.8rem;">{{ $league->slug }}</code></td>
                                <td>{{ $league->leader_name ?: '—' }}{{ $league->leader_title ? ' – ' . $league->leader_title : '' }}</td>
                                <td style="white-space:nowrap;">
                                    <a href="{{ route('admin.party-leagues.edit', $league) }}" style="font-size:0.8rem;color:var(--zanupf-gold);margin-right:0.75rem;">Edit</a>
                                    <form method="POST" action="{{ route('admin.party-leagues.destroy', $league) }}" style="display:inline;" onsubmit="return confirm('Delete this league?');">
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
        </section>
    </div>
@endsection
