<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>{{ $settings['site_title'] ?? 'IAMJOS' }} - Indonesian Academic Journal System</title>
    <meta name="description" content="{{ $settings['site_description'] ?? 'Discover peer-reviewed academic journals and research articles across multiple disciplines.' }}">
    <meta name="keywords" content="academic journals, research, publications, open access, Indonesia, scholarly articles">
    <meta name="generator" content="IAMJOS - Indonesian Academic Journal System">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/webp" href="{{ asset('assets/media/logos/logo.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/media/logos/logo.webp') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $settings['site_title'] ?? 'IAMJOS' }}">
    <meta property="og:description" content="{{ $settings['site_description'] ?? 'Discover peer-reviewed academic journals.' }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="IAMJOS">
    <meta property="og:image" content="{{ asset('assets/media/logos/logo.webp') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $settings['site_title'] ?? 'IAMJOS' }}">
    <meta name="twitter:description" content="{{ $settings['site_description'] ?? 'Discover peer-reviewed academic journals.' }}">
    <meta name="twitter:image" content="{{ asset('assets/media/logos/logo.webp') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Line clamp utilities */
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>

    @stack('styles')
</head>

<body class="antialiased font-sans bg-gray-50 text-gray-900">
    {{-- ============================================ --}}
    {{-- DYNAMIC NAVIGATION BAR --}}
    {{-- ============================================ --}}
    <x-site.navbar :primaryMenu="$primaryMenu ?? null" :userMenu="$userMenu ?? null" :settings="$settings ?? []" />

    {{-- ============================================ --}}
    {{-- DYNAMIC CONTENT BLOCKS --}}
    {{-- Render active blocks in sort_order --}}
    {{-- ============================================ --}}
    <main>
        @foreach($blocks as $block)
            @php
                $componentName = 'site.blocks.' . str_replace('_', '-', $block->key);
                $currentBlockData = $blockData[$block->key] ?? [];
            @endphp

            @if(View::exists("components.{$componentName}"))
                <x-dynamic-component :component="$componentName" :block="$block" :data="$currentBlockData" />
            @else
                {{-- Fallback for missing components (dev mode) --}}
                @if(config('app.debug'))
                    <div class="bg-yellow-50 border border-yellow-200 p-4 text-center text-yellow-700">
                        <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                        Missing component: {{ $componentName }}
                    </div>
                @endif
            @endif
        @endforeach
    </main>

    {{-- ============================================ --}}
    {{-- DYNAMIC FOOTER --}}
    {{-- ============================================ --}}
    <x-site.footer :footerMenu="$footerMenu ?? null" :settings="$settings ?? []" />
    @livewireScripts
    @stack('scripts')
</body>

</html>
