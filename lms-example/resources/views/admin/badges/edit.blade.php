@extends('layouts.admin')

@section('title', 'Edit: ' . $badge->name)

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.badges.index') }}">Badges</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <h1 class="h2 mb-4">Edit badge</h1>

    <form action="{{ route('admin.badges.update', $badge) }}" method="post" class="card shadow-sm">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('admin.badges._form', ['badge' => $badge])
        </div>
        <div class="card-footer bg-transparent">
            <button type="submit" class="btn btn-primary">Update badge</button>
            <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
