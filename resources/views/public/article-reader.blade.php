<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $submission->title }} - PDF Reader</title>
    <meta name="description" content="{{ Str::limit(strip_tags($submission->abstract), 160) }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            "50": "#f0f9ff",
                            "100": "#e0f2fe",
                            "200": "#bae6fd",
                            "300": "#7dd3fc",
                            "400": "#38bdf8",
                            "500": "#0ea5e9",
                            "600": "#0284c7",
                            "700": "#0369a1",
                            "800": "#075985",
                            "900": "#0c4a6e",
                            "950": "#082f49"
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        serif: ['Merriweather', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        /* PDF viewer container */
        .pdf-viewer {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-full font-sans bg-gray-900 text-gray-100 antialiased overflow-hidden" x-data="{ sidebarOpen: true }">
    <div class="h-screen flex">
        <!-- Sidebar (Left 25%) -->
        <aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="w-full md:w-96 lg:w-[400px] xl:w-[450px] bg-white text-gray-900 flex flex-col h-full shadow-2xl z-20">
            <!-- Sidebar Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-white">Article Details</span>
                </div>
                <button @click="sidebarOpen = false"
                    class="p-2 hover:bg-white/10 rounded-lg transition-colors text-white md:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Content -->
            <div class="flex-1 overflow-y-auto sidebar-scroll p-6 space-y-6">
                <!-- Title -->
                <div>
                    <h1 class="text-xl font-serif font-bold text-gray-900 leading-tight">
                        {{ $submission->title }}
                    </h1>
                </div>

                <!-- Authors -->
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                        <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Authors
                    </h3>
                    <div class="space-y-2">
                        @foreach ($submission->authors as $index => $author)
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-medium">
                                    {{ substr($author->first_name, 0, 1) }}{{ substr($author->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $author->first_name }} {{ $author->last_name }}
                                        @if ($author->is_corresponding)
                                            <span class="text-xs text-primary-600">(Corresponding)</span>
                                        @endif
                                    </p>
                                    @if ($author->affiliation)
                                        <p class="text-xs text-gray-500">{{ $author->affiliation }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Abstract -->
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                        <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        Abstract
                    </h3>
                    <div class="text-sm text-gray-700 leading-relaxed prose prose-sm max-w-none"
                        x-data="{ expanded: false }">
                        <div x-show="!expanded">
                            {!! Str::limit(strip_tags($submission->abstract), 300) !!}
                            @if (strlen(strip_tags($submission->abstract)) > 300)
                                <button @click="expanded = true"
                                    class="text-primary-600 hover:text-primary-700 font-medium ml-1">
                                    Read more
                                </button>
                            @endif
                        </div>
                        <div x-show="expanded" x-cloak>
                            {!! nl2br(e(strip_tags($submission->abstract))) !!}
                            <button @click="expanded = false"
                                class="text-primary-600 hover:text-primary-700 font-medium ml-1">
                                Show less
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Keywords -->
                @if ($submission->keywords)
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Keywords
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach (explode(',', $submission->keywords) as $keyword)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ trim($keyword) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Meta Info -->
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    @if ($submission->issue)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Published In</span>
                            <span class="text-sm font-medium text-gray-900">{{ $submission->issue->identifier }}</span>
                        </div>
                    @endif

                    @if ($submission->published_at)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Published Date</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ $submission->published_at->format('M d, Y') }}</span>
                        </div>
                    @endif

                    @if ($submission->section)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Section</span>
                            <span class="text-sm font-medium text-gray-900">{{ $submission->section->name }}</span>
                        </div>
                    @endif

                    @if ($submission->doi)
                        <div class="pt-2 border-t border-gray-200">
                            <span class="text-xs text-gray-500 block mb-1">DOI</span>
                            <a href="https://doi.org/{{ $submission->doi }}" target="_blank"
                                class="text-sm text-primary-600 hover:text-primary-700 font-medium break-all">
                                {{ $submission->doi }}
                            </a>
                        </div>
                    @endif
                </div>

                @if ($galleyFile)
                    <a href="{{ route('journal.article.download.pdf', ['journal' => $journal->slug, 'seq_id' => $submission->seq_id, 'filename' => Str::slug($submission->title)]) }}"
                        class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </a>
                @endif
            </div>

            <!-- Sidebar Footer -->
            <div class="border-t border-gray-200 p-4">
                <a href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $submission->seq_id]) }}"
                    class="flex items-center justify-center w-full px-4 py-2.5 text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Article Page
                </a>
            </div>
        </aside>

        <!-- Main PDF Viewer Area (75%) -->
        <main class="flex-1 flex flex-col h-full pdf-viewer relative">
            <!-- PDF Toolbar -->
            <div
                class="bg-gray-800/90 backdrop-blur-sm px-4 py-3 flex items-center justify-between border-b border-gray-700">
                <!-- Left side -->
                <div class="flex items-center space-x-4">
                    <!-- Toggle Sidebar -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 hover:bg-gray-700 rounded-lg transition-colors"
                        :class="{ 'bg-gray-700': !sidebarOpen }">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- File name -->
                    <div class="hidden sm:flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zm-3 9.5A1.5 1.5 0 018.5 12H7v6h1.5v-2.5h.5a1.5 1.5 0 001.5-1.5v-1zm-.5 1a.5.5 0 01-.5.5H8.5v-1h.5a.5.5 0 01.5.5zm3.5-.5H12v6h1.5v-2h.5a1.5 1.5 0 001.5-1.5v-1a1.5 1.5 0 00-1.5-1.5zm0 2.5h-.5v-1.5h.5a.5.5 0 01.5.5v.5a.5.5 0 01-.5.5zm4-2.5v1h-1v1h1v1h-1v2h-1.5v-6h3v1h-1.5z" />
                        </svg>
                        @if ($galleyFile)
                            <span class="text-sm text-gray-400 truncate max-w-xs">{{ $galleyFile->file_name }}</span>
                        @else
                            <span class="text-sm text-gray-400">No PDF Available</span>
                        @endif
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-2">
                    @if ($galleyFile)
                        <!-- Open in New Tab -->
                        <a href="{{ Storage::url($galleyFile->file_path) }}" target="_blank"
                            class="p-2 hover:bg-gray-700 rounded-lg transition-colors text-gray-300 hover:text-white"
                            title="Open in New Tab">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>

                        <!-- Download -->
                        <a href="{{ route('journal.article.download.pdf', ['journal' => $journal->slug, 'seq_id' => $submission->seq_id, 'filename' => Str::slug($submission->title)]) }}"
                            class="p-2 hover:bg-gray-700 rounded-lg transition-colors text-gray-300 hover:text-white"
                            title="Download">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    @endif

                    <!-- Close / Back to Journal -->
                    <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}"
                        class="p-2 hover:bg-gray-700 rounded-lg transition-colors text-gray-300 hover:text-white"
                        title="Back to Journal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- PDF Content -->
            <div class="flex-1 relative">
                @if ($galleyFile)
                    @php
                        $fileUrl = Storage::url($galleyFile->file_path);
                        $isPdf = Str::endsWith(strtolower($galleyFile->file_name), '.pdf');
                    @endphp

                    @if ($isPdf)
                        <!-- PDF Viewer using Object/Iframe -->
                        <object data="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
                            type="application/pdf" class="w-full h-full">
                            <!-- Fallback for browsers that don't support object -->
                            <iframe src="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
                                class="w-full h-full border-0" title="{{ $submission->title }}">
                                <p class="text-center text-gray-400 p-8">
                                    Your browser doesn't support PDF viewing.
                                    <a href="{{ route('journal.article.download.pdf', ['journal' => $journal->slug, 'seq_id' => $submission->seq_id, 'filename' => Str::slug($submission->title)]) }}"
                                        class="text-primary-400 hover:text-primary-300 underline">
                                        Download the PDF
                                    </a> instead.
                                </p>
                            </iframe>
                        </object>
                    @else
                        <!-- Non-PDF file -->
                        <div class="h-full flex items-center justify-center">
                            <div class="text-center p-8">
                                <div
                                    class="w-20 h-20 mx-auto mb-4 bg-gray-700 rounded-2xl flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-200 mb-2">{{ $galleyFile->file_name }}</h3>
                                <p class="text-gray-400 mb-6">This file type cannot be previewed in the browser.</p>
                                <a href="{{ route('journal.article.download', ['journal' => $journal->slug, 'article' => $submission->seq_id, 'galley' => $galleyFile->id]) }}"
                                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download File
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- No Galley File Available -->
                    <div class="h-full flex items-center justify-center">
                        <div class="text-center p-8 max-w-md">
                            <div
                                class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-700 to-gray-800 rounded-2xl flex items-center justify-center shadow-xl">
                                <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-200 mb-3">PDF Not Available</h3>
                            <p class="text-gray-400 mb-6 leading-relaxed">
                                The full-text PDF for this article is not yet available.
                                Please check back later or contact the journal editors for more information.
                            </p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $submission->seq_id]) }}"
                                    class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    View Article Info
                                </a>
                                <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}"
                                    class="inline-flex items-center px-5 py-2.5 text-gray-300 hover:text-white font-medium rounded-lg border border-gray-600 hover:border-gray-500 transition-colors">
                                    Back to Journal
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>

</html>
