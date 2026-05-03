@extends('layouts.portal')

@section('title', $page->title)

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="mb-6 text-sm text-gray-500">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ route('portal.home') }}" class="hover:text-blue-600">
                        <i class="fa-solid fa-house"></i>
                    </a>
                </li>
                <li><i class="fa-solid fa-chevron-right text-xs text-gray-400"></i></li>
                <li class="text-gray-900 font-medium">{{ $page->title }}</li>
            </ol>
        </nav>

        {{-- Page Content Card --}}
        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <header class="px-6 py-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h1 class="text-3xl font-bold text-gray-900">{{ $page->title }}</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Last updated: {{ $page->updated_at->format('F j, Y') }}
                </p>
            </header>

            {{-- Content --}}
            <div class="px-6 py-8">
                <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline">
                    {!! clean($page->content) !!}
                </div>
            </div>
        </article>

        {{-- Back Link --}}
        <div class="mt-6 text-center">
            <a href="{{ route('portal.home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
