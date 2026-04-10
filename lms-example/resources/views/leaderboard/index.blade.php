@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="container">
    <h1 class="h2 mb-1">Leaderboard</h1>
    <p class="text-muted mb-4">Top learners by points. Earn points by enrolling, completing units, passing Knowledge Checks, and finishing courses.</p>

    @if($users->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-trophy me-2"></i>No users on the leaderboard yet. Start learning to earn points and climb the ranks!
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">#</th>
                    <th>Name</th>
                    <th class="text-center">Points</th>
                    <th>Badges</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $i => $u)
                <tr>
                    <td class="text-muted">{{ $users->firstItem() + $loop->index }}</td>
                    <td>{{ $u->name ?? 'User' }}</td>
                    <td class="text-center"><strong>{{ (int) ($u->points ?? 0) }}</strong></td>
                    <td>
                        @php $ubadges = $u->badges ?? collect(); @endphp
                        @forelse($ubadges as $b)
                        <span class="badge bg-secondary me-1" title="{{ $b->description ?? '' }}"><i class="{{ $b->icon ?? 'bi bi-award' }} me-1"></i>{{ $b->name }}</span>
                        @empty
                        <span class="text-muted">—</span>
                        @endforelse
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
