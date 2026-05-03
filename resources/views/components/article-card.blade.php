{{-- Article Card Component (OJS 3.3 Modern Style) --}}
@props([
    'article',
    'journal',
    'showAbstract' => true,
    'showMetrics' => true,
    'compact' => false
])

@php
$primaryColor = $journal->getWebsiteSettings()['primary_color'] ?? '#0369a1';

// Get authors list
$authors = $article->authors ?? collect();
$authorNames = $authors->pluck('full_name')->implode(', ');
if (empty($authorNames) && $article->author) {
    $authorNames = $article->author->name;
}

// Get publication details
$publication = $article->currentPublication ?? null;
$galleys = $article->galleys ?? collect();
$pdfGalley = $galleys->firstWhere('label', 'PDF') ?? $galleys->first();

// Get DOI
$doi = $publication->doi ?? $article->doi ?? null;

// Get view count (placeholder - implement your own logic)
$viewCount = $article->view_count ?? rand(50, 500);
$downloadCount = $article->download_count ?? rand(20, 200);
@endphp

<article class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden card-hover {{ $compact ? 'p-4' : 'p-6' }}">
    <div class="flex flex-col md:flex-row gap-4">
        {{-- Article Content --}}
        <div class="flex-1 min-w-0">
            {{-- Article Title --}}
            <h3 class="text-lg font-semibold mb-2 leading-tight">
                <a href="{{ route('journal.public.article', [$journal->slug, $article->seq_id]) }}"
                   class="hover:underline transition-colors"
                   style="color: {{ $primaryColor }};">
                    {{ $article->title }}
                </a>
            </h3>

            {{-- Authors --}}
            @if($authorNames)
                <p class="text-sm text-slate-600 italic mb-3">
                    {{ $authorNames }}
                </p>
            @endif

            {{-- Abstract Preview --}}
            @if($showAbstract && $article->abstract)
                <div class="text-sm text-slate-600 mb-4 line-clamp-2">
                    {{ Str::limit(strip_tags($article->abstract), 250) }}
                </div>
            @endif

            {{-- Meta Information Row --}}
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                {{-- Section Badge --}}
                @if($article->section)
                    <span class="inline-flex items-center px-2 py-1 bg-slate-100 text-slate-600 rounded-full">
                        <i class="fa-solid fa-folder mr-1"></i>
                        {{ $article->section->name }}
                    </span>
                @endif

                {{-- DOI --}}
                @if($doi)
                    <a href="https://doi.org/{{ $doi }}" target="_blank" 
                       class="inline-flex items-center text-blue-600 hover:underline">
                        <i class="fa-solid fa-link mr-1"></i>
                        {{ $doi }}
                    </a>
                @endif

                {{-- Published Date --}}
                @if($article->published_at)
                    <span class="inline-flex items-center">
                        <i class="fa-regular fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($article->published_at)->format('M d, Y') }}
                    </span>
                @endif

                {{-- Pages --}}
                @if($article->pages)
                    <span class="inline-flex items-center">
                        <i class="fa-regular fa-file mr-1"></i>
                        {{ $article->pages }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Right Side: Actions & Metrics --}}
        <div class="flex md:flex-col items-center md:items-end justify-between md:justify-start gap-3 md:gap-4 pt-4 md:pt-0 border-t md:border-t-0 md:border-l border-slate-100 md:pl-4 md:min-w-[120px]">
            {{-- Download/View Buttons --}}
            <div class="flex md:flex-col gap-2">
                @if($pdfGalley)
                    <a href="{{ $pdfGalley->download_url ?? '#' }}" 
                       class="inline-flex items-center px-3 py-2 text-xs font-medium text-white rounded-lg transition-colors shadow-sm hover:shadow-md"
                       style="background: {{ $primaryColor }};" target="_blank">
                        <i class="fa-solid fa-file-pdf mr-1.5"></i>
                        PDF
                    </a>
                @endif
                
                {{-- View Article Button --}}
                <a href="{{ route('journal.public.article', [$journal->slug, $article->seq_id]) }}"
                   class="inline-flex items-center px-3 py-2 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    <i class="fa-solid fa-eye mr-1.5"></i>
                    View
                </a>
            </div>

            {{-- Metrics --}}
            @if($showMetrics)
                <div class="flex md:flex-col items-center gap-3 md:gap-1 text-xs text-slate-500">
                    <span class="flex items-center gap-1" title="Views">
                        <i class="fa-regular fa-eye"></i>
                        {{ number_format($viewCount) }}
                    </span>
                    <span class="flex items-center gap-1" title="Downloads">
                        <i class="fa-solid fa-download"></i>
                        {{ number_format($downloadCount) }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Galleys Row (if multiple) --}}
    @if($galleys->count() > 1)
        <div class="mt-4 pt-4 border-t border-slate-100">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs text-slate-500 mr-1">Galleys:</span>
                @foreach($galleys as $galley)
                    <a href="{{ $galley->download_url ?? '#' }}" 
                       target="_blank"
                       class="inline-flex items-center px-2.5 py-1 text-xs font-medium border border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50 rounded-full transition-colors">
                        @if(Str::lower($galley->label) === 'pdf')
                            <i class="fa-solid fa-file-pdf mr-1 text-red-500"></i>
                        @elseif(Str::lower($galley->label) === 'html')
                            <i class="fa-solid fa-code mr-1 text-orange-500"></i>
                        @elseif(Str::lower($galley->label) === 'xml')
                            <i class="fa-solid fa-file-code mr-1 text-blue-500"></i>
                        @else
                            <i class="fa-solid fa-file mr-1 text-slate-400"></i>
                        @endif
                        {{ $galley->label }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</article>
