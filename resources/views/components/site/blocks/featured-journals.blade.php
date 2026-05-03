{{-- Featured Journals Block - Interactive Grid Cards --}}
@props(['block', 'data' => []])

@php
$config = $block->config ?? [];
$title = $config['title'] ?? 'Featured Journals';
$subtitle = $config['subtitle'] ?? 'Explore our top-rated peer-reviewed publications';
$layout = $config['layout'] ?? 'grid';
$columns = $config['columns'] ?? 4;
$limit = $config['limit'] ?? 8;
$showBadges = $config['show_badges'] ?? true;
$showStats = $config['show_stats'] ?? true;

$journals = $data['journals'] ?? collect();
@endphp

<section class="py-16 md:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 rounded-full mb-4">
                <i class="fa-solid fa-star mr-2"></i>
                Featured
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ $title }}
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                {{ $subtitle }}
            </p>
        </div>

        {{-- Journals Grid --}}
        @if($journals->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($journals->take($limit) as $journal)
                    <div class="flex flex-col bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden h-full group">
                        
                        {{-- Cover Image --}}
                        <div class="p-4 flex items-center justify-center border-b border-slate-50 bg-slate-50 relative">
                            @if($journal->thumbnail_path)
                                <img src="{{ Storage::url($journal->thumbnail_path) }}"
                                     alt="{{ $journal->name }} Cover"
                                     class="max-h-48 object-contain border border-indigo-100 p-0.5 bg-white shadow-sm transition-transform duration-300 group-hover:scale-[1.02]">
                            @else
                                <div class="max-h-48 h-40 w-32 flex items-center justify-center bg-slate-100 border border-slate-200 text-slate-300 p-1 shadow-sm rounded-sm">
                                    <i class="fa-solid fa-book-open text-3xl"></i>
                                </div>
                            @endif

                            {{-- Optional: Sinta Badge or other status can go here floating --}}
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 p-5 flex flex-col items-center text-center">
                            <h3 class="text-base md:text-lg font-extrabold uppercase text-indigo-900 mb-2 leading-snug">
                                <a href="{{ route('journal.public.home', $journal->slug) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $journal->name }}
                                </a>
                            </h3>
                            
                            <div class="text-xs md:text-sm text-slate-500 line-clamp-3 leading-relaxed mb-4">
                                {{ strip_tags($journal->description) }}
                            </div>

                            @if($journal->issn_online || $journal->issn_print)
                                <div class="mt-auto pt-2 text-xs font-semibold text-indigo-500 uppercase tracking-wide bg-indigo-50/50 px-2.5 py-1 rounded-md">
                                    @if($journal->issn_online) E-ISSN: {{ $journal->issn_online }} @endif
                                    @if($journal->issn_online && $journal->issn_print) &bull; @endif
                                    @if($journal->issn_print) P-ISSN: {{ $journal->issn_print }} @endif
                                </div>
                            @endif
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="mt-auto bg-slate-50 border-t border-slate-100 px-4 py-3 flex flex-row items-center justify-between gap-3">
                            <a href="{{ route('journal.public.home', $journal->slug) }}" class="flex-1 text-center text-sm font-semibold text-slate-700 hover:text-indigo-600 hover:underline underline-offset-2 transition-all">
                                View Journal
                            </a>
                            <a href="{{ route('journal.public.current', $journal->slug) }}" class="flex-1 text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
                                Current Issue
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- View All Button --}}
            <div class="text-center mt-12">
                <a href="{{ route('portal.journals') }}"
                   class="inline-flex items-center px-8 py-4 bg-white border border-slate-300 text-slate-700 font-semibold rounded-sm hover:border-indigo-500 hover:text-indigo-600 shadow-sm hover:shadow transition-all group">
                    View All Journals
                    <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <i class="fa-solid fa-book text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No journals available yet.</p>
            </div>
        @endif
    </div>
</section>
