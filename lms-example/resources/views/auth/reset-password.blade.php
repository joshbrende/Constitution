@extends('layouts.app')

@section('title', 'Reset password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <p class="text-muted small mb-2">{{ config('app.name') }}</p>
                    <h2 class="h4 mb-4">Reset password</h2>
                    <p class="text-muted small mb-4">Enter your new password below.</p>
                    <form action="{{ route('password.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $email) }}" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reset password</button>
                    </form>
                    <p class="mt-3 mb-0 text-muted small"><a href="{{ route('login') }}">Back to login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
