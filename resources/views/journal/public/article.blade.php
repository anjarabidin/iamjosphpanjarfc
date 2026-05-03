@php
    $primaryColor = $settings['primary_color'] ?? '#0369a1';
    $secondaryColor = $settings['secondary_color'] ?? '#7c3aed';

    // Get PDF Galley for citation_pdf_url (Google Scholar)
    $pdfGalley =
        $article->galleys?->where('file_type', 'application/pdf')->first() ??
        ($article->galleys?->whereIn('label', ['PDF', 'pdf'])->first() ?? $article->galleys?->first());

    // Publication date logic
    $publicationDate = $issue?->published_at ?? $article->published_at;
@endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$article->title . ' | ' . $journal->name">

    {{-- CANONICAL URL --}}
    @push('meta_tags')
        <link rel="canonical"
            href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}" />
    @endpush

    {{-- ============================================ --}}
    {{-- GOOGLE SCHOLAR META TAGS (Keep existing) --}}
    {{-- ============================================ --}}
    {{-- ============================================ --}}
    {{-- GOOGLE SCHOLAR / HIGHWIRE PRESS META TAGS --}}
    {{-- CRITICAL for academic indexing --}}
    {{-- ============================================ --}}
    @push('meta_tags')
        @php
            $pub = $article->currentPublication ?? $article;
            $pubTitle = $pub->title ?? $article->title;
            // Get date_published from currentPublication if it exists, otherwise fallback to $publicationDate
            $pubDate = $pub->date_published ?? ($issue?->published_at ?? $article->published_at);
            $pubPages = $pub->pages ?? $article->pages;
            $pubDoi = $pub->doi ?? $article->doi;
            $pubKeywords = $pub->keywords ?? $article->keywords;
            $pubAbstract = $pub->abstract ?? $article->abstract;
            $pubAuthors = $pub->authors ?? $article->authors;
        @endphp

        {{-- Highwire Press / Google Scholar Tags --}}
        {{-- =================================== --}}
        <meta name="gs_meta_revision" content="1.1">
        <meta name="citation_title" content="{{ $pubTitle }}">
        <meta name="citation_journal_title" content="{{ $journal->name }}">
        @if ($journal->abbreviation)
            <meta name="citation_journal_abbrev" content="{{ $journal->abbreviation }}">
        @endif
        @if ($journal->publisher)
            <meta name="citation_publisher" content="{{ $journal->publisher }}">
        @endif

        {{-- Publication Date (CRITICAL - Format: YYYY/MM/DD) --}}
        @if ($pubDate)
            <meta name="citation_publication_date" content="{{ $pubDate->format('Y/m/d') }}">
            <meta name="citation_date" content="{{ $pubDate->format('Y/m/d') }}">
            @if ($issue && $issue->year)
                {{-- Ensure citation_year is present for timeline filtering --}}
                <meta name="citation_year" content="{{ $issue->year }}">
            @else
                <meta name="citation_year" content="{{ $pubDate->format('Y') }}">
            @endif
        @endif

        {{-- Volume & Issue --}}
        @if ($issue)
            @if ($issue->volume)
                <meta name="citation_volume" content="{{ $issue->volume }}">
            @endif
            @if ($issue->number)
                <meta name="citation_issue" content="{{ $issue->number }}">
            @endif
        @endif

        {{-- Page Numbers --}}
        @if ($pubPages)
            @php
                $pages = explode('-', $pubPages);
                $firstPage = $pages[0] ?? $pubPages;
                $lastPage = $pages[1] ?? null;
            @endphp
            <meta name="citation_firstpage" content="{{ trim($firstPage) }}">
            @if ($lastPage)
                <meta name="citation_lastpage" content="{{ trim($lastPage) }}">
            @endif
        @endif

        {{-- DOI (Critical for citation tracking) --}}
        @if ($pubDoi)
            <meta name="citation_doi" content="{{ $pubDoi }}">
        @endif

        {{-- ISSN --}}
        @if ($journal->issn_online)
            <meta name="citation_issn" content="{{ $journal->issn_online }}">
        @elseif($journal->issn_print)
            <meta name="citation_issn" content="{{ $journal->issn_print }}">
        @endif

        {{-- Authors (One tag per author - CRITICAL: First Last) --}}
        @if ($pubAuthors && $pubAuthors->isNotEmpty())
            @foreach ($pubAuthors as $author)
                <meta name="citation_author" content="{{ $author->first_name }} {{ $author->last_name }}">
                @if ($author->affiliation)
                    <meta name="citation_author_institution" content="{{ $author->affiliation }}">
                @endif
            @endforeach
        @endif

        {{-- Keywords (Fixing the JSON bug: Output one tag per keyword) --}}
        @if ($pubKeywords && count(is_array($pubKeywords) ? $pubKeywords : (is_string($pubKeywords) ? explode(',', $pubKeywords) : [$pubKeywords])) > 0)
            @php
                $rawKeywords = $pubKeywords;
                $processedKeywords = [];

                // 1. Handle JSON String (Tagify output)
                if (is_string($rawKeywords)) {
                    $trimmed = trim($rawKeywords);
                    if (str_starts_with($trimmed, '[') || str_starts_with($trimmed, '{')) {
                        $decoded = json_decode($trimmed, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $rawKeywords = $decoded; // Now it's an array
        }
    } else {
        // Comma separated string
        $rawKeywords = explode(',', $trimmed);
    }
}

// 2. Iterate (Whether Collection, Array from JSON, or Array from explode)
foreach ($rawKeywords as $k) {
    $val = null;
    if (is_object($k)) {
        // Collection of Models or Objects
        $val = $k->content ?? ($k->value ?? ($k->keyword ?? null));
    } elseif (is_array($k)) {
        // Array from JSON decode
        $val = $k['content'] ?? ($k['value'] ?? ($k['keyword'] ?? null));
                    } else {
                        // String
                        $val = $k;
                    }

                    if ($val) {
                        $processedKeywords[] = trim((string) $val);
                    }
                }
            @endphp
            @foreach ($processedKeywords as $keyword)
                <meta name="citation_keywords" content="{{ $keyword }}">
            @endforeach
        @endif

        {{-- Abstract URL (Landing Page) --}}
        <meta name="citation_abstract_html_url" content="{{ url()->current() }}">
        <meta name="citation_fulltext_html_url" content="{{ route('journal.public.article', [$journal->slug, $article->seq_id]) }}">

        {{-- PDF URL (CRITICAL - Must point to actual download file) --}}
        {{-- PDF URL (CRITICAL - Must point to actual download file w/ SEO friendly URL) --}}
        @if ($pdfGalley)
            <meta name="citation_pdf_url"
                content="{{ route('journal.article.download.pdf', [
                    'journal' => $journal->slug, 
                    'seq_id' => $article->seq_id, 
                    'filename' => Str::slug($article->title)
                ]) }}">
        @endif

        {{-- Abstract (Stripped and Decoded) --}}
        @if ($pubAbstract)
            <meta name="citation_abstract" content="{{ trim(strip_tags(html_entity_decode($pubAbstract))) }}">
        @endif

        {{-- References (One tag per line) --}}
        @php
            $referencesStr = $article->currentPublication->references ?? $article->references;
            $referencesArray = $referencesStr ? array_filter(array_map('trim', explode("\n", $referencesStr))) : [];
        @endphp
        @foreach ($referencesArray as $ref)
            <meta name="citation_reference" content="{{ trim(strip_tags(html_entity_decode($ref))) }}">
        @endforeach

        {{-- Dublin Core Metadata --}}
        @if ($pubAuthors && $pubAuthors->isNotEmpty())
            @foreach ($pubAuthors as $author)
                <meta name="DC.Creator.PersonalName" content="{{ $author->first_name }} {{ $author->last_name }}">
            @endforeach
        @endif
        @if ($pubDate)
            <meta name="DC.Date.issued" content="{{ $pubDate->format('Y-m-d') }}">
        @endif
        @if ($pubAbstract)
            <meta name="DC.Description" xml:lang="en" content="{{ trim(strip_tags(html_entity_decode($pubAbstract))) }}">
        @endif
        @if ($pubDoi)
            <meta name="DC.Identifier.DOI" content="{{ $pubDoi }}">
        @endif
        <meta name="DC.Identifier.URI" content="{{ url()->current() }}">
        <meta name="DC.Language" scheme="ISO639-1" content="{{ $article->locale ?? 'en' }}">
        @if ($journal->issn_online)
            <meta name="DC.Source.ISSN" content="{{ $journal->issn_online }}">
        @elseif($journal->issn_print)
            <meta name="DC.Source.ISSN" content="{{ $journal->issn_print }}">
        @endif
        <meta name="DC.Source" content="{{ $journal->name }}">
        <meta name="DC.Type" content="Text.Serial.Journal">
        <meta name="DC.Type.articleType" content="Articles">

        {{-- Language --}}
        <meta name="citation_language" content="{{ $article->locale ?? 'en' }}">
    @endpush
    {{-- Schema.org JSON-LD for ScholarlyArticle --}}
    @php
        $schemaData = [
            '@context' => 'https://schema.org',
            '@type' => 'ScholarlyArticle',
            'headline' => $article->title,
            'name' => $article->title,
            'description' => Str::limit(strip_tags($article->abstract ?? ''), 300),
            'author' =>
                $article->authors
                    ?->map(function ($author) {
                        $a = [
                            '@type' => 'Person',
                            'name' => $author->full_name ?? $author->last_name . ' ' . $author->first_name,
                        ];
                        if (!empty($author->affiliation)) {
                            $a['affiliation'] = [
                                '@type' => 'Organization',
                                'name' => $author->affiliation,
                            ];
                        }
                        return $a;
                    })
                    ->toArray() ?? [],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $journal->publisher ?? $journal->name,
            ],
            'mainEntityOfPage' => url()->current(),
        ];

        if ($publicationDate) {
            $schemaData['datePublished'] = $publicationDate->toIso8601String();
        }

        if ($issue) {
            $schemaData['isPartOf'] = [
                '@type' => 'PublicationIssue',
                'issueNumber' => $issue->number ?? '',
                'isPartOf' => [
                    '@type' => 'PublicationVolume',
                    'volumeNumber' => $issue->volume ?? '',
                    'isPartOf' => [
                        '@type' => 'Periodical',
                        'name' => $journal->name,
                        'issn' => $journal->issn_online ?? $journal->issn_print,
                    ],
                ],
            ];
        }

        if ($article->doi) {
            $schemaData['identifier'] = [
                '@type' => 'PropertyValue',
                'propertyID' => 'DOI',
                'value' => $article->doi,
            ];
            $schemaData['sameAs'] = 'https://doi.org/' . $article->doi;
        }
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schemaData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
    </script>

    {{-- ============================================ --}}
    {{-- BREADCRUMB NAVIGATION --}}
    {{-- ============================================ --}}
    <nav class="text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li>
                <a href="{{ route('journal.public.home', $journal->slug) }}"
                    class="hover:text-primary-600 hover:underline">
                    <i class="fa-solid fa-home mr-1"></i>Home
                </a>
            </li>
            <li class="text-slate-400">/</li>
            <li>
                <a href="{{ route('journal.public.archives', $journal->slug) }}"
                    class="hover:text-primary-600 hover:underline">
                    Archives
                </a>
            </li>
            @if ($issue)
                <li class="text-slate-400">/</li>
                <li>
                    <a href="{{ route('journal.public.issue', [$journal->slug, $issue->seq_id]) }}"
                        class="hover:text-primary-600 hover:underline">
                        Vol. {{ $issue->volume }} No. {{ $issue->number }} ({{ $issue->year }})
                    </a>
                </li>
            @endif
            <li class="text-slate-400">/</li>
            <li class="text-slate-800 font-medium truncate max-w-xs">
                {{ Str::limit($article->title, 40) }}
            </li>
        </ol>
    </nav>

    {{-- ============================================ --}}
    {{-- 2-COLUMN LAYOUT: MAIN CONTENT + SIDEBAR --}}
    {{-- OJS 3.3 FLAT DESIGN (NO CARDS) --}}
    {{-- ============================================ --}}
    <div class="flex flex-col lg:flex-row gap-12">

        {{-- ================= LEFT COLUMN (MAIN CONTENT - 75%) ================= --}}
        <main class="w-full lg:w-3/4 min-w-0 space-y-10">

            {{-- 1. TITLE --}}
            <h1 class="text-3xl md:text-4xl font-serif font-bold text-slate-900 leading-tight">
                {{ $article->title }}
            </h1>

            @if ($article->subtitle)
                <p class="text-xl text-slate-600 -mt-6">{{ $article->subtitle }}</p>
            @endif

            {{-- 2. AUTHORS --}}
            <div class="space-y-3">
                @if ($article->authors && $article->authors->isNotEmpty())
                    @foreach ($article->authors as $author)
                        <div class="leading-snug">
                            <div class="font-bold text-slate-900 text-lg">
                                {{ $author->first_name }} {{ $author->last_name }}
                                @if ($author->is_corresponding)
                                    <span class="text-orange-500 text-sm ml-1" title="Corresponding Author">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                @endif
                                @if ($author->orcid)
                                    <a href="https://orcid.org/{{ $author->orcid }}" target="_blank"
                                        class="text-green-600 hover:text-green-700 ml-2"
                                        title="ORCID: {{ $author->orcid }}">
                                        <i class="fa-brands fa-orcid text-lg"></i>
                                    </a>
                                @endif
                            </div>
                            @if ($author->affiliation)
                                <div class="text-slate-500 text-sm italic">{{ $author->affiliation }}</div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- 3. DOI --}}
            @php
                $doi = $article->currentPublication->doi ?? $article->doi;
            @endphp
            @if ($doi)
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-bold text-slate-700">DOI:</span>
                    <a href="https://doi.org/{{ $doi }}" target="_blank"
                        class="text-primary-600 hover:underline break-all">
                        https://doi.org/{{ $doi }}
                    </a>
                </div>
            @endif

            {{-- 4. KEYWORDS --}}
            {{-- 4. KEYWORDS --}}
            @php
                $rawKeywords = $article->currentPublication->keywords ?? $article->keywords;
                $keywords = [];
                if (!empty($rawKeywords)) {
                    if (is_string($rawKeywords)) {
                        $keywords = array_filter(array_map('trim', explode(',', $rawKeywords)));
                    } elseif (is_array($rawKeywords)) {
                        $keywords = $rawKeywords;
                    }
                }
            @endphp

            @if (!empty($keywords))
                <div class="mb-6">
                    <span class="font-bold text-slate-700">Keywords:</span>
                    <span class="text-slate-600">
                        @foreach ($keywords as $keyword)
                            @if (!$loop->first)
                                ,
                            @endif
                            <a href="{{ route('journal.public.search', ['journal' => $journal->slug, 'q' => $keyword]) }}"
                                class="hover:text-primary-600 hover:underline">{{ $keyword }}</a>
                        @endforeach
                    </span>
                </div>
            @endif

            {{-- 5. ABSTRACT (Orange Underline) --}}
            @if ($article->abstract)
                <div class="pt-6">
                    <h3
                        class="text-xl font-bold text-slate-800 border-b-4 border-orange-400 inline-block mb-4 pb-1 uppercase tracking-wide">
                        Abstract
                    </h3>
                    <div class="prose max-w-none text-slate-700 leading-relaxed text-justify">
                        {!! clean($article->abstract) !!}
                    </div>
                </div>
            @endif

            {{-- 6. DOWNLOADS CHART (Orange Underline) --}}
            <div class="pt-6">
                <h3
                    class="text-xl font-bold text-slate-800 border-b-4 border-orange-400 inline-block mb-6 pb-1 uppercase tracking-wide">
                    Downloads
                </h3>

                {{-- Summary Stats (Inline) --}}
                <div class="flex gap-8 mb-6 text-center">
                    <div>
                        <div class="text-3xl font-bold text-blue-600">
                            {{ number_format(array_sum(($viewsData ?? collect())->toArray())) }}
                        </div>
                        <div class="text-sm text-slate-600 mt-1">Total Views</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-green-600">
                            {{ number_format(array_sum(($downloadsData ?? collect())->toArray())) }}
                        </div>
                        <div class="text-sm text-slate-600 mt-1">Total Downloads</div>
                    </div>
                </div>

                {{-- Chart --}}
                <div class="h-64 w-full">
                    <canvas id="statsChart"></canvas>
                </div>

                {{-- Geographic Distribution (Admin Only) --}}
                @if (auth()->check() &&
                        auth()->user()->hasJournalPermission([
                            \App\Models\Role::LEVEL_ADMIN,
                            \App\Models\Role::LEVEL_MANAGER,
                            \App\Models\Role::LEVEL_EDITOR
                        ], $journal->id) &&
                        !empty($countryStats))
                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <h4 class="text-lg font-bold text-slate-700 mb-4">
                            <i class="fa-solid fa-globe mr-2"></i>
                            Geographic Distribution (Top 10 Countries)
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 text-slate-600">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold">Country</th>
                                        <th class="px-4 py-2 text-right font-semibold">Views</th>
                                        <th class="px-4 py-2 text-left font-semibold">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($countryStats as $stat)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 font-medium text-slate-800">
                                                <i class="fa-solid fa-flag mr-2 text-slate-400"></i>
                                                {{ $stat->country_code }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-slate-700">
                                                {{ number_format($stat->views) }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 bg-slate-200 rounded-full h-2 overflow-hidden">
                                                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-blue-600"
                                                            style="width: {{ $stat->percentage }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-slate-500 min-w-[3rem] text-right">
                                                        {{ number_format($stat->percentage, 1) }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 7. REFERENCES (Orange Underline) --}}
            {{-- 7. REFERENCES (Orange Underline) --}}
            @php
                $references = $article->currentPublication->references ?? $article->references;
            @endphp
            @if ($references)
                <div class="pt-6">
                    <h3
                        class="text-xl font-bold text-slate-800 border-b-4 border-orange-400 inline-block mb-4 pb-1 uppercase tracking-wide">
                        References
                    </h3>
                    <div class="prose prose-sm max-w-none text-slate-600 text-sm leading-relaxed">
                        @php
                            $safeReferences = e($references);
                            $linkedReferences = preg_replace_callback(
                                '/(https?:\/\/[^\s]+)/',
                                function ($matches) {
                                    $url = $matches[1];
                                    $trailing = '';
                                    if (preg_match('/[.,;]$/', $url, $m)) {
                                        $url = substr($url, 0, -1);
                                        $trailing = $m[0];
                                    }
                                    return '<a href="' .
                                        $url .
                                        '" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 hover:underline break-all">' .
                                        $url .
                                        '</a>' .
                                        $trailing;
                                },
                                $safeReferences,
                            );
                        @endphp
                        {!! clean(nl2br($linkedReferences)) !!}
                    </div>
                </div>
            @endif

            {{-- 8. SIMILAR ARTICLES (Blue Header) --}}
            @if ($relatedArticles && $relatedArticles->isNotEmpty())
                <div class="pt-8 border-t border-slate-200">
                    <h3 class="text-xl font-bold text-primary-700 mb-4">
                        <i class="fa-solid fa-newspaper mr-2"></i>Similar Articles
                    </h3>
                    <div class="space-y-3 text-slate-700">
                        @foreach ($relatedArticles as $related)
                            <div>
                                @php
                                    $authors = $related->authors
                                        ? $related->authors
                                            ->map(function ($author) {
                                                return trim($author->first_name . ' ' . $author->last_name);
                                            })
                                            ->implode(', ')
                                        : '';
                                    $issueLink = $related->issue
                                        ? route('journal.public.issue', [$journal->slug, $related->issue->seq_id])
                                        : '#';
                                    $issueText = $journal->name;
                                    if ($related->issue) {
                                        $issueText .=
                                            ': Vol. ' .
                                            ($related->issue->volume ?? '-') .
                                            ' No. ' .
                                            ($related->issue->number ?? '-') .
                                            ' (' .
                                            ($related->issue->year ?? '-') .
                                            '): ' .
                                            ($related->issue->published_at
                                                ? $related->issue->published_at->format('F')
                                                : '') .
                                            ': ' .
                                            $journal->name;
                                    }
                                @endphp
                                {{ $authors }},
                                <a href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $related->seq_id]) }}"
                                    class="hover:text-primary-600 hover:underline font-medium">
                                    {{ $related->title }}
                                </a>,
                                <a href="{{ $issueLink }}" class="hover:text-primary-600 hover:underline">
                                    {{ $issueText }}
                                </a>
                                @if (!empty($related->doi))
                                    <div class="text-sm mt-0.5">
                                        <span class="font-bold text-slate-600">DOI:</span>
                                        <a href="https://doi.org/{{ $related->doi }}" target="_blank"
                                            class="text-primary-600 hover:underline">
                                            https://doi.org/{{ $related->doi }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </main>

        {{-- ================= RIGHT COLUMN (SIDEBAR - 25%) ================= --}}
        {{-- ================= RIGHT COLUMN (SIDEBAR - 25%) ================= --}}
        <aside class="w-full lg:w-1/4 space-y-6">

            {{-- ISSUE COVER (Clickable - Links to Issue Page) --}}
            @if ($issue)
                @if ($issue->cover_path)
                    <a href="{{ route('journal.public.issue', [$journal->slug, $issue->seq_id]) }}"
                        class="block hover:opacity-90 transition group">
                        <img src="{{ Storage::url($issue->cover_path) }}" alt="{{ $issue->title }}"
                            class="w-full rounded shadow-md border border-slate-200">
                    </a>
                @endif
            @endif

            {{-- FULL TEXT BUTTONS (Teal/OJS Style) --}}
            <div class="bg-slate-50 p-5 rounded border border-slate-200">
                <h4 class="font-bold text-slate-700 text-xs uppercase mb-3 tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-download"></i>
                    Full Text
                </h4>

                @if ($article->galleys && $article->galleys->isNotEmpty())
                    <div class="space-y-2">
                        @foreach ($article->galleys as $galley)
                            @php
                                $isPdf = str_contains(strtolower($galley->file_type ?? ''), 'pdf') || strtolower($galley->label ?? '') === 'pdf';
                                $downloadRoute = $isPdf 
                                    ? route('journal.article.download.pdf', ['journal' => $journal->slug, 'seq_id' => $article->seq_id, 'filename' => Str::slug($article->title)])
                                    : route('journal.article.download', ['journal' => $journal->slug, 'article' => $article->seq_id, 'galley' => $galley->id]);
                            @endphp
                            <a href="{{ $downloadRoute }}"
                                class="flex items-center justify-center w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-4 rounded transition shadow-sm gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                {{ $galley->label ?? 'PDF' }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic text-center py-4">
                        <i class="fa-solid fa-file-circle-xmark mr-1"></i>
                        No files available.
                    </p>
                @endif
            </div>

            {{-- ISSUE INFORMATION --}}
            @if ($issue)
                <div class="bg-slate-50 p-5 rounded border border-slate-200">
                    <h4 class="font-bold text-slate-700 text-xs uppercase mb-3 tracking-wider">Issue</h4>
                    <a href="{{ route('journal.public.issue', [$journal->slug, $issue->seq_id]) }}"
                        class="block hover:text-primary-600 transition">
                        <p class="font-semibold text-slate-800">
                            Vol. {{ $issue->volume }} No. {{ $issue->number }} ({{ $issue->year }})
                        </p>
                        @if ($issue->title)
                            <p class="text-sm text-slate-500 mt-1">{{ $issue->title }}</p>
                        @endif
                    </a>
                </div>
            @endif

            {{-- HOW TO CITE (OJS 3.3 STYLE) --}}
            @php
                $year = $issue?->year ?? ($publicationDate?->year ?? now()->year);

                /* ========= AUTHOR FORMAT ========= */

                // Helper to title case names (fixes ALL CAPS inputs)
                $toTitleCase = function ($str) {
                    return mb_convert_case(trim($str ?? ''), MB_CASE_TITLE, 'UTF-8');
                };

                // APA: Last, F.
                $authorsAPA =
                    $article->authors
                        ?->map(function ($author) use ($toTitleCase) {
                            $last = $toTitleCase($author->last_name);
                            $first = $toTitleCase($author->first_name);
                            return $last ? $last . ', ' . mb_substr($first, 0, 1) . '.' : $first;
                        })
                        ->implode(', ') ?? 'Author';

                // Full: First Last
                $authorsFull =
                    $article->authors
                        ?->map(function ($author) use ($toTitleCase) {
                            $first = $toTitleCase($author->first_name);
                            $last = $toTitleCase($author->last_name);
                            return trim($first . ' ' . $last);
                        })
                        ->implode(', ') ?? 'Author';

                // IEEE/Vancouver: F. Last
                $authorsIEEE =
                    $article->authors
                        ?->map(function ($author) use ($toTitleCase) {
                            $first = $toTitleCase($author->first_name);
                            $last = $toTitleCase($author->last_name);
                            return ($first ? mb_substr($first, 0, 1) . '. ' : '') . $last;
                        })
                        ->implode(', ') ?? 'Author';

                $journalName = $journal->name ?? '';
                $volume = $issue?->volume;
                $number = $issue?->number;
                $pages = $article->currentPublication?->pages ?? '';
                $doiUrl = $article->currentPublication?->doi
                    ? 'https://doi.org/' . $article->currentPublication->doi
                    : '';

                /* ========= CITATION FORMATS ========= */

                $citations = [
                    'APA' =>
                        "{$authorsAPA} ({$year}). {$article->title}. <em>{$journalName}</em>" .
                        ($volume ? ", {$volume}" : '') .
                        ($number ? "({$number})" : '') .
                        ($pages ? ", {$pages}" : '') .
                        ". {$doiUrl}",
                    'ACM' =>
                        "{$authorsFull}. {$year}. {$article->title}. <em>{$journalName}</em>" .
                        ($volume ? ", {$volume}" : '') .
                        ($number ? ", {$number}" : '') .
                        ($pages ? ", {$pages}" : '') .
                        ". DOI: {$doiUrl}",

                    'ACS' =>
                        "{$authorsFull}. {$article->title}. <em>{$journalName}</em> {$year}" .
                        ($volume ? ", {$volume}" : '') .
                        ($number ? "({$number})" : '') .
                        ($pages ? ", {$pages}" : '') .
                        ". {$doiUrl}",                   
                    'ABNT' =>
                        mb_strtoupper($authorsFull) .
                        ". {$article->title}. {$journalName}, {$year}." .
                        ($volume ? " v. {$volume}" : '') .
                        ($number ? ", n. {$number}" : '') .
                        ($pages ? ", p. {$pages}" : '') .
                        ". Disponível em: {$doiUrl}",

                    'Chicago' =>
                        "{$authorsFull}. {$year}. \"{$article->title}.\" <em>{$journalName}</em>" .
                        ($volume ? " {$volume}" : '') .
                        ($number ? ", no. {$number}" : '') .
                        ($pages ? ": {$pages}" : '') .
                        ". {$doiUrl}",

                    'Harvard' =>
                        "{$authorsFull} ({$year}) '{$article->title}', <em>{$journalName}</em>" .
                        ($volume ? ", vol. {$volume}" : '') .
                        ($number ? ", no. {$number}" : '') .
                        ($pages ? ", pp. {$pages}" : '') .
                        ". Available at: {$doiUrl}",

                    'IEEE' =>
                        "{$authorsIEEE}, \"{$article->title},\" <em>{$journalName}</em>" .
                        ($volume ? ", vol. {$volume}" : '') .
                        ($number ? ", no. {$number}" : '') .
                        ($pages ? ", pp. {$pages}" : '') .
                        ", {$year}. {$doiUrl}",

                    'MLA' =>
                        "{$authorsFull}. \"{$article->title}.\" <em>{$journalName}</em>" .
                        ($volume ? ", vol. {$volume}" : '') .
                        ($number ? ", no. {$number}" : '') .
                        ", {$year}" .
                        ($pages ? ", pp. {$pages}" : '') .
                        ". {$doiUrl}",

                    'Turabian' =>
                        "{$authorsFull}. \"{$article->title}.\" {$journalName}" .
                        ($volume ? " {$volume}" : '') .
                        ($number ? ", no. {$number}" : '') .
                        " ({$year})" .
                        ($pages ? ": {$pages}" : '') .
                        ". {$doiUrl}",

                    'Vancouver' =>
                        "{$authorsIEEE}. {$article->title}. {$journalName}. {$year}" .
                        ($volume ? ";{$volume}" : '') .
                        ($number ? "({$number})" : '') .
                        ($pages ? ":{$pages}" : '') .
                        ". {$doiUrl}",
                ];
            @endphp

            <div x-data="{ format: 'APA', citations: @js($citations) }" class="bg-slate-50 p-5 rounded border border-slate-200">
                <h4 class="font-bold text-slate-700 text-xs uppercase mb-3 tracking-wider">
                    More Citation Formats
                </h4>

                <select x-model="format" class="w-full mb-3 border border-slate-300 rounded text-xs p-2 bg-white">
                    <template x-for="(v, k) in citations" :key="k">
                        <option x-text="k"></option>
                    </template>
                </select>

                <div class="bg-white p-3 border rounded text-xs italic font-mono leading-relaxed break-words"
                    x-html="citations[format]"></div>

                <button
                    @click="
                        navigator.clipboard.writeText(
                            document.querySelector('[x-html]').innerText
                        );
                        $el.innerText='Copied';
                        setTimeout(()=> $el.innerText='Copy Citation',2000);
                    "
                    class="mt-3 text-sm text-primary-600 hover:underline">
                    Copy Citation
                </button>

                {{-- DOWNLOAD --}}
                <div class="mt-5 border-t pt-3 text-xs text-slate-600">
                    <p class="font-semibold mb-2">Download Citation</p>
                    <a href="{{ route('citation.ris', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}"
                        class="block hover:underline">
                        ⬇ EndNote / Zotero / Mendeley (RIS)
                    </a>
                    <a href="{{ route('citation.bibtex', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}"
                        class="block hover:underline">
                        ⬇ BibTeX
                    </a>
                </div>
            </div>

            {{-- ARTICLE METADATA --}}
            <div class="bg-slate-50 p-5 rounded border border-slate-200">
                <h4 class="font-bold text-slate-700 text-xs uppercase mb-3 tracking-wider">Article Info</h4>
                <dl class="space-y-3 text-sm">
                    @if ($publicationDate)
                        <div>
                            <dt class="text-xs font-bold text-slate-400 uppercase">Published</dt>
                            <dd class="text-slate-800 mt-1">{{ $publicationDate->format('Y-m-d') }}</dd>
                        </div>
                    @endif
                    @if ($article->section)
                        <div>
                            <dt class="text-xs font-bold text-slate-400 uppercase">Section</dt>
                            <dd class="text-slate-800 mt-1">{{ $article->section->name }}</dd>
                        </div>
                    @endif
                    @if ($article->pages)
                        <div>
                            <dt class="text-xs font-bold text-slate-400 uppercase">Pages</dt>
                            <dd class="text-slate-800 mt-1">{{ $article->pages }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- LICENSE --}}
            @php
                $license = $article->license ?? ($journal->default_license ?? 'CC BY 4.0');
            @endphp
            <div class="bg-slate-50 p-5 rounded border border-slate-200">
                <h4 class="font-bold text-slate-700 text-xs uppercase mb-3 tracking-wider">License</h4>
                <img src="https://licensebuttons.net/l/by/4.0/88x31.png" alt="Creative Commons License"
                    class="mb-2">
                <p class="text-xs text-slate-500 leading-relaxed">
                    This work is licensed under a
                    <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank"
                        class="text-blue-600 hover:underline">
                        Creative Commons Attribution 4.0 International License
                    </a>.
                </p>
            </div>

            {{-- SHARE BUTTONS --}}
            <div class="bg-slate-50 p-5 rounded border border-slate-200">
                <h4 class="font-bold text-slate-700 text-xs uppercase mb-3 tracking-wider">Share</h4>
                <div class="flex gap-2">
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}"
                        target="_blank" rel="noopener"
                        class="flex-1 flex items-center justify-center py-2 rounded-lg bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                        target="_blank" rel="noopener"
                        class="flex-1 flex items-center justify-center py-2 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($article->title) }}"
                        target="_blank" rel="noopener"
                        class="flex-1 flex items-center justify-center py-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' ' . url()->current()) }}"
                        target="_blank" rel="noopener"
                        class="flex-1 flex items-center justify-center py-2 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 transition-colors">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>

        </aside>
    </div>

    {{-- ============================================ --}}
    {{-- CHART.JS INITIALIZATION SCRIPT --}}
    {{-- ============================================ --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('statsChart');
                if (!ctx) return;

                const chartLabels = @json($chartLabels ?? []);
                const viewsData = @json($viewsData ?? []);
                const downloadsData = @json($downloadsData ?? []);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                                label: 'Views',
                                data: viewsData,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: 'rgb(59, 130, 246)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            },
                            {
                                label: 'Downloads',
                                data: downloadsData,
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: 'rgb(34, 197, 94)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y
                                            .toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

</x-layouts.public>
