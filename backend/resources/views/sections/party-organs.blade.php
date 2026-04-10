@extends('layouts.dashboard')

@section('title', 'Party Organs')
@section('page_heading', 'Party Organs')

@section('content')
    <div class="dash-content">
        @if ($canManage ?? false)
            <div style="margin-bottom:1rem;">
                <a href="{{ route('admin.party-organs.index') }}" class="dash-btn-ghost" style="text-decoration:none;">Manage Party Organs</a>
            </div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Party Organs</div>
                    <div class="dash-panel-subtitle">
                        Principal organs of the Party: Congress, Central Committee, Politburo, Leagues, and structures.
                    </div>
                </div>
            </div>

            @if ($organs->isEmpty())
                <p class="dash-panel-subtitle" style="margin-top:1rem;">No party organs published yet. Check back later or use the mobile app.</p>
            @else
                <div class="dash-nav-group-label" style="margin-top:1rem;">Organs</div>
                <table class="dash-table">
                    <thead>
                        <tr><th>Name</th><th>Description</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($organs as $organ)
                            <tr>
                                <td><strong>{{ $organ->name }}</strong></td>
                                <td>{{ $organ->short_description ? \Illuminate\Support\Str::limit($organ->short_description, 80) : '—' }}</td>
                                <td>
                                    <a href="{{ route('party-organs.show', $organ) }}" style="font-size:0.85rem;color:var(--zanupf-gold);text-decoration:none;">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection
