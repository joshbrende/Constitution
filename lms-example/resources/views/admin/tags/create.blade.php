@extends('layouts.admin')

@section('title', 'Create tag')

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tags.index') }}">Tags</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <h1 class="h2 mb-4">Create tag</h1>

    <form action="{{ route('admin.tags.store') }}" method="post" class="card shadow-sm">
        @csrf
        <div class="card-body">
            @include('admin.tags._form', ['tag' => $tag])
        </div>
        <div class="card-footer bg-transparent">
            <button type="submit" class="btn btn-primary">Create tag</button>
            <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
