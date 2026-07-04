@extends('setup.layout')

@section('title', 'Administrator account')
@section('heading', 'Administrator account')
@section('subheading')
    Create the first <strong>system administrator</strong>. This account manages roles, settings,
    and platform access for your organisation.
@endsection

@section('content')
    @if ($errors->any())
        <div class="err">Please fix the highlighted fields and try again.</div>
    @endif

    <form id="setup-admin-form" method="POST" action="{{ route('setup.admin.store') }}">
        @csrf
        <div class="grid">
            <div class="row">
                @include('setup.partials.field-label', [
                    'for' => 'name',
                    'text' => 'First name',
                    'tip' => 'Legal first name for the primary system administrator. Shown on the account profile.',
                    'tipAria' => 'First name help',
                ])
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
                @error('name')<div style="color:#991b1b;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                @include('setup.partials.field-label', [
                    'for' => 'surname',
                    'text' => 'Surname',
                    'tip' => 'Family name for the administrator account.',
                    'tipAria' => 'Surname help',
                ])
                <input id="surname" name="surname" type="text" value="{{ old('surname') }}" required>
                @error('surname')<div style="color:#991b1b;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>
            <div class="row full">
                @include('setup.partials.field-label', [
                    'for' => 'email',
                    'text' => 'Work email',
                    'tip' => 'Used to sign in to the admin portal and receive system notifications. Must be unique and reachable.',
                    'tipAria' => 'Email help',
                ])
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@zanupf.org.zw" required>
                @error('email')<div style="color:#991b1b;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                @include('setup.partials.field-label', [
                    'for' => 'password',
                    'text' => 'Password',
                    'tip' => 'Minimum 8 characters. Use a strong password that is not shared or reused on other sites.',
                    'tipAria' => 'Password help',
                ])
                <input id="password" name="password" type="password" required autocomplete="new-password">
                @error('password')<div style="color:#991b1b;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                @include('setup.partials.field-label', [
                    'for' => 'password_confirmation',
                    'text' => 'Confirm password',
                    'tip' => 'Re-enter the password exactly as above to confirm there are no typos.',
                    'tipAria' => 'Confirm password help',
                ])
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
            </div>
        </div>

        <div class="hint">
            Use a strong password (minimum 8 characters). Credentials are not emailed from this wizard.
        </div>
    </form>
@endsection

@section('footer_left')
    <a href="{{ route('setup.checks') }}" class="footer-link">Back to system checks</a>
@endsection

@section('footer_right')
    @include('setup.partials.btn-next', [
        'label' => 'Create administrator',
        'form' => 'setup-admin-form',
    ])
@endsection
