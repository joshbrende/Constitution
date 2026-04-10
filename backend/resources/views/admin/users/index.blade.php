@extends('layouts.dashboard')

@section('title', 'Users')
@section('page_heading', 'Users')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Users</div>
                    <div class="dash-panel-subtitle">All system users, including administrators, editors, students, and members.</div>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" style="margin-bottom:1rem;">
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

            @if ($users->isEmpty())
                <p class="dash-panel-subtitle">No users match your search.</p>
            @else
                <table class="dash-table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Created</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $u)
                        <tr>
                            <td>
                                <a href="{{ route('admin.users.edit', $u) }}" style="color:var(--zanupf-gold);font-weight:600;text-decoration:none;">{{ $u->name }} {{ $u->surname }}</a>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @php
                                    $roles = collect($u->roles ?? []);
                                @endphp
                                {{ $roles->map(fn($r)=>$r->slug ?? $r->name ?? '')->filter()->implode(', ') ?: '—' }}
                            </td>
                            <td>{{ optional($u->created_at)->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div style="margin-top:1rem;">
                    {{ $users->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection

