@extends('setup.layout')

@section('title', 'Complete installation')
@section('heading', 'All done!')
@section('subheading')
    Review your settings and the <strong>production checklist</strong> below. Give the environment block
    to your hosting team before you mark installation complete.
@endsection

@section('content')
    <div class="section-heading" style="margin:0 0 10px;">
        <h3 style="margin:0;font-size:15px;">Platform summary (database)</h3>
        @include('setup.partials.field-tip', [
            'tip' => 'Settings saved by the wizard in your database. Compare with the environment block below before completing installation.',
            'aria' => 'Platform summary help',
        ])
    </div>
    <table class="kv" role="presentation" style="border:1px solid var(--line); border-radius:4px; padding:0 14px; display:block;">
        <tr>
            <td class="k">Organisation</td>
            <td class="v">{{ $defaults['org_name'] ?? 'ZANUPF' }}</td>
        </tr>
        <tr>
            <td class="k">Support email</td>
            <td class="v">{{ $defaults['support_email'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="k">Production URL</td>
            <td class="v">{{ $defaults['public_site_url'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="k">Dialogue enabled</td>
            <td class="v">{{ !empty($defaults['enable_dialogue']) ? 'yes' : 'no' }}</td>
        </tr>
        <tr>
            <td class="k">National ID required</td>
            <td class="v">{{ !empty($defaults['require_national_id']) ? 'yes' : 'no' }}</td>
        </tr>
    </table>

    <div class="section-heading" style="margin:22px 0 10px;">
        <h3 style="margin:0;font-size:15px;">For your technical team (hosting / .env)</h3>
        @include('setup.partials.field-tip', [
            'tip' => 'After updating the server environment, run <code>php artisan config:clear</code>.',
            'aria' => 'Technical team help',
        ])
    </div>
    @include('setup.partials.env-snippet', [
        'envRecommendations' => $envRecommendations,
        'snippetId' => 'env-snippet-finish',
    ])

    @include('setup.partials.production-checklist', [
        'productionChecklist' => $productionChecklist ?? [],
    ])

    <form id="setup-complete-form" method="POST" action="{{ route('setup.complete') }}">
        @csrf
    </form>
@endsection

@section('footer_left')
    <a href="{{ route('setup.seed') }}" class="footer-link">Back</a>
@endsection

@section('footer_right')
    @include('setup.partials.btn-next', [
        'label' => 'Complete installation',
        'form' => 'setup-complete-form',
    ])
@endsection
