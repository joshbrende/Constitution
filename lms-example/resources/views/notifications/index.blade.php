@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifications</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="h2 mb-0">Notifications</h1>
        @if(auth()->user()->unreadNotifications()->exists())
        <form action="{{ route('notifications.mark-all-read') }}" method="post" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">Mark all as read</button>
        </form>
        @endif
    </div>

    @if($notifications->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-bell-slash me-2"></i>No notifications yet. You'll see updates about courses, enrollments, and achievements here.
    </div>
    @else
    <ul class="list-group">
        @foreach($notifications as $n)
        @php
            $d = is_array($n->data) ? $n->data : [];
            $msg = $d['message'] ?? 'Notification';
            $url = $d['action_url'] ?? route('notifications.index');
            $unread = $n->read_at === null;
        @endphp
        <li class="list-group-item d-flex justify-content-between align-items-start {{ $unread ? 'list-group-item-primary list-group-item-light' : '' }}">
            <div class="ms-2 me-auto">
                <a href="{{ route('notifications.read-and-go', $n->id) }}" class="{{ $unread ? 'fw-semibold' : '' }} text-decoration-none text-dark">{{ $msg }}</a>
                <div class="small text-muted mt-1">{{ $n->created_at->format('d M Y H:i') }}</div>
            </div>
            <a href="{{ route('notifications.read-and-go', $n->id) }}" class="btn btn-sm btn-outline-primary">View</a>
        </li>
        @endforeach
    </ul>
    <div class="mt-3">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
