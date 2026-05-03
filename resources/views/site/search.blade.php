@extends('layouts.portal')

@section('title', 'Global Search - IAMJOS')

@php
    $brandColor = $settings['primary_color'] ?? '#00629B';
@endphp

@section('content')
    <div x-data="{
        mobileFilters: false,
        activeTab: '{{ $category === 'journals' ? 'journals' : ($category === 'articles' ? 'articles' : 'all') }}'
    }" class="min-h-screen bg-slate-50 pb-20">

        {{-- PAGE HEADER: MINIMAL & CLEAN --}}
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Search Results</h1>
                        <p class="mt-1 text-slate-500 text-sm">
                            @if ($query)
                                Found results for "<span class="text-indigo-600 font-bold">{{ $query }}</span>"
                            @else
                                Explore our database of journals and articles
                            @endif
                        </p>
                    </div>
                    
                    {{-- Quick Action: Search Bar in Header --}}
                    <div class="w-full md:w-96">
                        <form action="{{ route('portal.search') }}" method="GET" class="relative">
                            <input type="text" name="q" value="{{ $query }}"                                placeholder="Refine search..."
                                class="w-full pl-10 pr-4 py-2 bg-slate-100 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                            <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col lg:flex-row gap-8">
                
                {{-- SIDEBAR FILTERS --}}
                <aside class="w-full lg:w-72 flex-shrink-0">
                    {{-- Mobile Toggle --}}
                    <button @click="mobileFilters = !mobileFilters"                        class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-white border border-slate-200 rounded-xl mb-4 text-sm font-bold text-slate-700 shadow-sm">
                        <span>Filters & Analytics</span>
                        <i class="fa-solid" :class="mobileFilters ? 'fa-chevron-up' : 'fa-sliders'"></i>
                    </button>

                    <div :class="mobileFilters ? 'block' : 'hidden lg:block'" class="space-y-6 animate-in fade-in slide-in-from-top-4 duration-300">
                        
                        <form action="{{ route('portal.search') }}" method="GET" id="filterForm">
                            <input type="hidden" name="q" value="{{ $query }}">

                            {{-- Filter Group: Category --}}
                            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">Content Type</h3>
                                <div class="space-y-2">
                                    @foreach(['all' => 'All results', 'journals' => 'Journals', 'articles' => 'Articles'] as $val => $label)
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="radio" name="category" value="{{ $val }}" {{ $category == $val ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                            class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                        <span class="ml-3 text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Filter Group: Journal Facet --}}
                            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm mt-6">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">Source Journal</h3>
                                <div class="space-y-1 max-h-60 overflow-y-auto custom-scrollbar">
                                    <label class="flex items-center p-2 rounded-lg hover:bg-slate-50 cursor-pointer group">
                                        <input type="radio" name="journal_id" value="" {{ empty($journalId) ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                            class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                        <span class="ml-3 text-sm font-medium text-slate-600">All Journals</span>
                                    </label>
                                    @foreach($filterJournals as $fj)
                                    <label class="flex items-center p-2 rounded-lg hover:bg-slate-50 cursor-pointer group">
                                        <input type="radio" name="journal_id" value="{{ $fj->id }}" {{ $journalId == $fj->id ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                            class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                        <div class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                                {{ $fj->name }}
                                            </div>
                                            <div class="text-[10px] text-slate-400">{{ $fj->published_count }} Articles</div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Filter Group: Sort --}}
                            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm mt-6">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">Sort By</h3>
                                <select name="sort" onchange="this.form.submit()"
                                    class="w-full bg-slate-50 border-transparent rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                    <option value="relevance" {{ $sort == 'relevance' ? 'selected' : '' }}>Relevance</option>
                                    <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest Published</option>
                                    <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest Published</option>
                                </select>
                            </div>
                        </form>

                        {{-- STATS / POPULAR (Static decoration) --}}
                        <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
                            <div class="relative z-10">
                                <h3 class="text-xs font-bold uppercase tracking-wider opacity-80 mb-2">Popular Tags</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($popularKeywords as $kw)
                                        <a href="{{ route('portal.search', ['q' => $kw->content]) }}" 
                                            class="text-[11px] bg-white/20 hover:bg-white/40 px-2 py-1 rounded-lg backdrop-blur-sm transition-colors">
                                            #{{ strtolower($kw->content) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <i class="fa-solid fa-bolt absolute bottom-[-10px] right-[-10px] text-6xl opacity-10"></i>
                        </div>
                    </div>
                </aside>

                {{-- MAIN CONTENT AREA --}}
                <main class="flex-1">
                    
                    {{-- Tab Switcher for Mobile/Quick Toggle --}}
                    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2 no-scrollbar">
                        @foreach(['all' => 'Top Results', 'journals' => 'Journals', 'articles' => 'Articles'] as $id => $name)
                            <button @click="activeTab = '{{ $id }}'"
                                :class="activeTab === '{{ $id }}' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-slate-500 border border-slate-200 hover:border-indigo-300'"
                                class="px-5 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300">
                                {{ $name }}
                            </button>
                        @endforeach
                    </div>

                    <div class="space-y-8">
                        
                        {{-- JOURNALS SECTION --}}
                        <div x-show="activeTab === 'all' || activeTab === 'journals'" x-transition:enter="duration-300 ease-out">
                            @if($journals->isNotEmpty())
                                <div class="mb-10">
                                    <div class="flex items-center gap-4 mb-4">
                                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-400">Scholarly Journals</h2>
                                        <div class="h-px bg-slate-200 flex-1"></div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($journals as $journal)
                                            <div class="group bg-white border border-slate-200 rounded-2xl p-5 hover:border-indigo-500 hover:shadow-xl transition-all duration-300 relative overflow-hidden">
                                                <div class="flex items-start gap-4">
                                                    <div class="w-14 h-14 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-600 group-hover:border-indigo-600 transition-colors duration-300">
                                                        <i class="fa-solid fa-book-open text-indigo-600 group-hover:text-white transition-colors duration-300"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h3 class="font-bold text-slate-900 truncate">
                                                            <a href="{{ route('journal.public.home', $journal->slug) }}" class="hover:text-indigo-600 text-lg">
                                                                {{ $journal->name }}
                                                            </a>
                                                        </h3>
                                                        <div class="flex flex-wrap items-center gap-3 mt-1 text-[11px] font-bold text-slate-400 uppercase tracking-tighter">
                                                            <span class="flex items-center gap-1"><i class="fa-solid fa-fingerprint text-[10px]"></i> {{ $journal->abbreviation }}</span>
                                                            <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                                            <span class="flex items-center gap-1"><i class="fa-solid fa-file-invoice text-[10px]"></i> {{ $journal->submissions_count }} Articles</span>
                                                        </div>
                                                        <p class="mt-3 text-sm text-slate-500 line-clamp-2 leading-relaxed">
                                                            {{ strip_tags($journal->description) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- ARTICLES SECTION --}}
                        <div x-show="activeTab === 'all' || activeTab === 'articles'" x-transition:enter="duration-300 ease-out">
                            @if($articles->isNotEmpty())
                                <div>
                                    <div class="flex items-center gap-4 mb-4">
                                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-400">Research Documents</h2>
                                        <div class="h-px bg-slate-200 flex-1"></div>
                                    </div>
                                    <div class="space-y-4">
                                        @foreach($articles as $article)
                                            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 hover:border-indigo-500 hover:shadow-2xl transition-all duration-500 group">
                                                <div class="flex flex-col md:flex-row gap-8">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-3 mb-4">
                                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-100 italic">Peer Reviewed</span>
                                                            <a href="{{ route('journal.public.home', $article->journal->slug) }}" class="text-[11px] font-bold text-indigo-500 hover:underline uppercase tracking-tighter">
                                                                {{ $article->journal->name }}
                                                            </a>
                                                        </div>

                                                        <h3 class="text-xl md:text-2xl font-extrabold text-slate-900 leading-tight">
                                                            <a href="{{ route('journal.public.article', [$article->journal->slug, $article->seq_id]) }}"
                                                                class="hover:text-indigo-600 transition-colors">
                                                                {{ $article->title }}
                                                            </a>
                                                        </h3>

                                                        <div class="flex flex-wrap items-center gap-2 mt-3 text-sm text-slate-500">
                                                            <span class="font-medium">by</span>
                                                            <span class="text-indigo-600 font-bold italic">{{ $article->authors->pluck('display_name')->join(', ') }}</span>
                                                        </div>

                                                        @if ($article->abstract)
                                                            <div class="mt-6 text-sm text-slate-600 leading-relaxed line-clamp-3 md:line-clamp-4 font-light italic">
                                                                {{ Str::limit(strip_tags($article->abstract), 400) }}
                                                            </div>
                                                        @endif

                                                        <div class="flex flex-wrap items-center gap-6 mt-8 pt-6 border-t border-slate-50">
                                                            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                                                <i class="fa-regular fa-calendar-check text-indigo-500"></i>
                                                                <span>{{ $article->published_at ? $article->published_at->format('M d, Y') : 'N/A' }}</span>
                                                            </div>
                                                            @if ($article->section)
                                                                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                                                    <i class="fa-regular fa-folder-open text-indigo-500"></i>
                                                                    <span>{{ $article->section->title }}</span>
                                                                </div>
                                                            @endif
                                                            
                                                            {{-- Metrics --}}
                                                            <div class="flex items-center gap-6 ml-auto">
                                                                <div class="flex flex-col items-center">
                                                                    <span class="text-lg font-black text-slate-800">{{ number_format($article->views_count) }}</span>
                                                                    <span class="text-[9px] uppercase font-black text-slate-400 tracking-widest">Views</span>
                                                                </div>
                                                                <div class="flex flex-col items-center">
                                                                    <span class="text-lg font-black text-slate-800">{{ number_format($article->downloads_count) }}</span>
                                                                    <span class="text-[9px] uppercase font-black text-slate-400 tracking-widest">Saved</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($articles->hasPages())
                                        <div class="mt-12">
                                            {{ $articles->links() }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- EMPTY STATE --}}
                        @if($journals->isEmpty() && $articles->isEmpty())
                            <div class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                                <div class="w-24 h-24 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fa-solid fa-magnifying-glass-chart text-4xl"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-900 mb-2">No Matching Data Found</h3>
                                <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">
                                    We couldn't find any journals or articles matching your current search parameters.
                                </p>
                                <a href="{{ route('portal.search') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                    Clear all filters
                                </a>
                            </div>
                        @endif

                    </div>
                </main>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
@endsection
