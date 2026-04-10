@extends('errors.layout-simple')

@section('title', 'Service unavailable')

@section('content')
    <h1>Service unavailable</h1>
    <p>The site is temporarily busy or undergoing maintenance. Please try again shortly.</p>
    <a href="{{ url('/') }}">Back to home</a>
@endsection

@section('code')
    Error 503
@endsection
