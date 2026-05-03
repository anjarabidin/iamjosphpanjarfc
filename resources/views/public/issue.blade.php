@php $title = $issue->display_title; @endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">

    <!-- Issue Header -->
    <section class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Issue Cover -->
                <div class="lg:col-span-1">
                    <div
                        class="aspect-[3/4] bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center sticky top-24">
                        @if ($issue->cover_path)
                            <img src="{{ Storage::url($issue->cover_path) }}" alt="Issue Cover"
                                class="w-full h-full object-cover rounded-xl">
                        @else
                            <div class="text-center text-white p-6">
                                <p class="text-lg font-bold">Vol. {{ $issue->volume }}</p>
                                <p class="text-4xl font-bold">No. {{ $issue->number }}</p>
                                <p class="text-lg mt-2">{{ $issue->year }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Issue Info -->
                <div class="lg:col-span-3">
                    <nav class="text-sm text-gray-500 mb-4">
                        <a href="{{ route('journal.public.archives', ['journal' => $journal->slug]) }}"
                            class="hover:text-primary-600">Archives</a>
                        <span class="mx-2">/</span>
                        <span class="text-gray-900">{{ $issue->year }}</span>
                    </nav>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $issue->display_title }}</h1>

                    @if ($issue->title)
                        <p class="text-xl text-gray-600 mb-4">{{ $issue->title }}</p>
                    @endif

                    @if ($issue->description)
                        <div class="prose max-w-none text-gray-700 mb-6">
                            {!! clean($issue->description) !!}
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-8">
                        <span>Published: {{ $issue->published_at?->format('F j, Y') }}</span>
                        <span>•</span>
                        <span>{{ $articles->count() }} Articles</span>
                    </div>

                    <!-- Table of Contents -->
                    <div class="space-y-8">
                        @if (isset($articlesBySection) && $articlesBySection->isNotEmpty())
                            @foreach ($articlesBySection as $sectionName => $sectionArticles)
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                        {{ $sectionName }}
                                    </h2>
                                    <div class="space-y-8">
                                        @foreach ($sectionArticles as $article)
                                            @php
                                                $pub = $article->currentPublication; // Get versioned publication
                                                $doi = $pub->doi ?? $article->doi;
                                                $pages = $pub->pages ?? $article->pages;
                                            @endphp
                                            <div
                                                class="flex flex-col md:flex-row gap-4 border-b border-gray-100 pb-6 last:border-0">

                                                {{-- MAIN CONTENT (Left) --}}
                                                <div class="flex-1 min-w-0">

                                                    {{-- 1. TITLE --}}
                                                    <h4 class="text-lg font-bold text-blue-700 leading-tight mb-1">
                                                        <a href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}"
                                                            class="hover:underline">
                                                            {{ $article->title }}
                                                        </a>
                                                    </h4>

                                                    {{-- 2. SUBTITLE --}}
                                                    @if (!empty($article->subtitle))
                                                        <div class="text-sm text-gray-600 font-medium mb-2">
                                                            {{ $article->subtitle }}
                                                        </div>
                                                    @endif

                                                    {{-- 3. AUTHORS --}}
                                                    <div class="mt-2 mb-3 space-y-1">
                                                        @if ($article->authors && $article->authors->isNotEmpty())
                                                            @foreach ($article->authors as $author)
                                                                <div class="text-sm text-gray-700">
                                                                    <span class="font-bold">{{ $author->first_name }}
                                                                        {{ $author->last_name }}</span>
                                                                    @if ($author->affiliation)
                                                                        <span class="text-gray-500">,
                                                                            {{ $author->affiliation }}</span>
                                                                    @endif
                                                                    @if ($author->country)
                                                                        <span class="text-gray-500">,
                                                                            {{ $author->country }}</span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-sm text-gray-500 italic">No authors listed
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- 4. DOI --}}
                                                    @if ($doi)
                                                        <div class="flex items-center gap-2 mb-3 mt-3">
                                                            <span
                                                                class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 uppercase">
                                                                DOI
                                                            </span>
                                                            <a href="https://doi.org/{{ $doi }}"
                                                                class="text-sm text-blue-600 hover:underline underline-offset-2 break-all"
                                                                target="_blank">
                                                                https://doi.org/{{ $doi }}
                                                            </a>
                                                        </div>
                                                    @endif

                                                    {{-- 5. ACTION BAR --}}
                                                    <div class="flex flex-wrap items-center gap-4 mt-4">
                                                        {{-- Galley Buttons --}}
                                                        @if ($article->galleys && $article->galleys->isNotEmpty())
                                                            @foreach ($article->galleys as $galley)
                                                                <a href="{{ route('journal.article.download', ['journal' => $journal->slug, 'article' => $article->slug ?? $article->id, 'galley' => $galley->id]) }}"
                                                                    class="inline-flex items-center px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white text-xs font-bold rounded shadow-sm transition group">
                                                                    <svg class="w-4 h-4 mr-1.5 opacity-80 group-hover:opacity-100"
                                                                        fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                    {{ $galley->label ?? 'PDF' }}
                                                                </a>
                                                            @endforeach
                                                        @endif

                                                        {{-- Metrics --}}
                                                        <div
                                                            class="flex items-center gap-3 text-xs text-gray-500 border-l border-gray-200 pl-4">
                                                            <span class="flex items-center gap-1"
                                                                title="Abstract Views">
                                                                <svg class="w-4 h-4 text-gray-400" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                    </path>
                                                                </svg>
                                                                {{ $article->views_count ?? 0 }} Views
                                                            </span>
                                                            <span class="flex items-center gap-1"
                                                                title="File Downloads">
                                                                <svg class="w-4 h-4 text-gray-400" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                    </path>
                                                                </svg>
                                                                {{ $article->downloads_count ?? 0 }} Downloads
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- PAGE NUMBERS (Right Side) --}}
                                                @if ($pages)
                                                    <div class="text-sm text-gray-500 font-mono whitespace-nowrap pt-1">
                                                        {{ $pages }}
                                                    </div>
                                                @endif

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No articles in this issue</h3>
                                <p class="text-gray-500">Articles will be added soon.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
