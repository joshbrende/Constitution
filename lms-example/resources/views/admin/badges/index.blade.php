@extends('layouts.admin')

@section('title', 'Badges')

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Badges</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Badges</h1>
        <a href="{{ route('admin.badges.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Create badge</a>
    </div>

    <p class="text-muted">Badges are awarded when users reach the required points. Set <code>points_required</code> to 0 to disable auto-award.</p>

    @if($badges->isEmpty())
    <div class="alert alert-info">No badges yet. <a href="{{ route('admin.badges.create') }}">Create one</a>.</div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Points required</th>
                    <th>Icon</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($badges as $b)
                <tr>
                    <td>
                        @if($b->icon)
                        <i class="bi {{ $b->icon }} me-2 text-secondary"></i>
                        @endif
                        {{ $b->name }}
                    </td>
                    <td><code>{{ $b->slug }}</code></td>
                    <td>{{ (int) $b->points_required }}</td>
                    <td>{{ $b->icon ?: '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.badges.edit', $b) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
