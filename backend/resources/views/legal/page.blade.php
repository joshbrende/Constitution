@extends('layouts.public-legal')

@section('title', $page->title ?? 'Legal')
@section('content')
    <h1>{{ $page->title }}</h1>
    <div style="white-space:pre-wrap;color:#e5e7eb;font-size:0.95rem;line-height:1.65;">
        {{ $page->body }}
    </div>
@endsection

