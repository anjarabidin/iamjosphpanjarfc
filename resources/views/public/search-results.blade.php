<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search: {{ $query }} - IAMJOS</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="font-sans bg-gray-50 text-gray-900 antialiased">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('portal.home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg font-display">I</span>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-bold font-display text-gray-900">IAMJOS</h1>
                        <p class="text-xs text-gray-500 -mt-0.5">Portal Jurnal Akademik</p>
                    </div>
                </a>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-700">
                            <i class="fas fa-th-large mr-2"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-primary-600">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <!-- Search Header -->
    <section class="bg-gradient-to-br from-primary-600 via-primary-700 to-purple-800 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Pencarian</h1>
                <p class="text-primary-200">Temukan jurnal dan artikel ilmiah</p>
            </div>

            <form action="{{ route('portal.search') }}" method="GET" class="relative">
                <div class="flex items-center bg-white rounded-xl shadow-xl overflow-hidden">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="q" value="{{ $query }}"
                            placeholder="Cari jurnal, artikel, atau penulis..."
                            class="w-full pl-12 pr-4 py-4 text-gray-700 placeholder-gray-400 focus:outline-none">
                    </div>
                    <button type="submit" class="px-6 py-4 bg-primary-600 text-white font-medium hover:bg-primary-700">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Results -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(strlen($query) < 2)
                <div class="text-center py-16">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Masukkan minimal 2 karakter untuk memulai pencarian</p>
                </div>
            @elseif($journals->isEmpty() && $articles->isEmpty())
                <div class="text-center py-16">
                    <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Tidak ada hasil ditemukan untuk "<strong>{{ $query }}</strong>"</p>
                    <p class="text-gray-400 mt-2">Coba kata kunci lain atau periksa ejaan Anda</p>
                </div>
            @else
                <!-- Journals Results -->
                @if($journals->isNotEmpty())
                    <div class="mb-12">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book-open text-primary-600"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Jurnal</h2>
                                <p class="text-sm text-gray-500">{{ $journals->count() }} hasil ditemukan</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($journals as $journal)
                                <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}" 
                                   class="block bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow group">
                                    <div class="h-32 bg-gradient-to-br from-primary-500 to-purple-600 relative">
                                        @if($journal->cover_image)
                                            <img src="{{ asset('storage/' . $journal->cover_image) }}" alt="{{ $journal->name }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="text-4xl font-bold text-white/30">{{ substr($journal->abbreviation ?? $journal->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2">
                                            {{ $journal->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $journal->abbreviation }}</p>
                                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                                            <span><i class="fas fa-file-alt mr-1"></i> {{ $journal->submissions_count ?? 0 }} Artikel</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Articles Results -->
                @if($articles->isNotEmpty())
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-green-600"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Artikel</h2>
                                <p class="text-sm text-gray-500">{{ $articles->count() }} hasil ditemukan</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($articles as $article)
                                <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="px-2 py-1 text-xs font-medium bg-primary-100 text-primary-700 rounded">
                                                    {{ $article->journal->abbreviation ?? 'Journal' }}
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
                                                </span>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900 hover:text-primary-600 transition-colors mb-2">
                                                <a href="#">{{ $article->title }}</a>
                                            </h3>
                                            <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                                                {{ Str::limit(strip_tags($article->abstract), 250) }}
                                            </p>
                                            <div class="text-sm text-gray-500">
                                                <i class="fas fa-user mr-1"></i>
                                                {{ $article->submitter->name ?? 'Unknown Author' }}
                                            </div>
                                        </div>
                                        <a href="#" class="shrink-0 px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg">
                                            Baca <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-purple-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">I</span>
                    </div>
                    <span class="font-semibold">IAMJOS</span>
                </div>
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} IAMJOS. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
