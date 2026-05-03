@props(['journal', 'brandColor' => '#00629B', 'secondaryColor' => '#7c3aed'])

<div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 p-6 flex flex-col md:flex-row gap-6 group">
    {{-- Thumbnail / Cover --}}
    <div class="flex-shrink-0 mx-auto md:mx-0">
        @if($journal->thumbnail_path)
            <img src="{{ Storage::url($journal->thumbnail_path) }}" 
                 alt="{{ $journal->name }}" 
                 class="w-32 h-44 object-cover rounded-lg shadow-sm border border-gray-100 group-hover:opacity-90 transition-opacity">
        @else
            <div class="w-32 h-44 rounded-lg bg-gray-50 border border-gray-200 flex flex-col items-center justify-center p-3 text-center"
                 style="border-color: {{ $brandColor }}30; background-color: {{ $brandColor }}08">
                <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center mb-2">
                    <span class="text-xl font-bold" style="color: {{ $brandColor }}">{{ substr($journal->name, 0, 1) }}</span>
                </div>
                <span class="text-xs font-medium line-clamp-2 leading-tight" style="color: {{ $brandColor }}">
                    {{ $journal->abbreviation ?? 'Journal' }}
                </span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="flex-grow flex flex-col justify-between">
        <div>
            {{-- Top Row: Badges --}}
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    <i class="fa-solid fa-lock-open mr-1"></i> Open Access
                </span>
                
                {{-- Accreditation Placeholders --}}
                @if(rand(0,1)) 
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                        SINTA {{ rand(1, 6) }}
                    </span>
                @endif
            </div>

            {{-- Title --}}
            <h3 class="text-xl md:text-2xl font-bold mb-2">
                <a href="{{ route('journal.public.home', $journal->slug) }}" 
                   class="hover:opacity-80 transition-opacity"
                   style="color: {{ $brandColor }}">
                   {{ $journal->name }}
                </a>
            </h3>

            {{-- ISSN --}}
            @if($journal->issn_online || $journal->issn_print)
                <div class="flex items-center gap-4 text-xs text-slate-500 font-mono mb-4">
                    @if($journal->issn_online)
                        <span><span class="font-semibold text-slate-700">e-ISSN:</span> {{ $journal->issn_online }}</span>
                    @endif
                    @if($journal->issn_print)
                        <span><span class="font-semibold text-slate-700">p-ISSN:</span> {{ $journal->issn_print }}</span>
                    @endif
                </div>
            @endif

            {{-- Description --}}
            <div class="text-sm text-slate-600 mb-4 line-clamp-2 leading-relaxed">
                {{ $journal->description }}
            </div>
        </div>

        {{-- Bottom Row: Metrics & Actions --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-auto pt-4 border-t border-gray-100">
            {{-- Metrics --}}
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-1.5" title="Latest Publication">
                    <i class="fa-regular fa-calendar-alt text-slate-400"></i>
                    <span>Updated 2 days ago</span> 
                </div>
                <div class="flex items-center gap-1.5" title="Total Published Issues">
                    <i class="fa-solid fa-layer-group text-slate-400"></i>
                    <span>{{ $journal->issues_count ?? 0 }} Issues</span>
                </div>
                <div class="flex items-center gap-1.5" title="Total Articles">
                    <i class="fa-regular fa-file-alt text-slate-400"></i>
                    <span>{{ $journal->submissions_count ?? 0 }} Articles</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('journal.public.current', $journal->slug) }}" 
                   class="text-sm font-medium text-slate-600 hover:text-slate-900 px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                    Current Issue
                </a>
                
                {{-- View Journal Button (Dynamic Outline) --}}
                <a href="{{ route('journal.public.home', $journal->slug) }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-transparent border hover:text-white transition-all shadow-sm"
                   style="border-color: {{ $brandColor }}; color: {{ $brandColor }};"
                   onmouseover="this.style.backgroundColor='{{ $brandColor }}'; this.style.color='white';"
                   onmouseout="this.style.backgroundColor='transparent'; this.style.color='{{ $brandColor }}';">
                    View Journal <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>
