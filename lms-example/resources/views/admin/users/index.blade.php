@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
    </nav>

    <h1 class="h2 mb-4">Users</h1>

    @if($users->isEmpty())
    <div class="alert alert-info">No users yet.</div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{ $u->name }}@if($u->surname) {{ $u->surname }}@endif</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge bg-secondary">{{ $u->roles->first()?->name ?? '—' }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
