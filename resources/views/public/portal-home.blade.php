<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Academic Journal Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('portal.home') }}" class="flex items-center space-x-2">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
                </a>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="text-sm font-medium text-gray-600 hover:text-primary-600">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-sm font-medium text-gray-600 hover:text-primary-600">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-gray-600 hover:text-primary-600">Login</a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Submit Manuscript
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section
        class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 text-white py-20 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 relative">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl font-extrabold mb-6">
                    Open Access Academic Journals
                </h1>
                <p class="text-xl text-primary-100 mb-8">
                    Discover peer-reviewed research across multiple disciplines. Browse our hosted journals and access
                    the latest academic publications.
                </p>

                <!-- Stats -->
                <div class="flex items-center justify-center gap-8 sm:gap-12">
                    <div class="text-center">
                        <p class="text-3xl sm:text-4xl font-bold">{{ $totalJournals }}</p>
                        <p class="text-sm text-primary-200">Journals</p>
                    </div>
                    <div class="w-px h-12 bg-primary-400/30"></div>
                    <div class="text-center">
                        <p class="text-3xl sm:text-4xl font-bold">{{ $totalArticles }}</p>
                        <p class="text-sm text-primary-200">Articles</p>
                    </div>
                    <div class="w-px h-12 bg-primary-400/30"></div>
                    <div class="text-center">
                        <p class="text-3xl sm:text-4xl font-bold">{{ $totalIssues }}</p>
                        <p class="text-sm text-primary-200">Issues</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Journals Section -->
    <section class="py-16">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Our Journals</h2>
                    <p class="text-gray-500 mt-1">Explore our collection of peer-reviewed academic journals</p>
                </div>
                <a href="{{ route('portal.journals') }}"
                    class="hidden sm:inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                    View All
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @if ($journals->isEmpty())
                <div class="text-center py-16 bg-gray-100 rounded-2xl">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Journals Available</h3>
                    <p class="text-gray-500">Check back soon for new publications.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($journals as $journal)
                        <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}"
                            class="group bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-200 overflow-hidden transition-all duration-300 hover:-translate-y-1">

                            <!-- Header with gradient -->
                            <div
                                class="h-24 bg-gradient-to-br from-primary-500 to-primary-700 relative overflow-hidden">
                                <div class="absolute inset-0 opacity-20">
                                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                        <defs>
                                            <pattern id="grid" width="10" height="10"
                                                patternUnits="userSpaceOnUse">
                                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white"
                                                    stroke-width="0.5" />
                                            </pattern>
                                        </defs>
                                        <rect width="100" height="100" fill="url(#grid)" />
                                    </svg>
                                </div>
                                <div class="absolute bottom-4 left-4">
                                    <span
                                        class="text-3xl font-bold text-white/90">{{ $journal->abbreviation ?? strtoupper(substr($journal->name, 0, 3)) }}</span>
                                </div>
                            </div>

                            <div class="p-5">
                                <h3
                                    class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2 mb-2">
                                    {{ $journal->name }}
                                </h3>
                                <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                                    {{ $journal->description ?? 'Academic journal publishing quality research.' }}
                                </p>
                                <div class="flex items-center gap-4 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $journal->submissions_count }} Articles
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                        {{ $journal->issues_count }} Issues
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Latest Articles Section -->
    @if ($latestArticles->isNotEmpty())
        <section class="py-16 bg-gray-100">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="text-center mb-10">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Latest Publications</h2>
                    <p class="text-gray-500 mt-1">Recent articles from across all journals</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($latestArticles as $article)
                        <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span
                                    class="px-2 py-1 text-xs font-medium bg-primary-100 text-primary-700 rounded-full">
                                    {{ $article->journal?->abbreviation ?? 'Journal' }}
                                </span>
                                @if ($article->section)
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                        {{ $article->section->name }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-gray-900 line-clamp-2 mb-2">
                                <a href="{{ route('journal.public.article', ['journal' => $article->journal?->slug ?? 'default', 'article' => $article->seq_id]) }}"
                                    class="hover:text-primary-600 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 mb-3">
                                {{ $article->authors->pluck('name')->join(', ') }}
                            </p>
                            <p class="text-xs text-gray-400">
                                Published {{ $article->published_at?->format('M d, Y') }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="font-bold">{{ config('app.name') }}</span>
                </div>
                <div class="flex items-center gap-6 text-sm text-gray-400">
                    <a href="{{ route('portal.journals') }}" class="hover:text-white transition-colors">All
                        Journals</a>
                    <a href="{{ route('login') }}" class="hover:text-white transition-colors">Login</a>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-500">
                <p>© {{ date('Y') }} {{ config('app.name') }}. Open Journal System Clone built with ❤️</p>
            </div>
        </div>
    </footer>
</body>

</html>
