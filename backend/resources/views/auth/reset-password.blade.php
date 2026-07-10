@extends('layouts.auth')

@section('title', 'Reset password – ZANU PF Academy')

@section('content')
    <h1 class="auth-title">Choose a new password</h1>
    <p class="auth-subtitle">
        Enter your email and a new password to finish resetting your account.
    </p>

    @if ($errors->any())
        <div class="helper-text" role="alert" style="color:#fecaca;margin-bottom:0.75rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $email) }}"
                required
                autocomplete="email"
                placeholder="you@example.org.zw"
            >
        </div>

        <div class="form-group">
            <label for="password">New password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            >
            <p class="helper-text" style="margin-top:0.35rem;">
                At least 8 characters, with upper and lower case letters and a number.
            </p>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            >
        </div>

        <div class="actions-row">
            <button type="submit" class="btn-primary">
                <span>Reset password</span>
            </button>

            <a href="{{ route('login') }}" class="text-link">
                Back to login
            </a>
        </div>
    </form>
@endsection
