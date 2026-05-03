@extends('errors.layout')

@section('title', 'Page Not Found')

@section('icon')
<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" class="opacity-50" />
</svg>
@endsection

@section('message')
The manuscript or resource you are looking for has been moved, renamed, or no longer exists in our system.
@endsection
