@extends('layouts.dashboard')

@section('title', 'Static pages')
@section('page_heading', 'Static pages')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Static pages</div>
                    <div class="dash-panel-subtitle">
                        Manage Help, Terms &amp; Conditions, Privacy policy and other static content shown in the app.
                    </div>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
            </div>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif

            <table class="dash-table">
                <thead>
                <tr>
                    <th>Slug</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($pages as $page)
                    <tr>
                        <td><code style="font-size:0.8rem;">{{ $page->slug }}</code></td>
                        <td>{{ $page->title }}</td>
                        <td>
                            @if($page->is_published)
                                <span class="status-pill status-pill--active"><span class="dot"></span>Published</span>
                            @else
                                <span class="status-pill status-pill--pending"><span class="dot"></span>Hidden</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.static-pages.edit', $page) }}" style="font-size:0.8rem;color:var(--zanupf-gold);">
                                Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    </div>
@endsection

