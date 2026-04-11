@extends('layouts.auth')

@section('title', 'Login – ZANU PF Academy')

@section('content')
    <h1 class="auth-title">Sign in</h1>
    <p class="auth-subtitle">
        Secure access for administrators, instructors, and members to manage and study the Constitution.
    </p>

    @if (session('status'))
        <div class="helper-text" role="status" style="color:#bbf7d0;margin-bottom:0.75rem;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="helper-text" role="alert" style="color:#fecaca;margin-bottom:0.75rem;">
            The credentials do not match our records. Please try again.
        </div>
    @endif

    <form method="POST" action="{{ url('/login') }}">
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
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="password-toggle" data-toggle-password="password">
                    Show
                </button>
            </div>
        </div>

        <div class="checkbox-row">
            <input
                id="remember"
                type="checkbox"
                name="remember"
                value="1"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label for="remember" style="margin-bottom:0;">
                Keep me signed in on this device.
            </label>
        </div>

        <div class="actions-row">
            <button type="submit" class="btn-primary">
                <span>Log in</span>
            </button>

            <a href="{{ route('password.request') }}" class="text-link">
                Forgot password?
            </a>

            <a href="{{ route('register') }}" class="text-link">
                Need an account? Register
            </a>
        </div>
    </form>
@endsection

