<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $journal->name }}</title>
    <meta name="description" content="{{ $journal->description ?? $journal->name }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $primaryColor = $settings['primary_color'] ?? '#4F46E5';
        $secondaryColor = $settings['secondary_color'] ?? '#7C3AED';
    @endphp

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .btn-primary:hover {
            filter: brightness(1.1);
        }

        .text-primary-custom {
            color: var(--primary-color);
        }

        .bg-primary-custom {
            background-color: var(--primary-color);
        }

        .border-primary-custom {
            border-color: var(--primary-color);
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Marquee animation for indexer logos */
        @keyframes marquee {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            animation: marquee 20s linear infinite;
        }

        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
</head>

<body class="antialiased bg-white" x-data="{ mobileMenuOpen: false }">

    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50 glass border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('journal.public.home', $journal->slug) }}" class="flex items-center space-x-3">
                    @if ($journal->logo_path)
                        <img src="{{ Storage::url($journal->logo_path) }}" alt="{{ $journal->name }}"
                            class="h-10 w-auto">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-primary-custom flex items-center justify-center"
                            style="background: var(--primary-color);">
                            <span
                                class="text-white font-bold text-sm">{{ strtoupper(substr($journal->abbreviation ?? $journal->name, 0, 2)) }}</span>
                        </div>
                    @endif
                    <div class="hidden sm:block">
                        <span class="font-bold text-gray-900">{{ $journal->abbreviation ?? $journal->name }}</span>
                    </div>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('journal.public.home', $journal->slug) }}"
                        class="text-sm font-medium {{ request()->routeIs('journal.public.home') ? 'text-primary-custom' : 'text-gray-600 hover:text-gray-900' }}"
                        style="{{ request()->routeIs('journal.public.home') ? 'color: var(--primary-color);' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('journal.public.current', $journal->slug) }}"
                        class="text-sm font-medium {{ request()->routeIs('journal.public.current') ? 'text-primary-custom' : 'text-gray-600 hover:text-gray-900' }}"
                        style="{{ request()->routeIs('journal.public.current') ? 'color: var(--primary-color);' : '' }}">
                        Current Issue
                    </a>
                    <a href="{{ route('journal.public.archives', $journal->slug) }}"
                        class="text-sm font-medium {{ request()->routeIs('journal.public.archives') ? 'text-primary-custom' : 'text-gray-600 hover:text-gray-900' }}"
                        style="{{ request()->routeIs('journal.public.archives') ? 'color: var(--primary-color);' : '' }}">
                        Archives
                    </a>
                    <a href="{{ route('journal.public.about', $journal->slug) }}"
                        class="text-sm font-medium {{ request()->routeIs('journal.public.about') ? 'text-primary-custom' : 'text-gray-600 hover:text-gray-900' }}"
                        style="{{ request()->routeIs('journal.public.about') ? 'color: var(--primary-color);' : '' }}">
                        About
                    </a>
                </div>

                {{-- Actions --}}
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('journal.dashboard', $journal->slug) }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Sign In
                        </a>
                    @endauth

                    <a href="{{ route('journal.submissions.create', $journal->slug) }}"
                        class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-full btn-primary shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all">
                        Submit Manuscript
                    </a>

                    {{-- Mobile Menu Button --}}
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-600">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden bg-white border-t border-gray-100">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('journal.public.home', $journal->slug) }}"
                    class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Home</a>
                <a href="{{ route('journal.public.current', $journal->slug) }}"
                    class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Current
                    Issue</a>
                <a href="{{ route('journal.public.archives', $journal->slug) }}"
                    class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Archives</a>
                <a href="{{ route('journal.public.about', $journal->slug) }}"
                    class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">About</a>
                <a href="{{ route('journal.submissions.create', $journal->slug) }}"
                    class="block px-4 py-2 text-sm font-medium text-white rounded-lg btn-primary text-center">Submit
                    Manuscript</a>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="pt-16">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Brand Column --}}
                <div class="md:col-span-1">
                    <div class="flex items-center space-x-3 mb-4">
                        @if ($journal->logo_path)
                            <img src="{{ Storage::url($journal->logo_path) }}" alt="{{ $journal->name }}"
                                class="h-10 w-auto">
                        @else
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                style="background: var(--primary-color);">
                                <span
                                    class="text-white font-bold text-sm">{{ strtoupper(substr($journal->abbreviation ?? $journal->name, 0, 2)) }}</span>
                            </div>
                        @endif
                        <span class="font-bold text-gray-900">{{ $journal->abbreviation ?? $journal->name }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ $settings['footer_description'] ?? ($journal->description ?? 'A peer-reviewed academic journal.') }}
                    </p>
                    @if ($journal->issn_print || $journal->issn_online)
                        <div class="text-xs text-gray-500 space-y-1">
                            @if ($journal->issn_print)
                                <p>Print ISSN: {{ $journal->issn_print }}</p>
                            @endif
                            @if ($journal->issn_online)
                                <p>Online ISSN: {{ $journal->issn_online }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Journal Links --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Journal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('journal.public.current', $journal->slug) }}"
                                class="text-gray-600 hover:text-gray-900">Current Issue</a></li>
                        <li><a href="{{ route('journal.public.archives', $journal->slug) }}"
                                class="text-gray-600 hover:text-gray-900">Archives</a></li>
                        <li><a href="{{ route('journal.public.about', $journal->slug) }}"
                                class="text-gray-600 hover:text-gray-900">About</a></li>
                        <li><a href="{{ route('journal.public.editorial-team', $journal->slug) }}"
                                class="text-gray-600 hover:text-gray-900">Editorial Board</a></li>
                    </ul>
                </div>

                {{-- For Authors --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">For Authors</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('journal.submissions.create', $journal->slug) }}"
                                class="text-gray-600 hover:text-gray-900">Submit Manuscript</a></li>
                        <li><a href="{{ route('journal.public.author-guidelines', $journal->slug) }}"
                                class="text-gray-600 hover:text-gray-900">Author Guidelines</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-gray-900">Publication Ethics</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-gray-900">Peer Review Process</a></li>
                    </ul>
                </div>

                {{-- Contact & Social --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        @if (!empty($settings['contact_email']))
                            <li class="flex items-center">
                                <i class="fa-solid fa-envelope w-5 text-gray-400"></i>
                                <a href="mailto:{{ $settings['contact_email'] }}"
                                    class="hover:text-gray-900">{{ $settings['contact_email'] }}</a>
                            </li>
                        @endif
                        @if (!empty($settings['contact_phone']))
                            <li class="flex items-center">
                                <i class="fa-solid fa-phone w-5 text-gray-400"></i>
                                <span>{{ $settings['contact_phone'] }}</span>
                            </li>
                        @endif
                        @if (!empty($settings['contact_address']))
                            <li class="flex items-center">
                                <i class="fa-solid fa-location-dot w-5 text-gray-400"></i>
                                <span>{{ $settings['contact_address'] }}</span>
                            </li>
                        @endif
                    </ul>

                    {{-- Social Links --}}
                    <div class="flex space-x-4">
                        @if (!empty($settings['social_facebook']))
                            <a href="{{ $settings['social_facebook'] }}" target="_blank"
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-blue-100 hover:text-blue-600 transition-colors">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                        @endif
                        @if (!empty($settings['social_twitter']))
                            <a href="{{ $settings['social_twitter'] }}" target="_blank"
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-sky-100 hover:text-sky-500 transition-colors">
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                        @endif
                        @if (!empty($settings['social_linkedin']))
                            <a href="{{ $settings['social_linkedin'] }}" target="_blank"
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-blue-100 hover:text-blue-700 transition-colors">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                        @endif
                        @if (!empty($settings['social_instagram']))
                            <a href="{{ $settings['social_instagram'] }}" target="_blank"
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-pink-100 hover:text-pink-600 transition-colors">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-gray-200 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} {{ $journal->name }}. All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0 text-sm text-gray-500">
                    <a href="#" class="hover:text-gray-900">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-900">Open Access Policy</a>
                    <a href="{{ route('portal.home') }}" class="hover:text-gray-900">IAMJOS Portal</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
