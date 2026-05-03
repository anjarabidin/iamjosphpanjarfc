@extends('errors.layout')

@section('title', 'Access Restricted')

@section('icon')
<div class="relative">
    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
    </svg>
    <div class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></div>
</div>
@endsection

@section('message')
Your account's Permission Level is not authorized to access this secure resource. Please contact the Journal Manager if you believe this is an error.
@endsection
