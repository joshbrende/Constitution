@extends('layouts.auth')

@section('title', 'Forgot password – ZANU PF Academy')

@section('content')
    <h1 class="auth-title">Forgot your password?</h1>
    <p class="auth-subtitle">
        Enter your email address and we will send you a secure link to reset your password.
    </p>

    @if (session('status'))
        <div class="helper-text" style="color:#bbf7d0;margin-bottom:0.75rem;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="helper-text" style="color:#fecaca;margin-bottom:0.75rem;">
            {{ $errors->first('email') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
            >
        </div>

        <div class="actions-row">
            <button type="submit" class="btn-primary">
                <span>Send reset link</span>
            </button>

            <a href="{{ route('login') }} " class="text-link">
                Back to login
            </a>
        </div>
    </form>
@endsection

