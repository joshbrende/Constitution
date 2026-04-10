@extends('errors.layout-simple')

@section('title', 'Session expired')

@section('content')
    <h1>Session expired</h1>
    <p>This form has expired for security. Please refresh the page and try again.</p>
    <a href="{{ url()->previous() && url()->previous() !== url()->current() ? url()->previous() : url('/') }}">Go back</a>
@endsection

@section('code')
    Error 419
@endsection
