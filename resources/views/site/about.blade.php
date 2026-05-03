@extends('layouts.portal')

@section('title', 'About Us')

@section('content')

    <!-- Breadcrumb (Aligned with Content) -->
    <section class="bg-gray-50 py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center text-sm text-gray-500 space-x-2">
                <a href="{{ route('portal.home') }}"
                   class="flex items-center hover:text-primary-600 transition">
                    <i class="fas fa-home mr-1"></i>
                    Home
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="font-medium text-gray-900">About</span>
            </nav>
        </div>
    </section>

    <!-- Main Content -->
    <section class="bg-white py-10 lg:py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <article
                class="prose prose-lg max-w-none
                       prose-h1:text-3xl prose-h1:font-bold prose-h1:mb-4
                       prose-h2:text-2xl prose-h2:font-semibold prose-h2:mt-10
                       prose-p:text-gray-700 prose-p:leading-relaxed
                       prose-li:text-gray-700
                       prose-strong:text-gray-900
                       prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline">

                @if($siteSettings && !empty($siteSettings->about_content))
                    {!! clean($siteSettings->about_content) !!}
                @else
                    <div class="not-prose text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-full bg-gray-100">
                            <i class="fas fa-info-circle text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Content Coming Soon
                        </h3>
                        <p class="text-gray-500">
                            Halaman ini sedang dalam proses penyusunan konten.
                        </p>
                    </div>
                @endif

            </article>

        </div>
    </section>

@endsection
