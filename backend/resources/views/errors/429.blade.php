@extends('errors.layout-simple')

@section('title', 'Too many requests')

@section('content')
    <h1>Slow down</h1>
    <p>You have sent too many requests. Please wait a moment and try again.</p>
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}">Go back</a>
@endsection

@section('code')
    Error 429
@endsection
