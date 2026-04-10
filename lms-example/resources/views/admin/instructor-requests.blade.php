@extends('layouts.admin')

@section('title', 'Instructor requests')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Instructor requests</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Instructor requests</h1>
    <p class="text-muted mb-4">Facilitators request to instruct courses that have no facilitator. Approve to assign them; reject to decline.</p>

    @if($pendingCount > 0)
    <div class="alert alert-info">You have <strong>{{ $pendingCount }}</strong> pending request(s).</div>
    @endif

    @if($requests->isEmpty())
    <div class="alert alert-info">No instructor requests yet.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Course</th>
                    <th>Facilitator</th>
                    <th>Requested</th>
                    <th>Status</th>
                    <th>Decided</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                <tr>
                    <td>
                        <a href="{{ route('courses.show', $req->course) }}" class="text-decoration-none">{{ $req->course->title ?? '—' }}</a>
                    </td>
                    <td>{{ $req->user->name ?? 'User #' . $req->user_id }}</td>
                    <td><small class="text-muted">{{ $req->created_at?->format('d M Y H:i') }}</small></td>
                    <td>
                        @if($req->status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($req->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                        @else
                        <span class="badge bg-secondary">Rejected</span>
                        @endif
                    </td>
                    <td>
                        @if($req->decided_at)
                        <small>{{ $req->decided_at->format('d M Y') }} by {{ $req->decidedBy->name ?? '—' }}</small>
                        @if($req->admin_notes)<br><small class="text-muted">{{ $req->admin_notes }}</small>@endif
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($req->status === 'pending')
                        <form action="{{ route('admin.instructor-requests.approve', $req) }}" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $req->id }}">Reject</button>
                        <div class="modal fade" id="reject-{{ $req->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.instructor-requests.reject', $req) }}" method="post">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Reject {{ $req->user->name }}'s request to facilitate "{{ $req->course->title }}"?</p>
                                            <label for="notes-{{ $req->id }}" class="form-label">Note (optional, shown to you only)</label>
                                            <textarea name="admin_notes" id="notes-{{ $req->id }}" class="form-control" rows="2" placeholder="Reason for rejection (optional)">{{ old('admin_notes') }}</textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
