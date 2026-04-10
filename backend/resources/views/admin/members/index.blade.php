@extends('layouts.dashboard')

@section('title', 'Members')
@section('page_heading', 'Members')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Members</div>
                    <div class="dash-panel-subtitle">Users who have completed membership requirements and hold at least one certificate.</div>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
            </div>

            <form method="GET" action="{{ route('admin.members.index') }}" style="margin-bottom:1rem;">
                <div style="display:flex;gap:0.5rem;max-width:28rem;">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search by name, surname, or email"
                        style="flex:1;padding:0.5rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);"
                    >
                    <button
                        type="submit"
                        style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;"
                    >
                        Search
                    </button>
                </div>
            </form>

            @if ($members->isEmpty())
                <p class="dash-panel-subtitle">No members match your search.</p>
            @else
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles / membership</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $m)
                            <tr>
                                <td>
                                    <strong>{{ $m->name }} {{ $m->surname }}</strong>
                                </td>
                                <td>{{ $m->email }}</td>
                                <td>
                                    @php
                                        $roles = collect($m->roles ?? []);
                                        // Only surface "member" once the user has at least one certificate
                                        if (($m->certificates->count() ?? 0) === 0) {
                                            $roles = $roles->reject(fn($r) => ($r->slug ?? null) === 'member');
                                        }
                                    @endphp
                                    {{ $roles->map(fn($r)=>$r->slug ?? $r->name ?? '')->filter()->implode(', ') ?: '—' }}
                                </td>
                                <td>{{ optional($m->created_at)->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:1rem;">
                    {{ $members->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
