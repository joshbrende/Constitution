@extends('layouts.admin')

@section('title', 'Tags')

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tags</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Tags</h1>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Create tag</a>
    </div>

    <p class="text-muted">Tags can be assigned to courses. Use them to filter the course catalog.</p>

    @if($tags->isEmpty())
    <div class="alert alert-info">No tags yet. <a href="{{ route('admin.tags.create') }}">Create one</a>.</div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th class="text-center">Courses</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tags as $t)
                <tr>
                    <td>{{ $t->name }}</td>
                    <td><code>{{ $t->slug }}</code></td>
                    <td class="text-center">{{ $t->courses_count ?? 0 }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.tags.edit', $t) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
