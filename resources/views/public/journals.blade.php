<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Journals - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
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

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="text-sm font-medium text-gray-600 hover:text-primary-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-gray-600 hover:text-primary-600">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="bg-gradient-to-br from-primary-600 to-primary-800 text-white py-12">
        <div class="container mx-auto px-4 sm:px-6">
            <nav class="text-sm mb-4">
                <a href="{{ route('portal.home') }}" class="text-primary-200 hover:text-white">Home</a>
                <span class="mx-2 text-primary-300">/</span>
                <span>Journals</span>
            </nav>
            <h1 class="text-3xl font-bold">All Journals</h1>
            <p class="text-primary-200 mt-2">Browse our complete collection of academic journals</p>
        </div>
    </section>

    <!-- Search & Filters -->
    <section class="bg-white border-b py-4">
        <div class="container mx-auto px-4 sm:px-6">
            <form action="{{ route('portal.journals') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Search journals..."
                            class="w-full px-4 py-2.5 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <button type="submit"
                    class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    Search
                </button>
            </form>
        </div>
    </section>

    <!-- Journals Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4 sm:px-6">
            @if ($search)
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-gray-600">
                        Found {{ $journals->total() }} journal(s) for "<strong>{{ $search }}</strong>"
                    </p>
                    <a href="{{ route('portal.journals') }}" class="text-sm text-primary-600 hover:text-primary-700">
                        Clear search
                    </a>
                </div>
            @endif

            @if ($journals->isEmpty())
                <div class="text-center py-16">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No journals found</h3>
                    <p class="text-gray-500">Try adjusting your search criteria.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($journals as $journal)
                        <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}"
                            class="group bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-200 overflow-hidden transition-all duration-300">

                            <div
                                class="h-32 bg-gradient-to-br from-primary-500 to-primary-700 relative overflow-hidden p-6">
                                @if ($journal->logo_path)
                                    <img src="{{ Storage::url($journal->logo_path) }}" alt="{{ $journal->name }}"
                                        class="h-full w-auto object-contain bg-white rounded-lg p-2">
                                @else
                                    <span
                                        class="text-4xl font-bold text-white/90">{{ $journal->abbreviation ?? strtoupper(substr($journal->name, 0, 3)) }}</span>
                                @endif
                            </div>

                            <div class="p-6">
                                <h3
                                    class="text-lg font-semibold text-gray-900 group-hover:text-primary-600 transition-colors mb-2">
                                    {{ $journal->name }}
                                </h3>
                                @if ($journal->abbreviation)
                                    <p class="text-sm text-primary-600 font-medium mb-2">{{ $journal->abbreviation }}
                                    </p>
                                @endif
                                <p class="text-sm text-gray-500 line-clamp-3 mb-4">
                                    {{ $journal->description ?? 'Academic journal publishing quality peer-reviewed research.' }}
                                </p>

                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if ($journal->issn_print)
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">ISSN Print:
                                            {{ $journal->issn_print }}</span>
                                    @endif
                                    @if ($journal->issn_online)
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">ISSN Online:
                                            {{ $journal->issn_online }}</span>
                                    @endif
                                </div>

                                <div
                                    class="flex items-center justify-between pt-4 border-t border-gray-100 text-sm text-gray-500">
                                    <span>{{ $journal->submissions_count }} Articles</span>
                                    <span>{{ $journal->issues_count }} Issues</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $journals->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4 sm:px-6 text-center text-sm text-gray-500">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>
