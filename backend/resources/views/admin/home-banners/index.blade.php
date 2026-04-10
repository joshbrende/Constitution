@extends('layouts.dashboard')

@section('title', 'Home banners')
@section('page_heading', 'Home banners')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Home banner carousel</div>
                    <div class="dash-panel-subtitle">
                        Manage promotional banners shown under the Overview header in the mobile app (e.g. Vision 2030, how to become a member).
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <a href="{{ route('admin.home-banners.create') }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">
                        Add banner
                    </a>
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif

            <table class="dash-table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>CTA</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($banners as $banner)
                    <tr>
                        <td>{{ $banner->title }}</td>
                        <td>{{ $banner->subtitle ?? '—' }}</td>
                        <td>
                            @if($banner->cta_label)
                                <code style="font-size:0.8rem;">{{ $banner->cta_label }}</code>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($banner->is_published)
                                <span class="status-pill status-pill--active"><span class="dot"></span>Published</span>
                            @else
                                <span class="status-pill status-pill--pending"><span class="dot"></span>Hidden</span>
                            @endif
                        </td>
                        <td>{{ $banner->sort_order }}</td>
                        <td>
                            <a href="{{ route('admin.home-banners.edit', $banner) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.home-banners.destroy', $banner) }}" style="display:inline;" onsubmit="return confirm('Delete this banner?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="dash-panel-subtitle">No banners yet. Add one to promote Vision 2030, membership, or campaigns on the mobile overview.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection

