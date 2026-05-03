@extends('layouts.portal')

@section('title', 'Browse Journals - IAMJOS')

@php
    $brandColor = $settings['primary_color'] ?? '#00629B';
@endphp

@section('content')
<div x-data="{ mobileFiltersOpen: false }" class="min-h-screen bg-slate-50 pb-20">

    {{-- ============================================ --}}
    {{-- PAGE HEADER: MINIMAL & MODERN --}}
    {{-- ============================================ --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="max-w-2xl">
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-3">Browse Journals</h1>
                    <p class="text-slate-500 text-base md:text-lg leading-relaxed">
                        Explore our comprehensive collection of high-quality, peer-reviewed academic journals across diverse fields of research and scholarly inquiry.
                    </p>
                </div>
                
                {{-- Quick Sort / Mobile Filter Toggle --}}
                <div class="flex items-center gap-3">
                    <button @click="mobileFiltersOpen = true" 
                            type="button"
                            class="lg:hidden inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        <i class="fa-solid fa-sliders text-indigo-600"></i>
                        Filters
                    </button>
                    
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Sort By:</span>
                        <select onchange="window.location.href=this.value" 
                                class="bg-transparent border-none text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer p-0 pr-8">
                            <option value="{{ route('portal.journals', array_merge(request()->query(), ['sort' => 'name'])) }}" 
                                    {{ $sort == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="{{ route('portal.journals', array_merge(request()->query(), ['sort' => 'newest'])) }}" 
                                    {{ $sort == 'newest' ? 'selected' : '' }}>Recently Added</option>
                            <option value="{{ route('portal.journals', array_merge(request()->query(), ['sort' => 'articles'])) }}" 
                                    {{ $sort == 'articles' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- = = = = = = = = = = = = = = = = = = = = = = = --}}
    {{-- MAIN GRID CONTAINER --}}
    {{-- = = = = = = = = = = = = = = = = = = = = = = = --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- SIDEBAR: DESKTOP --}}
            <aside class="hidden lg:block lg:col-span-3">
                <div class="sticky top-24 space-y-6">
                    <x-site.journal-sidebar 
                        :search="$search" 
                        :sort="$sort" 
                        :alpha="$alpha" 
                        :alphabet="$alphabet" 
                        :brand-color="$brandColor" 
                    />
                </div>
            </aside>

            {{-- MOBILE DRAWER: FILTERS --}}
            <template x-teleport="body">
                <div x-show="mobileFiltersOpen" 
                     x-cloak
                     class="fixed inset-0 z-[100] lg:hidden">
                    
                    {{-- Backdrop --}}
                    <div x-show="mobileFiltersOpen"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         @click="mobileFiltersOpen = false" 
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm">
                    </div>

                    {{-- Drawer Panel --}}
                    <div x-show="mobileFiltersOpen"
                         x-transition:enter="transform transition ease-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in duration-200"
                         class="fixed right-0 top-0 bottom-0 w-full max-w-sm bg-white shadow-2xl flex flex-col">
                        
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                            <h3 class="font-black text-xl text-slate-900 uppercase tracking-tight">Filters</h3>
                            <button @click="mobileFiltersOpen = false" 
                                    class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 bg-slate-50 rounded-full transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                            <x-site.journal-sidebar 
                                :search="$search" 
                                :sort="$sort" 
                                :alpha="$alpha" 
                                :alphabet="$alphabet" 
                                :brand-color="$brandColor" 
                            />
                        </div>

                        {{-- Footer --}}
                        <div class="p-6 border-t border-slate-100 bg-slate-50">
                            <button @click="mobileFiltersOpen = false" 
                                    class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- CONTENT AREA --}}
            <main class="lg:col-span-9 min-w-0">
                
                {{-- Status Bar --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-1 bg-indigo-600 rounded-full"></div>
                        <p class="text-sm font-medium text-slate-500">
                            Showing <span class="text-slate-900 font-black">{{ $journals->firstItem() ?? 0 }}-{{ $journals->lastItem() ?? 0 }}</span> 
                            of <span class="text-slate-900 font-black">{{ $totalJournals }}</span> specialized journals
                        </p>
                    </div>

                    {{-- Breadcrumbs/Quick Filter Indicator --}}
                    @if($search || ($alpha && $alpha !== 'all'))
                        <div class="flex flex-wrap items-center gap-2">
                             @if($search)
                                <div class="flex items-center gap-2 pl-3 pr-1 py-1 bg-white border border-slate-200 rounded-lg shadow-sm">
                                    <span class="text-[11px] font-bold text-slate-400">Search:</span>
                                    <span class="text-xs font-black text-indigo-600">"{{ Str::limit($search, 15) }}"</span>
                                    <a href="{{ route('portal.journals', array_merge(request()->except('search'))) }}" 
                                       class="w-5 h-5 flex items-center justify-center rounded-md hover:bg-red-50 text-slate-300 hover:text-red-500 transition-colors">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                    </a>
                                </div>
                            @endif
                            @if($alpha && $alpha !== 'all')
                                <div class="flex items-center gap-2 pl-3 pr-1 py-1 bg-white border border-slate-200 rounded-lg shadow-sm">
                                    <span class="text-[11px] font-bold text-slate-400">Alphabet:</span>
                                    <span class="text-xs font-black text-indigo-600 uppercase">{{ $alpha }}</span>
                                    <a href="{{ route('portal.journals', array_merge(request()->except('alpha'))) }}" 
                                       class="w-5 h-5 flex items-center justify-center rounded-md hover:bg-red-50 text-slate-300 hover:text-red-500 transition-colors">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                    </a>
                                </div>
                            @endif
                            <a href="{{ route('portal.journals') }}" class="text-[11px] font-bold text-slate-400 hover:text-indigo-600 uppercase tracking-widest ml-1 transition-colors">Clear All</a>
                        </div>
                    @endif
                </div>

                {{-- Journal Cards Grid --}}
                @if($journals->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($journals as $journal)
                            <x-site.journal-card :journal="$journal" :brand-color="$brandColor" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-16">
                        {{ $journals->links() }}
                    </div>
                @else
                    {{-- Professional Empty State --}}
                    <div class="bg-white rounded-3xl border border-slate-200 border-dashed p-20 text-center shadow-sm">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 relative">
                            <i class="fa-solid fa-book-open-reader text-4xl text-slate-200"></i>
                            <div class="absolute -right-1 -bottom-1 w-8 h-8 bg-white rounded-full border border-slate-100 flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-ban text-red-400 text-xs"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 mb-3">No Journals Cataloged</h3>
                        <p class="text-slate-500 max-w-sm mx-auto mb-10 font-medium leading-relaxed">
                            We couldn't find any journals matching your current criteria. Broaden your search or explore all archives.
                        </p>
                        <a href="{{ route('portal.journals') }}" 
                           class="inline-flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                            Discover all publications
                        </a>
                    </div>
                @endif

            </main>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection
