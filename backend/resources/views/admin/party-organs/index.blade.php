@extends('layouts.dashboard')

@section('title', 'Party Organs')
@section('page_heading', 'Party Organs')

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
                    <div class="dash-panel-title">Party Organs</div>
                    <div class="dash-panel-subtitle">Principal organs of the Party (Congress, Central Committee, Politburo, Leagues, structures). Shown in the app under Party Organs.</div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.party-organs.create') }}" class="dash-btn-ghost" style="text-decoration:none;">+ Add organ</a>
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            @if ($organs->isEmpty())
                <p style="color:var(--text-muted);margin-top:1rem;">No party organs yet. Add one to show Congress, Central Committee, Politburo, and other structures in the app.</p>
                <a href="{{ route('admin.party-organs.create') }}" style="display:inline-block;margin-top:0.75rem;color:var(--zanupf-gold);font-weight:600;">Create first organ</a>
            @else
                <table class="dash-table" style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($organs as $organ)
                            <tr>
                                <td>{{ $organ->order }}</td>
                                <td><strong>{{ $organ->name }}</strong></td>
                                <td><code style="font-size:0.8rem;">{{ $organ->slug }}</code></td>
                                <td>
                                    @if ($organ->is_published)
                                        <span class="dash-tag" style="font-size:0.75rem;background:var(--zanupf-green);color:#fff;">Published</span>
                                    @else
                                        <span class="dash-tag" style="font-size:0.75rem;">Draft</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;">
                                    <a href="{{ route('admin.party-organs.edit', $organ) }}" style="font-size:0.8rem;color:var(--zanupf-gold);margin-right:0.75rem;">Edit</a>
                                    <form method="POST" action="{{ route('admin.party-organs.destroy', $organ) }}" style="display:inline;" onsubmit="return confirm('Delete this party organ?');">
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
