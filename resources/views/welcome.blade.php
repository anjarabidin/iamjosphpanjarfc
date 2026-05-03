<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $content['hero_title'] ?? 'IAMJOS' }} - Portal Jurnal Akademik</title>
    <meta name="description" content="{{ $content['hero_subtitle'] ?? 'Platform Jurnal Akademik Indonesia' }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                            950: '#2e1065',
                        },
                        accent: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #8b5cf6; }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .glass-dark {
            background: rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Hero Background Pattern */
        .hero-pattern {
            background-image:
                radial-gradient(at 40% 20%, hsla(262, 83%, 58%, 0.3) 0px, transparent 50%),
                radial-gradient(at 80% 0%, hsla(225, 89%, 67%, 0.2) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(280, 75%, 70%, 0.2) 0px, transparent 50%),
                radial-gradient(at 80% 50%, hsla(340, 65%, 68%, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(269, 100%, 77%, 0.2) 0px, transparent 50%),
                radial-gradient(at 80% 100%, hsla(240, 80%, 70%, 0.15) 0px, transparent 50%);
        }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* Card hover effects */
        .journal-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .journal-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.25);
        }

        /* Subject card hover */
        .subject-card {
            transition: all 0.3s ease;
        }
        .subject-card:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body class="font-sans bg-gray-50 text-gray-900 antialiased" x-data="{ 
    mobileMenu: false,
    scrolled: false
}" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
            :class="scrolled ? 'bg-white/95 backdrop-blur-lg shadow-lg' : 'bg-transparent'">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('portal.home') }}" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 via-accent-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-primary-500/30 transition-shadow">
                        <span class="text-white font-bold text-xl font-display">I</span>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-bold font-display" :class="scrolled ? 'text-gray-900' : 'text-white'">IAMJOS</h1>
                        <p class="text-xs -mt-0.5" :class="scrolled ? 'text-gray-500' : 'text-white/70'">Portal Jurnal Akademik</p>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('portal.journals') }}" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-primary-600' : 'text-white/90 hover:text-white'">
                        Jurnal
                    </a>
                    <a href="#subjects" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-primary-600' : 'text-white/90 hover:text-white'">
                        Subjek
                    </a>
                    <a href="{{ route('portal.about') }}" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-primary-600' : 'text-white/90 hover:text-white'">
                        Tentang Kami
                    </a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="hidden sm:inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-xl bg-white text-primary-600 hover:bg-primary-50 transition-colors shadow-lg">
                            <i class="fas fa-th-large mr-2"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-primary-600' : 'text-white/90 hover:text-white'">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-xl bg-white text-primary-600 hover:bg-primary-50 transition-colors shadow-lg">
                            Daftar
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg" :class="scrolled ? 'text-gray-600' : 'text-white'">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="lg:hidden bg-white border-t shadow-xl">
            <div class="px-4 py-6 space-y-4">
                <a href="{{ route('portal.journals') }}" @click="mobileMenu = false" class="block text-gray-600 hover:text-primary-600 font-medium">Jurnal</a>
                <a href="#subjects" @click="mobileMenu = false" class="block text-gray-600 hover:text-primary-600 font-medium">Subjek</a>
                <a href="{{ route('portal.about') }}" @click="mobileMenu = false" class="block text-gray-600 hover:text-primary-600 font-medium">Tentang Kami</a>
                @guest
                    <a href="{{ route('login') }}" class="block text-gray-600 hover:text-primary-600 font-medium">Masuk</a>
                @endguest
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center bg-gradient-to-br from-primary-900 via-primary-800 to-accent-900 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 hero-pattern"></div>

        <!-- Floating Shapes -->
        <div class="absolute top-20 left-10 w-64 h-64 bg-primary-500/20 rounded-full blur-3xl float-animation"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-accent-500/20 rounded-full blur-3xl float-animation" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/3 w-40 h-40 bg-pink-500/15 rounded-full blur-2xl float-animation" style="animation-delay: -1.5s;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 lg:py-40">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-8">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></span>
                    <span class="text-white/90 text-sm font-medium">Platform Open Access Terpercaya</span>
                </div>

                <!-- Title -->
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold font-display text-white mb-6 leading-tight">
                    {{ $content['hero_title'] ?? 'Temukan Pengetahuan,<br>Bagikan Inovasi' }}
                </h1>

                <!-- Subtitle -->
                <p class="text-lg sm:text-xl text-white/80 max-w-3xl mx-auto mb-10 leading-relaxed">
                    {{ $content['hero_subtitle'] ?? 'Platform jurnal akademik terbuka untuk peneliti, akademisi, dan institusi di seluruh Indonesia. Akses ribuan artikel ilmiah secara gratis.' }}
                </p>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto mb-10">
                    <form action="{{ route('portal.search') }}" method="GET" class="relative">
                        <div class="flex items-center bg-white rounded-2xl shadow-2xl overflow-hidden">
                            <div class="flex-1 relative">
                                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="q" 
                                       placeholder="{{ $content['hero_search_placeholder'] ?? 'Cari jurnal, artikel, atau penulis...' }}"
                                       class="w-full pl-14 pr-4 py-5 text-gray-700 placeholder-gray-400 focus:outline-none text-lg">
                            </div>
                            <button type="submit" class="px-8 py-5 bg-gradient-to-r from-primary-600 to-accent-600 text-white font-semibold hover:from-primary-700 hover:to-accent-700 transition-all">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Popular Tags -->
                @if(!empty($content['hero_popular_tags']))
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <span class="text-white/60 text-sm">Populer:</span>
                        @foreach($content['hero_popular_tags'] as $tag)
                            <a href="{{ route('portal.search', ['q' => $tag]) }}" 
                               class="px-4 py-1.5 text-sm bg-white/10 hover:bg-white/20 text-white rounded-full transition-colors border border-white/20">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2">
            <a href="#stats" class="block animate-bounce">
                <i class="fas fa-chevron-down text-white/50 text-2xl"></i>
            </a>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-16 bg-white relative -mt-16 z-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Journals -->
                <div class="bg-gradient-to-br from-primary-50 to-accent-50 rounded-2xl p-8 text-center border border-primary-100 shadow-xl shadow-primary-500/5">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-book-open text-white text-2xl"></i>
                    </div>
                    <p class="text-4xl lg:text-5xl font-bold font-display text-primary-700 mb-2">{{ number_format($stats['journals'] ?? 0) }}</p>
                    <p class="text-gray-600 font-medium">Jurnal Aktif</p>
                </div>

                <!-- Articles -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 text-center border border-green-100 shadow-xl shadow-green-500/5">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <p class="text-4xl lg:text-5xl font-bold font-display text-green-700 mb-2">{{ number_format($stats['articles'] ?? 0) }}</p>
                    <p class="text-gray-600 font-medium">Artikel Terbit</p>
                </div>

                <!-- Authors -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-8 text-center border border-amber-100 shadow-xl shadow-amber-500/5">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <p class="text-4xl lg:text-5xl font-bold font-display text-amber-700 mb-2">{{ number_format($stats['authors'] ?? 0) }}</p>
                    <p class="text-gray-600 font-medium">Penulis Terdaftar</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Journals Section -->
    <section id="journals" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 text-sm font-medium text-primary-600 bg-primary-100 rounded-full mb-4">
                    {{ $content['featured_title'] ?? 'Jurnal Pilihan' }}
                </span>
                <h2 class="text-3xl lg:text-4xl font-bold font-display text-gray-900 mb-4">
                    Jelajahi Jurnal Unggulan
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    {{ $content['featured_subtitle'] ?? 'Koleksi jurnal ilmiah terbaik dari berbagai bidang keilmuan' }}
                </p>
            </div>

            <!-- Journals Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($featuredJournals as $journal)
                    <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}" class="journal-card group bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                        <!-- Cover Image -->
                        <div class="relative h-48 bg-gradient-to-br from-primary-500 to-accent-600 overflow-hidden">
                            @if($journal->cover_image)
                                <img src="{{ asset('storage/' . $journal->cover_image) }}" alt="{{ $journal->name }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-6xl font-bold text-white/30 font-display">{{ substr($journal->abbreviation ?? $journal->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4">
                                <span class="inline-block px-3 py-1 text-xs font-medium bg-white/20 backdrop-blur-sm text-white rounded-full">
                                    {{ $journal->abbreviation ?? 'Journal' }}
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">
                                {{ $journal->name }}
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit(strip_tags($journal->description), 100) }}
                            </p>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">
                                    <i class="fas fa-file-alt mr-1"></i>
                                    {{ $journal->submissions_count ?? 0 }} Artikel
                                </span>
                                <span class="text-primary-600 font-medium group-hover:translate-x-1 transition-transform">
                                    Lihat <i class="fas fa-arrow-right ml-1"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <i class="fas fa-book text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Belum ada jurnal yang tersedia</p>
                    </div>
                @endforelse
            </div>

            <!-- View All Button -->
            @if(count($featuredJournals) > 0)
                <div class="text-center mt-12">
                    <a href="{{ route('portal.journals') }}" 
                       class="inline-flex items-center px-8 py-4 text-sm font-semibold text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-xl transition-colors">
                        Lihat Semua Jurnal
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Browse by Subject Section -->
    <section id="subjects" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 text-sm font-medium text-accent-600 bg-accent-100 rounded-full mb-4">
                    Eksplorasi
                </span>
                <h2 class="text-3xl lg:text-4xl font-bold font-display text-gray-900 mb-4">
                    Jelajahi Berdasarkan Bidang
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Temukan jurnal dan artikel berdasarkan bidang keilmuan yang Anda minati
                </p>
            </div>

            <!-- Subjects Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($subjects ?? [] as $subject)
                    <a href="{{ route('portal.search', ['subject' => $subject['name'] ?? $subject]) }}" 
                       class="subject-card group p-6 rounded-2xl border-2 border-gray-100 hover:border-{{ $subject['color'] ?? 'primary' }}-300 bg-white hover:bg-{{ $subject['color'] ?? 'primary' }}-50 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-{{ $subject['color'] ?? 'primary' }}-100 text-{{ $subject['color'] ?? 'primary' }}-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-{{ $subject['icon'] ?? 'folder' }} text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-{{ $subject['color'] ?? 'primary' }}-700">
                            {{ $subject['name'] ?? $subject }}
                        </h3>
                    </a>
                @empty
                    <div class="col-span-4 text-center py-8">
                        <p class="text-gray-500">Subjek akan ditampilkan di sini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Latest Articles Section -->
    @if(count($latestArticles ?? []) > 0)
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-12">
                <div>
                    <span class="inline-block px-4 py-1.5 text-sm font-medium text-green-600 bg-green-100 rounded-full mb-4">
                        Terbaru
                    </span>
                    <h2 class="text-3xl lg:text-4xl font-bold font-display text-gray-900">
                        Artikel Terbaru
                    </h2>
                </div>
                <a href="{{ route('portal.search') }}" class="mt-4 sm:mt-0 text-primary-600 font-medium hover:text-primary-700">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Articles List -->
            <div class="space-y-6">
                @foreach($latestArticles as $article)
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-3 py-1 text-xs font-medium bg-primary-100 text-primary-700 rounded-full">
                                        {{ $article->journal->abbreviation ?? 'Journal' }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 hover:text-primary-600 transition-colors">
                                    <a href="#">{{ $article->title }}</a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ Str::limit(strip_tags($article->abstract), 200) }}
                                </p>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span><i class="fas fa-user mr-1"></i> {{ $article->submitter->name ?? 'Unknown' }}</span>
                                </div>
                            </div>
                            <a href="#" class="shrink-0 inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors">
                                Baca <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-primary-600 via-accent-600 to-primary-800 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold font-display text-white mb-6">
                Siap Berkontribusi dalam Dunia Akademik?
            </h2>
            <p class="text-lg text-white/80 mb-10 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan peneliti dan akademisi yang telah mempercayakan karya mereka di platform kami.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-primary-600 bg-white hover:bg-gray-100 rounded-xl transition-colors shadow-xl">
                        <i class="fas fa-user-plus mr-2"></i>
                        Daftar Sekarang
                    </a>
                @endguest
                <a href="#journals" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-white/10 hover:bg-white/20 rounded-xl transition-colors border border-white/30">
                    <i class="fas fa-book-open mr-2"></i>
                    Jelajahi Jurnal
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="about" class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- About -->
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 via-accent-500 to-pink-500 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-xl font-display">I</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold font-display">IAMJOS</h3>
                            <p class="text-sm text-gray-400">Portal Jurnal Akademik</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md leading-relaxed">
                        {{ $content['footer_about'] ?? 'IAMJOS adalah platform open-access untuk publikasi dan penyebaran karya ilmiah di Indonesia. Kami berkomitmen untuk memajukan dunia akademik dengan menyediakan akses mudah ke pengetahuan berkualitas.' }}
                    </p>
                    <!-- Social Links -->
                    <div class="flex items-center space-x-4">
                        @if(!empty($content['social_facebook']))
                            <a href="{{ $content['social_facebook'] }}" target="_blank" class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if(!empty($content['social_twitter']))
                            <a href="{{ $content['social_twitter'] }}" target="_blank" class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        @if(!empty($content['social_instagram']))
                            <a href="{{ $content['social_instagram'] }}" target="_blank" class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if(!empty($content['social_youtube']))
                            <a href="{{ $content['social_youtube'] }}" target="_blank" class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-6">Tautan Cepat</h4>
                    <ul class="space-y-3">
                        <li><a href="#journals" class="text-gray-400 hover:text-white transition-colors">Daftar Jurnal</a></li>
                        <li><a href="#subjects" class="text-gray-400 hover:text-white transition-colors">Bidang Ilmu</a></li>
                        <li><a href="{{ route('portal.search') }}" class="text-gray-400 hover:text-white transition-colors">Cari Artikel</a></li>
                        @guest
                            <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition-colors">Daftar Akun</a></li>
                            <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors">Masuk</a></li>
                        @endguest
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-semibold mb-6">Kontak</h4>
                    <ul class="space-y-4">
                        @if(!empty($content['footer_address']))
                            <li class="flex items-start">
                                <i class="fas fa-map-marker-alt text-primary-400 mt-1 mr-3"></i>
                                <span class="text-gray-400">{{ $content['footer_address'] }}</span>
                            </li>
                        @endif
                        @if(!empty($content['footer_email']))
                            <li class="flex items-center">
                                <i class="fas fa-envelope text-primary-400 mr-3"></i>
                                <a href="mailto:{{ $content['footer_email'] }}" class="text-gray-400 hover:text-white transition-colors">{{ $content['footer_email'] }}</a>
                            </li>
                        @endif
                        @if(!empty($content['footer_phone']))
                            <li class="flex items-center">
                                <i class="fas fa-phone text-primary-400 mr-3"></i>
                                <a href="tel:{{ $content['footer_phone'] }}" class="text-gray-400 hover:text-white transition-colors">{{ $content['footer_phone'] }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-gray-500 text-sm">
                        &copy; {{ date('Y') }} IAMJOS. All rights reserved.
                    </p>
                    {{-- <p class="text-gray-500 text-sm">
                        Powered by <span class="text-primary-400">Laravel</span> &amp; <span class="text-primary-400">Open Journal Systems</span>
                    </p> --}}
                </div>
            </div>
        </div>
    </footer>

</body>
</html>