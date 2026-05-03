{{-- Master Public Layout Component for Journal Pages (OJS 3.3 Parity) --}}
@props(['journal', 'settings' => [], 'title' => null, 'description' => null])

@php
$primaryColor = $settings['primary_color'] ?? '#0369a1';
$secondaryColor = $settings['secondary_color'] ?? '#7c3aed';

// OJS 3.3 Header Logic:
// If homepage_image exists AND show_homepage_image_in_header is TRUE -> show as header background
// Otherwise -> show standard branding header
$showImageInHeader = $journal->homepage_image_path && $journal->show_homepage_image_in_header;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Basic SEO Meta Tags --}}
    <title>{{ $title ?? $journal->name ?? 'IAMJOS' }}</title>
    <meta name="description" content="{{ $description ?? ($journal->description ?? 'Open-access academic journal platform') }}">
    <meta name="keywords" content="{{ $journal->keywords ?? 'academic, journal, research, publication, open access' }}">
    <meta name="generator" content="IAMJOS - Indonesian Academic Journal System">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Default Open Graph Tags (can be overridden by child views) --}}
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? $journal->name }}">
    <meta property="og:description" content="{{ $description ?? Str::limit($journal->description ?? '', 200) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $journal->name ?? 'IAMJOS' }}">
    @if($journal->logo_path ?? false)
        <meta property="og:image" content="{{ Storage::url($journal->logo_path) }}">
    @endif

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title ?? $journal->name }}">
    <meta name="twitter:description" content="{{ $description ?? Str::limit($journal->description ?? '', 200) }}">

    {{-- ============================================ --}}
    {{-- GOOGLE SCHOLAR / HIGHWIRE PRESS META TAGS --}}
    {{-- Child views can push additional meta tags here --}}
    {{-- ============================================ --}}
    @stack('meta_tags')

    {{-- Dublin Core Metadata (Alternative for Scholar) --}}
    <meta name="DC.Title" content="{{ $title ?? $journal->name }}">
    <meta name="DC.Publisher" content="{{ $journal->publisher ?? $journal->name }}">
    @if($journal->issn_online)
        <meta name="DC.Identifier" content="ISSN {{ $journal->issn_online }}">
    @endif


    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&family=merriweather:400,700&display=swap" rel="stylesheet" />

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Dynamic CSS Variables --}}
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
            --primary-50: {{ $primaryColor }}10;
            --primary-100: {{ $primaryColor }}20;
        }

        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Gradient text utility */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Button styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 40px -10px var(--primary-color);
        }

        /* Card hover effect */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
        }

        /* Line clamp utilities */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Custom prose styling */
        .prose-journal {
            --tw-prose-body: #475569;
            --tw-prose-headings: #0f172a;
            --tw-prose-links: var(--primary-color);
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased font-sans bg-slate-50 text-slate-900" x-data="{ mobileMenuOpen: false }">

    @if($showImageInHeader)
        {{-- CASE 1: Homepage Image as Header Background --}}
        <header class="relative min-h-[200px] md:min-h-[280px] flex items-center"
                style="background-image: url('{{ Storage::url($journal->homepage_image_path) }}'); background-size: cover; background-position: center;">
            {{-- Dark Overlay for readability --}}
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/80 via-slate-900/60 to-slate-900/40"></div>
            
            {{-- Header Content --}}
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
                <div class="flex items-center space-x-6">
                    {{-- Logo --}}
                    @if($journal->logo_path)
                        <img src="{{ Storage::url($journal->logo_path) }}" 
                             alt="{{ $journal->name }}" 
                             class="h-20 md:h-24 w-auto drop-shadow-lg">
                    @else
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-xl flex items-center justify-center text-white text-3xl font-bold shadow-lg"
                             style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                            {{ strtoupper(substr($journal->abbreviation ?? $journal->name ?? 'J', 0, 2)) }}
                        </div>
                    @endif
                    
                    {{-- Journal Title & Description --}}
                    <div class="text-white">
                        <h1 class="text-2xl md:text-4xl font-serif font-bold drop-shadow-lg">
                            {{ $journal->name ?? 'Academic Journal' }}
                        </h1>
                        @if($journal->description)
                            <p class="text-sm md:text-base text-white/80 mt-2 max-w-2xl drop-shadow">
                                {{ Str::limit($journal->description, 150) }}
                            </p>
                        @endif
                        {{-- ISSN Badges --}}
                        @if($journal->issn_online || $journal->issn_print)
                            <div class="flex flex-wrap gap-3 mt-4">
                                @if($journal->issn_online)
                                    <span class="text-xs bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-white">
                                        e-ISSN: {{ $journal->issn_online }}
                                    </span>
                                @endif
                                @if($journal->issn_print)
                                    <span class="text-xs bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-white">
                                        p-ISSN: {{ $journal->issn_print }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>
    @else
        {{-- CASE 2: Standard Branding Header (No Background Image) --}}
        <header class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center space-x-4">
                    {{-- Logo --}}
                    @if($journal->logo_path)
                        <img src="{{ Storage::url($journal->logo_path) }}" 
                             alt="{{ $journal->name }}" 
                             class="h-16 w-auto">
                    @else
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold"
                             style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                            {{ strtoupper(substr($journal->abbreviation ?? $journal->name ?? 'J', 0, 2)) }}
                        </div>
                    @endif
                    
                    {{-- Journal Title & Description --}}
                    <div>
                        <h1 class="text-2xl md:text-3xl font-serif font-bold text-slate-900">
                            {{ $journal->name ?? 'Academic Journal' }}
                        </h1>
                        @if($journal->description)
                            <p class="text-sm text-slate-600 mt-1 max-w-2xl">
                                {{ Str::limit($journal->description, 150) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </header>
    @endif

    {{-- Dynamic Navigation Bar --}}
    <x-public.navbar :journal="$journal" :primary-menu="$primaryMenu ?? collect()" :user-menu="$userMenu ?? collect()" />

    {{-- MAIN LAYOUT CONTAINER --}}
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- FLEX WRAPPER: Column on Mobile, Row on Desktop --}}
            <div class="flex flex-col lg:flex-row gap-8 items-start">

                {{-- LEFT COLUMN: Main Content --}}
                <main class="w-full {{ $sidebarBlocks->isNotEmpty() ? 'lg:w-3/4' : '' }} min-w-0 transition-all duration-300">
                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Page Content --}}
                    {{ $slot }}
                </main>

                {{-- RIGHT COLUMN: Sidebar (Only render if blocks exist) --}}
              
                @if($sidebarBlocks->isNotEmpty())
                    <aside class="w-full lg:w-1/4 flex-shrink-0 space-y-6">
                        <x-public.sidebar :journal="$journal" />
                    </aside>
                @endif

            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Journal Info --}}
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        @if($journal->logo_path)
                            <img src="{{ Storage::url($journal->logo_path) }}" 
                                 alt="{{ $journal->name }}" 
                                 class="h-12 w-auto brightness-0 invert opacity-80">
                        @else
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold"
                                 style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                {{ strtoupper(substr($journal->abbreviation ?? 'J', 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $journal->abbreviation ?? $journal->name }}</h3>
                            <p class="text-xs text-slate-400">{{ $journal->name }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 mb-4 max-w-md">
                        {{ $settings['footer_description'] ?? ($journal->description ?? 'A peer-reviewed scholarly journal.') }}
                    </p>
                    @if($journal->issn_online || $journal->issn_print)
                        <div class="flex flex-wrap gap-4 text-sm">
                            @if($journal->issn_online)
                                <span class="bg-slate-800 px-3 py-1 rounded-full text-slate-300">
                                    <i class="fa-solid fa-globe mr-1 text-slate-500"></i>
                                    e-ISSN: {{ $journal->issn_online }}
                                </span>
                            @endif
                            @if($journal->issn_print)
                                <span class="bg-slate-800 px-3 py-1 rounded-full text-slate-300">
                                    <i class="fa-solid fa-print mr-1 text-slate-500"></i>
                                    p-ISSN: {{ $journal->issn_print }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Journal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('journal.public.current', $journal->slug) }}" class="text-slate-400 hover:text-white transition-colors">Current Issue</a></li>
                        <li><a href="{{ route('journal.public.archives', $journal->slug) }}" class="text-slate-400 hover:text-white transition-colors">Archives</a></li>
                        <li><a href="{{ route('journal.public.about', $journal->slug) }}" class="text-slate-400 hover:text-white transition-colors">About</a></li>
                        <li><a href="{{ route('journal.public.editorial-team', $journal->slug) }}" class="text-slate-400 hover:text-white transition-colors">Editorial Board</a></li>
                    </ul>
                </div>

                {{-- For Authors --}}
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">For Authors</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('journal.public.author-guidelines', $journal->slug) }}" class="text-slate-400 hover:text-white transition-colors">Author Guidelines</a></li>
                        <li><a href="{{ route('journal.submissions.create', $journal->slug) }}" class="text-slate-400 hover:text-white transition-colors">Submit Manuscript</a></li>
                        <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition-colors">Login / Register</a></li>
                    </ul>

                    {{-- Social Links --}}
                    @if(!empty($settings['social_facebook']) || !empty($settings['social_twitter']) || !empty($settings['social_linkedin']))
                        <div class="flex space-x-3 mt-6">
                            @if(!empty($settings['social_facebook']))
                                <a href="{{ $settings['social_facebook'] }}" target="_blank" 
                                   class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                            @endif
                            @if(!empty($settings['social_twitter']))
                                <a href="{{ $settings['social_twitter'] }}" target="_blank"
                                   class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-sky-500 hover:text-white transition-all">
                                    <i class="fa-brands fa-x-twitter"></i>
                                </a>
                            @endif
                            @if(!empty($settings['social_linkedin']))
                                <a href="{{ $settings['social_linkedin'] }}" target="_blank"
                                   class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-blue-700 hover:text-white transition-all">
                                    <i class="fa-brands fa-linkedin-in"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Custom Page Footer (from admin settings) --}}
            @if($journal->page_footer)
                <div class="border-t border-slate-800 mt-8 pt-8">
                    <div class="prose prose-sm prose-invert max-w-none text-slate-400">
                        {!! clean($journal->page_footer) !!}
                    </div>
                </div>
            @endif

            {{-- Bottom Bar --}}
            <div class="border-t border-slate-800 mt-8 pt-8 flex flex-col md:flex-row items-center justify-between text-sm text-slate-500">
                <p>
                    © {{ date('Y') }} {{ $journal->name ?? 'IAMJOS' }}. 
                    @if($journal->publisher)
                        Published by {{ $journal->publisher }}.
                    @endif
                </p>
                <div class="mt-4 md:mt-0 flex items-center space-x-4">
                    <a href="{{ route('portal.home') }}" class="hover:text-white transition-colors">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Back to Portal
                    </a>
                    <span class="text-slate-700">|</span>
                    <span class="text-slate-600">Powered by <strong class="text-slate-400">IAMJOS</strong></span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
