@extends('layouts.dashboard')

@section('title', 'Roles')
@section('page_heading', 'Roles')

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
                    <div class="dash-panel-title">Roles</div>
                    <div class="dash-panel-subtitle">Create and manage roles for backend administration. Assign roles to users from the Users page.</div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.roles.create') }}" class="dash-btn-ghost" style="text-decoration:none;">+ Add role</a>
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            @if ($roles->isEmpty())
                <p style="color:var(--text-muted);margin-top:1rem;">No roles yet.</p>
                <a href="{{ route('admin.roles.create') }}" style="display:inline-block;margin-top:0.75rem;color:var(--zanupf-gold);font-weight:600;">Create first role</a>
            @else
                <table class="dash-table" style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td><strong>{{ $role->name }}</strong></td>
                                <td><code style="font-size:0.8rem;">{{ $role->slug }}</code></td>
                                <td>{{ $role->description ?: '—' }}</td>
                                <td>{{ $role->users_count }}</td>
                                <td style="white-space:nowrap;">
                                    <a href="{{ route('admin.roles.edit', $role) }}" style="font-size:0.8rem;color:var(--zanupf-gold);margin-right:0.75rem;">Edit</a>
                                    @if ($role->users_count === 0)
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="display:inline;" onsubmit="return confirm('Delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="font-size:0.8rem;color:var(--zanupf-red);background:none;border:none;cursor:pointer;padding:0;">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection
