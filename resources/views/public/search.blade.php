@php
    $brandColor = $settings['primary_color'] ?? '#4F46E5';
@endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">
    @push('styles')
    <style>
        :root {
            --brand-color: {{ $brandColor }};
            --brand-color-soft: {{ $brandColor }}15; /* 15% opacity */
            --brand-color-muted: {{ $brandColor }}40; /* 40% opacity */
        }
        .text-brand { color: var(--brand-color); }
        .bg-brand { background-color: var(--brand-color); }
        .bg-brand-soft { background-color: var(--brand-color-soft); }
        .border-brand { border-color: var(--brand-color); }
        .focus-within\:border-brand:focus-within { border-color: var(--brand-color); }
        .group-focus-within\:text-brand:focus-within { color: var(--brand-color); }
    </style>
    @endpush

    {{-- = = = = = = = = = = = = = = = = = = = = = = = --}}
    {{-- MINIMALIST HERO SECTION --}}
    {{-- = = = = = = = = = = = = = = = = = = = = = = = --}}
    <section class="relative bg-slate-50 border-b border-slate-200/60 py-12 px-4">
        {{-- Subtle Gradient Accent --}}
        <div class="absolute bottom-0 left-0 w-full h-px" style="background: linear-gradient(to right, transparent, var(--brand-color-muted), transparent);"></div>

        <div class="max-w-4xl mx-auto" x-data="{
            q: '{{ $query ?? '' }}',
            type: '{{ $type ?? 'all' }}',
            year: '{{ $year ?? '' }}'
        }">
            <div class="text-center mb-8">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-brand-soft rounded-full text-[10px] font-bold text-brand uppercase tracking-widest mb-4">
                    <i class="fa-solid fa-earth-asia"></i>
                    Global Discovery
                </span>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-3">Search Articles</h1>
                <p class="text-slate-500 max-w-lg mx-auto text-base">
                    Explore high-quality research and scholarly works across our specialized collections.
                </p>
            </div>

            {{-- Floating Glass Search Bar --}}
            <form action="{{ route('journal.public.search', ['journal' => $journal->slug]) }}" method="GET" class="space-y-6">
                <div class="relative group max-w-3xl mx-auto">
                    {{-- Input Container with Focus Effects --}}
                    <div class="relative flex items-center bg-white border border-slate-200 rounded-2xl shadow-sm transition-all duration-300 group-focus-within:shadow-xl group-focus-within:border-brand/50 group-focus-within:-translate-y-0.5 overflow-hidden">
                        <div class="pl-5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-slate-300 text-lg group-focus-within:text-brand transition-colors"></i>
                        </div>
                        <input type="text" 
                               name="q" 
                               x-model="q"
                               placeholder="Search title, keywords, or authors..."
                               class="w-full pl-4 pr-32 py-4.5 text-base border-0 focus:ring-0 placeholder-slate-400 text-slate-700 font-medium"
                               autofocus>
                        <div class="pr-2">
                            <button type="submit"
                                    class="px-6 py-2.5 bg-brand hover:brightness-110 text-white text-xs font-bold rounded-xl transition-all uppercase tracking-widest shadow-lg shadow-brand/20">
                                Search
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Segmented Control Filters --}}
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <div class="relative inline-flex bg-slate-100 rounded-xl p-1 shadow-inner border border-slate-200/50" x-data="{
                        activeTab: '{{ $type ?? 'all' }}'
                    }">
                        {{-- Sliding Active Indicator --}}
                        <div class="absolute inset-y-1 bg-white rounded-lg shadow-sm transition-all duration-300 pointer-events-none"
                             :style="{
                                width: '{{ 100 / 4 }}%',
                                transform: activeTab === 'all' ? 'translateX(0)' : 
                                           activeTab === 'title' ? 'translateX(100%)' :
                                           activeTab === 'author' ? 'translateX(200%)' : 'translateX(300%)'
                             }">
                        </div>

                        @foreach([
                            ['val' => 'all', 'label' => 'All'],
                            ['val' => 'title', 'label' => 'Title'],
                            ['val' => 'author', 'label' => 'Author'],
                            ['val' => 'keywords', 'label' => 'Keywords']
                        ] as $option)
                            <button type="button" 
                                    @click="activeTab = '{{ $option['val'] }}'; type = '{{ $option['val'] }}'"
                                    :style="activeTab === '{{ $option['val'] }}' ? 'color: var(--brand-color)' : ''"
                                    :class="activeTab === '{{ $option['val'] }}' ? '' : 'text-slate-500 hover:text-slate-700'"
                                    class="relative px-6 py-1.5 rounded-lg text-xs font-bold uppercase tracking-tight transition-colors z-10 w-[80px] md:w-[100px]">
                                {{ $option['label'] }}
                            </button>
                        @endforeach
                        <input type="hidden" name="type" x-model="type">
                    </div>

                    {{-- Minimalist Year Selector --}}
                    @if (isset($years) && $years->count() > 0)
                        <div class="relative">
                            <select name="year" 
                                    x-model="year"
                                    class="appearance-none bg-white border border-slate-200 rounded-xl py-2 pl-4 pr-10 text-xs font-bold text-slate-600 focus:ring-2 focus:ring-brand/10 focus:border-brand/40 transition-all cursor-pointer shadow-sm">
                                <option value="">Any Year</option>
                                @foreach ($years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- = = = = = = = = = = = = = = = = = = = = = = = --}}
    {{-- MINIMALIST RESULTS SECTION --}}
    {{-- = = = = = = = = = = = = = = = = = = = = = = = --}}
    <section class="py-12 bg-white min-h-[60vh]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($query)
                {{-- Airy Status Bar --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 border-b border-slate-100 pb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            @if ($totalFound > 0)
                                <span class="text-brand">{{ number_format($totalFound) }}</span> Results
                            @else
                                No articles matched
                            @endif
                        </h2>
                        <p class="text-slate-400 text-sm mt-1">
                            Discovery for: <span class="text-slate-600 font-medium italic">"{{ $query }}"</span>
                        </p>
                    </div>

                    @if ($totalFound > 0)
                        <a href="{{ route('journal.public.search', ['journal' => $journal->slug]) }}"
                           class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-red-500 transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-xmark"></i>
                            Clear
                        </a>
                    @endif
                </div>

                @if ($totalFound > 0 && $results->count() > 0)
                    {{-- Minimalist Cards --}}
                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($results as $article)
                            <article class="group bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex flex-col gap-4">
                                    {{-- Status & Category Row --}}
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($article->section)
                                            <span class="px-2 py-0.5 bg-brand-soft text-brand rounded text-[9px] font-bold uppercase tracking-wider">
                                                {{ $article->section->name }}
                                            </span>
                                        @endif
                                        @if ($article->issue)
                                            <span class="px-2 py-0.5 bg-slate-50 text-slate-500 rounded text-[9px] font-bold uppercase tracking-wider">
                                                {{ $article->issue->identifier }}
                                            </span>
                                        @endif
                                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                        <time class="text-[10px] text-slate-400 font-medium">
                                            {{ $article->published_at?->format('F d, Y') ?? 'Unpublished' }}
                                        </time>
                                    </div>

                                    {{-- Title & Authors --}}
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-brand transition-colors leading-tight mb-2">
                                            <a href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}">
                                                {{ $article->title }}
                                            </a>
                                        </h3>
                                        @if ($article->authors->count() > 0)
                                            <p class="text-xs font-semibold text-slate-500">
                                                <i class="fa-solid fa-user-pen mr-1 opacity-40"></i>
                                                {{ $article->authors->map(fn($a) => $a->first_name . ' ' . $a->last_name)->join(', ') }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Snippet --}}
                                    @if ($article->abstract)
                                        <p class="text-slate-500 text-xs leading-relaxed line-clamp-2 opacity-80">
                                            {{ Str::limit(strip_tags($article->abstract), 200) }}
                                        </p>
                                    @endif

                                    {{-- Minimalist Action Bar --}}
                                    <div class="flex flex-wrap items-center gap-5 pt-4 mt-2 border-t border-slate-50">
                                        @if ($article->files->where('file_type', 'galley')->count() > 0)
                                            <a href="{{ route('journal.public.article.reader', ['journal' => $journal->slug, 'article' => $article->slug ?? $article->id]) }}"
                                               class="flex items-center gap-1.5 text-[10px] font-bold text-brand uppercase tracking-widest hover:underline">
                                                <i class="fa-solid fa-file-pdf opacity-70"></i>
                                                PDF Full-Text
                                            </a>
                                        @endif
                                        <a href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}"
                                           class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                                            Abstract Details
                                        </a>
                                        @php $doi = $article->currentPublication->doi ?? $article->doi; @endphp
                                        @if ($doi)
                                            <span class="text-[10px] font-bold text-slate-200 uppercase tracking-tighter">
                                                DOI: {{ $doi }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($results->hasPages())
                        <div class="mt-12">
                            {{ $results->links() }}
                        </div>
                    @endif
                @else
                    {{-- Airy Empty State --}}
                    <div class="py-20 flex flex-col items-center text-center">
                        <div class="mb-8 relative">
                            <div class="absolute inset-0 bg-brand-soft rounded-full scale-150 blur-2xl opacity-50"></div>
                            <i class="fa-solid fa-cloud-moon text-7xl text-slate-100 relative"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Finding your way?</h3>
                        <p class="text-slate-400 max-w-sm mx-auto mb-8 text-sm leading-relaxed">
                            No articles matched <span class="text-brand font-bold">"{{ $query }}"</span>. Take a step back or browse recent additions.
                        </p>
                        <div class="flex gap-3">
                            <a href="{{ route('journal.public.search', ['journal' => $journal->slug]) }}"
                               class="px-8 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all text-[10px] uppercase tracking-widest">
                                Global Search
                            </a>
                        </div>
                    </div>
                @endif
            @else
                {{-- Clean Welcome Grid --}}
                <div class="max-w-4xl mx-auto py-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('journal.public.current', ['journal' => $journal->slug]) }}"
                           class="block p-8 bg-white border border-slate-100 rounded-3xl shadow-sm transition-all hover:shadow-md hover:-translate-y-1 group">
                            <div class="w-12 h-12 rounded-2xl bg-brand-soft flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-bolt text-brand text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-slate-900 mb-2">Current Issue</h4>
                            <p class="text-slate-500 text-sm leading-relaxed mb-4">Discover the latest research and scholarly publications in this edition.</p>
                            <span class="inline-flex items-center gap-2 text-[11px] font-bold text-brand uppercase tracking-widest">
                                EXPLORE LATEST <i class="fa-solid fa-arrow-right-long"></i>
                            </span>
                        </a>

                        <a href="{{ route('journal.public.archives', ['journal' => $journal->slug]) }}"
                           class="block p-8 bg-brand rounded-3xl shadow-xl transition-all hover:-translate-y-1 group text-white">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-box-archive text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold mb-2">Historical Archives</h4>
                            <p class="text-white/70 text-sm leading-relaxed mb-4">Access our extensive library of past issues and historical records.</p>
                            <span class="inline-flex items-center gap-2 text-[11px] font-bold text-white/90 uppercase tracking-widest">
                                BROWSE LIBRARY <i class="fa-solid fa-arrow-right-long"></i>
                            </span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
