{{-- Article Landing Page with Google Scholar Metadata (Highwire Press Tags) --}}
@php
$primaryColor = $settings['primary_color'] ?? '#0369a1';
$secondaryColor = $settings['secondary_color'] ?? '#7c3aed';

// Get PDF Galley for citation_pdf_url
$pdfGalley = $article->galleys?->where('file_type', 'application/pdf')->first() 
    ?? $article->galleys?->whereIn('label', ['PDF', 'pdf'])->first()
    ?? $article->galleys?->first();
@endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$article->title . ' | ' . $journal->name">
    {{-- ============================================ --}}
    {{-- GOOGLE SCHOLAR / HIGHWIRE PRESS META TAGS --}}
    {{-- CRITICAL for academic indexing --}}
    {{-- ============================================ --}}
    @push('meta_tags')
        {{-- Highwire Press / Google Scholar Tags --}}
        <meta name="citation_title" content="{{ $article->title }}">
        <meta name="citation_journal_title" content="{{ $journal->name }}">
        @if($journal->abbreviation)
            <meta name="citation_journal_abbrev" content="{{ $journal->abbreviation }}">
        @endif
        @if($journal->publisher)
            <meta name="citation_publisher" content="{{ $journal->publisher }}">
        @endif

        {{-- Publication Date (CRITICAL - Format: YYYY/MM/DD) --}}
        @if($issue && $issue->published_at)
            <meta name="citation_publication_date" content="{{ $issue->published_at->format('Y/m/d') }}">
            <meta name="citation_date" content="{{ $issue->published_at->format('Y/m/d') }}">
            <meta name="citation_online_date" content="{{ $issue->published_at->format('Y/m/d') }}">
        @elseif($article->published_at)
            <meta name="citation_publication_date" content="{{ $article->published_at->format('Y/m/d') }}">
            <meta name="citation_date" content="{{ $article->published_at->format('Y/m/d') }}">
        @endif

        {{-- Volume & Issue --}}
        @if($issue)
            @if($issue->volume)
                <meta name="citation_volume" content="{{ $issue->volume }}">
            @endif
            @if($issue->number)
                <meta name="citation_issue" content="{{ $issue->number }}">
            @endif
        @endif

        {{-- Page Numbers --}}
        @if($article->pages)
            <meta name="citation_firstpage" content="{{ $article->first_page ?? explode('-', $article->pages)[0] ?? $article->pages }}">
            @if($article->last_page ?? (str_contains($article->pages, '-') ? explode('-', $article->pages)[1] ?? null : null))
                <meta name="citation_lastpage" content="{{ $article->last_page ?? explode('-', $article->pages)[1] }}">
            @endif
        @endif

        {{-- DOI (Critical for citation tracking) --}}
        @if($article->doi)
            <meta name="citation_doi" content="{{ $article->doi }}">
        @endif

        {{-- ISSN --}}
        @if($journal->issn_online)
            <meta name="citation_issn" content="{{ $journal->issn_online }}">
        @elseif($journal->issn_print)
            <meta name="citation_issn" content="{{ $journal->issn_print }}">
        @endif

        {{-- Authors (One tag per author - CRITICAL) --}}
        @if($article->authors && $article->authors->isNotEmpty())
            @foreach($article->authors as $author)
                <meta name="citation_author" content="{{ $author->full_name ?? ($author->given_name . ' ' . $author->family_name) }}">
                @if($author->affiliation)
                    <meta name="citation_author_institution" content="{{ $author->affiliation }}">
                @endif
                @if($author->email)
                    <meta name="citation_author_email" content="{{ $author->email }}">
                @endif
                @if($author->orcid)
                    <meta name="citation_author_orcid" content="{{ $author->orcid }}">
                @endif
            @endforeach
        @endif

        {{-- Keywords --}}
        @if($article->keywords && is_array($article->keywords))
            <meta name="citation_keywords" content="{{ implode('; ', $article->keywords) }}">
        @endif

        {{-- Abstract URL (Landing Page) --}}
        <meta name="citation_abstract_html_url" content="{{ url()->current() }}">

        {{-- PDF URL (CRITICAL - Must point to actual download, NOT view page) --}}
        @if($pdfGalley)
            <meta name="citation_pdf_url" content="{{ route('journal.article.download', ['journal' => $journal->slug, 'article' => $article->slug ?? $article->id, 'galley' => $pdfGalley->id]) }}">
        @endif

        {{-- Language --}}
        <meta name="citation_language" content="{{ $article->locale ?? 'en' }}">

        {{-- Dublin Core Metadata --}}
        <meta name="DC.Title" content="{{ $article->title }}">
        <meta name="DC.Creator" content="{{ $article->authors?->pluck('full_name')->implode('; ') }}">
        <meta name="DC.Description" content="{{ Str::limit(strip_tags($article->abstract ?? ''), 300) }}">
        @if($issue && $issue->published_at)
            <meta name="DC.Date" content="{{ $issue->published_at->format('Y-m-d') }}">
        @endif
        <meta name="DC.Publisher" content="{{ $journal->publisher ?? $journal->name }}">
        <meta name="DC.Type" content="Text.Article">
        @if($article->doi)
            <meta name="DC.Identifier" scheme="DOI" content="{{ $article->doi }}">
        @endif

        {{-- PRISM Metadata (Publishing Requirements for Industry Standard Metadata) --}}
        <meta name="prism.publicationName" content="{{ $journal->name }}">
        @if($issue)
            <meta name="prism.volume" content="{{ $issue->volume }}">
            <meta name="prism.number" content="{{ $issue->number }}">
        @endif
        @if($article->doi)
            <meta name="prism.doi" content="{{ $article->doi }}">
        @endif

        {{-- Open Graph for Articles --}}
        <meta property="og:type" content="article">
        <meta property="og:title" content="{{ $article->title }}">
        <meta property="og:description" content="{{ Str::limit(strip_tags($article->abstract ?? ''), 200) }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @if($issue && $issue->published_at)
            <meta property="article:published_time" content="{{ $issue->published_at->toIso8601String() }}">
        @endif
        @if($article->section)
            <meta property="article:section" content="{{ $article->section->name }}">
        @endif
        @foreach($article->authors ?? [] as $author)
            <meta property="article:author" content="{{ $author->full_name ?? ($author->given_name . ' ' . $author->family_name) }}">
        @endforeach

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $article->title }}">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($article->abstract ?? ''), 200) }}">

        {{-- Schema.org JSON-LD for ScholarlyArticle --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ScholarlyArticle",
            "headline": "{{ $article->title }}",
            "name": "{{ $article->title }}",
            "description": "{{ Str::limit(strip_tags($article->abstract ?? ''), 300) }}",
            "author": [
                @foreach($article->authors ?? [] as $index => $author)
                {
                    "@type": "Person",
                    "name": "{{ $author->full_name ?? ($author->given_name . ' ' . $author->family_name) }}"
                    @if($author->affiliation)
                    ,"affiliation": {
                        "@type": "Organization",
                        "name": "{{ $author->affiliation }}"
                    }
                    @endif
                    @if($author->orcid)
                    ,"identifier": "{{ $author->orcid }}"
                    @endif
                }@if(!$loop->last),@endif
                @endforeach
            ],
            @if($issue && $issue->published_at)
            "datePublished": "{{ $issue->published_at->toIso8601String() }}",
            @endif
            "publisher": {
                "@type": "Organization",
                "name": "{{ $journal->publisher ?? $journal->name }}"
            },
            "isPartOf": {
                "@type": "PublicationIssue",
                "issueNumber": "{{ $issue->number ?? '' }}",
                "isPartOf": {
                    "@type": "PublicationVolume",
                    "volumeNumber": "{{ $issue->volume ?? '' }}",
                    "isPartOf": {
                        "@type": "Periodical",
                        "name": "{{ $journal->name }}",
                        "issn": "{{ $journal->issn_online ?? $journal->issn_print }}"
                    }
                }
            },
            @if($article->doi)
            "identifier": {
                "@type": "PropertyValue",
                "propertyID": "DOI",
                "value": "{{ $article->doi }}"
            },
            "sameAs": "https://doi.org/{{ $article->doi }}",
            @endif
            "mainEntityOfPage": "{{ url()->current() }}"
        }
        </script>
    @endpush

    {{-- ============================================ --}}
    {{-- ARTICLE CONTENT --}}
    {{-- ============================================ --}}
    
    {{-- Breadcrumb --}}
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ route('journal.public.home', $journal->slug) }}" class="hover:text-slate-700">{{ $journal->abbreviation ?? $journal->name }}</a>
        <span class="mx-2">/</span>
        @if($issue)
            <a href="{{ route('journal.public.issue', [$journal->slug, $issue->id]) }}" class="hover:text-slate-700">
                Vol. {{ $issue->volume }} No. {{ $issue->number }} ({{ $issue->year }})
            </a>
            <span class="mx-2">/</span>
        @endif
        <span class="text-slate-700">Article</span>
    </nav>

    {{-- Article Header --}}
    <article class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Section Badge --}}
        @if($article->section)
            <div class="px-6 pt-6">
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full"
                      style="background: {{ $primaryColor }}15; color: {{ $primaryColor }};">
                    {{ $article->section->name }}
                </span>
            </div>
        @endif

        {{-- Title --}}
        <div class="px-6 pt-4 pb-6">
            <h1 class="text-2xl md:text-3xl font-serif font-bold text-slate-900 leading-tight">
                {{ $article->title }}
            </h1>

            {{-- Authors --}}
            @if($article->authors && $article->authors->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2">
                    @foreach($article->authors as $author)
                        <div class="flex items-center text-sm">
                            <span class="font-medium text-slate-800">
                                {{ $author->full_name ?? ($author->given_name . ' ' . $author->family_name) }}
                            </span>
                            @if($author->orcid)
                                <a href="https://orcid.org/{{ $author->orcid }}" target="_blank" 
                                   class="ml-1 text-green-600 hover:text-green-700" title="ORCID">
                                    <i class="fa-brands fa-orcid"></i>
                                </a>
                            @endif
                            @if($author->is_corresponding)
                                <span class="ml-1 text-amber-500" title="Corresponding Author">
                                    <i class="fa-solid fa-envelope text-xs"></i>
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
                {{-- Affiliations --}}
                <div class="mt-2 text-sm text-slate-500">
                    @php
                        $affiliations = $article->authors->pluck('affiliation')->filter()->unique()->values();
                    @endphp
                    @foreach($affiliations as $affiliation)
                        <p>{{ $affiliation }}</p>
                    @endforeach
                </div>
            @endif

            {{-- DOI Badge --}}
            @if($article->doi)
                <div class="mt-4">
                    <a href="https://doi.org/{{ $article->doi }}" target="_blank" 
                       class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fa-solid fa-link mr-2"></i>
                        DOI: {{ $article->doi }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Article Meta Bar --}}
        <div class="px-6 py-4 bg-slate-50 border-t border-b border-slate-200 flex flex-wrap gap-6 text-sm text-slate-600">
            @if($issue && $issue->published_at)
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-slate-400"></i>
                    <span>Published: {{ $issue->published_at->format('F d, Y') }}</span>
                </div>
            @endif
            @if($issue)
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-book text-slate-400"></i>
                    <span>Vol. {{ $issue->volume }} No. {{ $issue->number }}</span>
                </div>
            @endif
            @if($article->pages)
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-file-lines text-slate-400"></i>
                    <span>Pages: {{ $article->pages }}</span>
                </div>
            @endif
            @if($article->views_count ?? false)
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-eye text-slate-400"></i>
                    <span>{{ number_format($article->views_count) }} Views</span>
                </div>
            @endif
        </div>

        {{-- Download Buttons --}}
        @if($article->galleys && $article->galleys->isNotEmpty())
            <div class="px-6 py-4 flex flex-wrap gap-3">
                @foreach($article->galleys as $galley)
                    <a href="{{ route('journal.article.download', ['journal' => $journal->slug, 'article' => $article->slug ?? $article->id, 'galley' => $galley->id]) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors
                              {{ $galley->label === 'PDF' ? 'text-white' : 'text-slate-700 bg-slate-100 hover:bg-slate-200' }}"
                       style="{{ $galley->label === 'PDF' ? 'background: ' . $primaryColor . ';' : '' }}">
                        @if($galley->label === 'PDF')
                            <i class="fa-solid fa-file-pdf mr-2"></i>
                        @elseif($galley->label === 'XML')
                            <i class="fa-solid fa-file-code mr-2"></i>
                        @else
                            <i class="fa-solid fa-download mr-2"></i>
                        @endif
                        {{ $galley->label }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Abstract --}}
        @if($article->abstract)
            <div class="px-6 py-6 border-t border-slate-200">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Abstract</h2>
                <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed">
                    {!! $article->abstract !!}
                </div>
            </div>
        @endif

        {{-- Keywords --}}
        @if($article->keywords && is_array($article->keywords) && count($article->keywords) > 0)
            <div class="px-6 py-4 border-t border-slate-200">
                <h3 class="text-sm font-bold text-slate-700 mb-2">Keywords</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($article->keywords as $keyword)
                        <span class="px-3 py-1 text-sm text-slate-600 bg-slate-100 rounded-full">
                            {{ $keyword }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- How to Cite --}}
        <div class="px-6 py-6 border-t border-slate-200 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-700 mb-3">How to Cite</h3>
            <div class="p-4 bg-white rounded-lg border border-slate-200 text-sm text-slate-600 font-mono">
                @php
                    $authorList = $article->authors?->map(function($author) {
                        $familyName = $author->family_name ?? explode(' ', $author->full_name ?? '')[count(explode(' ', $author->full_name ?? '')) - 1] ?? '';
                        $givenName = $author->given_name ?? explode(' ', $author->full_name ?? '')[0] ?? '';
                        return $familyName . ', ' . substr($givenName, 0, 1) . '.';
                    })->implode(', ') ?? 'Author';
                @endphp
                {{ $authorList }} ({{ $issue?->year ?? now()->year }}). {{ $article->title }}. 
                <em>{{ $journal->name }}</em>, 
                <em>{{ $issue?->volume ?? '' }}</em>({{ $issue?->number ?? '' }}), 
                {{ $article->pages ?? '' }}.
                @if($article->doi)
                    https://doi.org/{{ $article->doi }}
                @endif
            </div>
            <button onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent.trim())"
                    class="mt-2 text-sm font-medium hover:underline" style="color: {{ $primaryColor }};">
                <i class="fa-regular fa-copy mr-1"></i> Copy Citation
            </button>
        </div>
    </article>

    {{-- References Section (if available) --}}
    @if($article->references)
        <section class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">References</h2>
            <div class="prose prose-sm prose-slate max-w-none">
                {!! $article->references !!}
            </div>
        </section>
    @endif

    {{-- Share Buttons --}}
    <section class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-slate-700 mb-4">Share This Article</h3>
        <div class="flex gap-3">
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" 
               target="_blank"
               class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center hover:bg-sky-200 transition-colors">
                <i class="fa-brands fa-x-twitter"></i>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
               target="_blank"
               class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200 transition-colors">
                <i class="fa-brands fa-facebook-f"></i>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($article->title) }}" 
               target="_blank"
               class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center hover:bg-blue-200 transition-colors">
                <i class="fa-brands fa-linkedin-in"></i>
            </a>
            <a href="mailto:?subject={{ urlencode($article->title) }}&body={{ urlencode(url()->current()) }}" 
               class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-envelope"></i>
            </a>
        </div>
    </section>
</x-layouts.public>
