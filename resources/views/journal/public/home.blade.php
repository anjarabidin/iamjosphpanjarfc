{{-- Journal Landing Page (OJS 3.3 Modern Style) --}}
@php
    $primaryColor = $settings['primary_color'] ?? '#0369a1';
    $secondaryColor = $settings['secondary_color'] ?? '#7c3aed';

    // OJS 3.3 Logic: Show homepage image in body only if NOT used as header background
    $showHomepageImageInBody = $journal->homepage_image_path && !$journal->show_homepage_image_in_header;
@endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$journal->name . ' - Home'">
    {{-- Announcement Banner (if any urgent announcements) --}}
    @if ($announcements->where('is_urgent', true)->isNotEmpty())
        @php $urgentAnnouncement = $announcements->where('is_urgent', true)->first(); @endphp
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-bullhorn text-amber-500 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-semibold text-amber-800">{{ $urgentAnnouncement->title }}</p>
                    <p class="text-sm text-amber-700 mt-1">
                        {{ Str::limit($urgentAnnouncement->excerpt ?? $urgentAnnouncement->content, 150) }}</p>
                </div>
                <a href="#announcements" class="text-sm text-amber-600 hover:underline">View all</a>
            </div>
        </div>
    @endif

    {{-- Homepage Image (OJS 3.3: Show in body when NOT used as header background) --}}
    @if ($showHomepageImageInBody)
        <div class="mb-8">
            <img src="{{ Storage::url($journal->homepage_image_path) }}" alt="{{ $journal->name }}"
                class="w-full h-auto rounded-lg shadow-md">
        </div>
    @endif

    {{-- MAIN CONTENT (Full Width) --}}
    <div>
        <div class="w-full">

            {{-- Journal Summary Section --}}
            @if ($journal->show_summary && !empty($journal->summary))
                <section class="mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center">
                        <div class="prose prose-lg prose-slate mx-auto text-slate-600 leading-relaxed">
                            {!! clean($journal->summary) !!}
                        </div>
                    </div>
                </section>
            @endif

            {{-- Current Issue Section (OJS 3.3 Style) --}}
            @if ($currentIssue)
                <section class="mb-8">
                    {{-- Section Header (OJS Style) --}}
                    <div class="mb-8">
                        <h2
                            class="text-xl font-bold text-slate-700 uppercase tracking-wide inline-block border-b-4 border-orange-400 pb-1">
                            Current Issue
                        </h2>
                    </div>

                    {{-- Issue Info Block (Cover + Description) --}}
                    <div
                        class="flex flex-col md:flex-row gap-6 mb-10 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        {{-- Cover Image (Fixed Size) --}}
                        <div class="flex-shrink-0">
                            @if ($currentIssue->cover_path)
                                <a href="{{ route('journal.public.current', $journal->slug) }}">
                                    <img src="{{ Storage::url($currentIssue->cover_path) }}"
                                        alt="Cover Vol {{ $currentIssue->volume }}"
                                        class="w-48 h-auto rounded shadow-lg border border-slate-200 hover:shadow-xl transition-shadow mx-auto md:mx-0">
                                </a>
                            @else
                                <div
                                    class="w-48 h-64 bg-slate-100 flex items-center justify-center text-slate-400 rounded border border-slate-200 mx-auto md:mx-0">
                                    <i class="fa-regular fa-image text-4xl"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Issue Details --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-2xl font-bold text-slate-900 mb-3 leading-tight">
                                Vol. {{ $currentIssue->volume }} No. {{ $currentIssue->number }}
                                ({{ $currentIssue->year }})@if ($currentIssue->title)
                                    : {{ $currentIssue->title }}
                                @endif
                            </h3>

                            {{-- Description (HTML Rendered) --}}
                            @if ($currentIssue->description)
                                <div class="prose prose-sm prose-slate max-w-none mb-4">
                                    {!! clean($currentIssue->description) !!}
                                </div>
                            @endif

                            {{-- Metadata --}}
                            <div class="space-y-2 text-sm text-slate-700 mt-4">
                                @if ($currentIssue->published_at)
                                    <p>
                                        <span class="font-bold text-slate-900">PUBLISHED:</span>
                                        {{ $currentIssue->published_at->format('Y-m-d') }}
                                    </p>
                                @endif

                                @if ($currentIssue->metadata['doi'] ?? false)
                                    <p>
                                        <span class="font-bold text-slate-900">DOI:</span>
                                        <a href="https://doi.org/{{ $currentIssue->metadata['doi'] }}"
                                            class="text-blue-600 hover:underline break-all" target="_blank">
                                            https://doi.org/{{ $currentIssue->metadata['doi'] }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Articles List (Table of Contents) --}}
                    @php
                        $issueArticles = $currentIssue
                            ->submissions()
                            ->where('status', 'published')
                            ->with(['authors', 'galleys', 'currentPublication'])
                            ->orderBy('created_at')
                            ->get();
                    @endphp

                    @if ($issueArticles->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-xl font-bold text-slate-700 border-b border-slate-200 pb-2 mb-6">Articles
                            </h3>

                            <div class="space-y-8">
                                @foreach ($issueArticles as $article)
                                    <div
                                        class="flex flex-col md:flex-row gap-4 border-b border-slate-100 pb-6 last:border-0">

                                        {{-- MAIN CONTENT (Left) --}}
                                        <div class="flex-1 min-w-0">

                                            {{-- 1. TITLE (Fix: Use Slug) --}}
                                            <h4 class="text-lg font-bold text-blue-700 leading-tight mb-1">
                                                <a href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}"
                                                    class="hover:underline">
                                                    {{ $article->title }}
                                                </a>
                                            </h4>

                                            {{-- 2. SUBTITLE (If exists) --}}
                                            @if (!empty($article->subtitle))
                                                <div class="text-sm text-slate-600 font-medium mb-2">
                                                    {{ $article->subtitle }}
                                                </div>
                                            @endif

                                            {{-- 3. AUTHORS (Detailed List) --}}
                                            <div class="mt-2 mb-3 space-y-1">
                                                @if ($article->authors->isNotEmpty())
                                                    @foreach ($article->authors as $author)
                                                        <div class="text-sm text-slate-700">
                                                            <span class="font-bold">{{ $author->first_name }}
                                                                {{ $author->last_name }}</span>
                                                            @if ($author->affiliation)
                                                                <span class="text-slate-500">,
                                                                    {{ $author->affiliation }}</span>
                                                            @endif
                                                            @if ($author->country)
                                                                <span class="text-slate-500">,
                                                                    {{ $author->country }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-sm text-slate-500 italic">No authors listed</div>
                                                @endif
                                            </div>

                                            {{-- 4. DOI --}}
                                            @if ($article->currentPublication?->doi)
                                                <div class="flex items-center gap-2 mb-3 mt-3">
                                                    {{-- OJS DOI Badge Style --}}
                                                    <span
                                                        class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 uppercase">
                                                        DOI
                                                    </span>
                                                    <a href="https://doi.org/{{ $article->currentPublication->doi }}"
                                                        class="text-sm text-blue-600 hover:underline underline-offset-2 break-all"
                                                        target="_blank">
                                                        https://doi.org/{{ $article->currentPublication->doi }}
                                                    </a>
                                                </div>
                                            @endif

                                            {{-- 5. ACTION BAR (Galleys + Metrics) --}}
                                            <div class="flex flex-wrap items-center gap-4 mt-4">
                                                {{-- Galley Buttons --}}
                                                @if ($article->galleys->isNotEmpty())
                                                    @foreach ($article->galleys as $galley)
                                                        <a href="{{ route('journal.article.download', ['journal' => $journal->slug, 'article' => $article->slug ?? $article->id, 'galley' => $galley->id]) }}"
                                                            class="inline-flex items-center px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold rounded shadow-sm transition group">
                                                            <svg class="w-4 h-4 mr-1.5 opacity-80 group-hover:opacity-100"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                            {{ $galley->label ?? 'PDF' }}
                                                        </a>
                                                    @endforeach
                                                @endif

                                                {{-- Metrics --}}
                                                <div
                                                    class="flex items-center gap-3 text-xs text-slate-500 border-l border-slate-200 pl-4">
                                                    <span class="flex items-center gap-1" title="Abstract Views">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                        {{ $article->views_count ?? 0 }} Views
                                                    </span>
                                                    <span class="flex items-center gap-1" title="File Downloads">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none"
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
                                        @php
                                            $pages = $article->currentPublication->pages ?? $article->pages;
                                        @endphp
                                        @if ($pages)
                                            <div class="text-sm text-slate-500 font-mono whitespace-nowrap pt-1">
                                                {{ $pages }}
                                            </div>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-lg p-8 text-center">
                            <i class="fa-regular fa-folder-open text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500">No articles published in this issue yet.</p>
                        </div>
                    @endif
                </section>
            @else
                {{-- No Current Issue --}}
                <section class="mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                            style="background: {{ $primaryColor }}15;">
                            <i class="fa-solid fa-book text-2xl" style="color: {{ $primaryColor }};"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Welcome to {{ $journal->name }}</h3>
                        <p class="text-slate-600 mb-6 max-w-md mx-auto">
                            {{ $journal->description ?? 'A peer-reviewed scholarly journal dedicated to advancing knowledge and research.' }}
                        </p>
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="{{ route('journal.submissions.create', $journal->slug) }}"
                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white rounded-lg transition-all"
                                style="background: {{ $primaryColor }};">
                                <i class="fa-solid fa-paper-plane mr-2"></i>
                                Submit Your Research
                            </a>
                            <a href="{{ route('journal.public.about', $journal->slug) }}"
                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium border rounded-lg transition-colors hover:bg-slate-50"
                                style="color: {{ $primaryColor }}; border-color: {{ $primaryColor }};">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Learn More
                            </a>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Announcements Section --}}
            @if ($announcements->isNotEmpty())
                <section id="announcements" class="mb-8">
                    <div class="flex items-center justify-between mb-6 pb-3 border-b-2"
                        style="border-color: {{ $primaryColor }};">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-bullhorn text-lg" style="color: {{ $primaryColor }};"></i>
                            Announcements
                        </h2>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($announcements as $announcement)
                            <div
                                class="bg-white rounded-lg shadow-sm border {{ $announcement->is_urgent ? 'border-amber-300 bg-amber-50/50' : 'border-slate-200' }} p-5 card-hover">
                                @if ($announcement->is_urgent)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-amber-700 bg-amber-100 rounded-full mb-2">
                                        <i class="fa-solid fa-star mr-1"></i> Important
                                    </span>
                                @endif
                                <p class="text-xs text-slate-500 mb-2">
                                    {{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y') }}
                                </p>
                                <h3 class="text-base font-semibold text-slate-900 mb-2 line-clamp-2">
                                    {{ $announcement->title }}
                                </h3>
                                <p class="text-sm text-slate-600 line-clamp-3">
                                    {{ Str::limit($announcement->excerpt ?? strip_tags($announcement->content ?? ''), 120) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </div> {{-- End Main Content --}}

    </div> {{-- End Content Container --}}


    {{-- ADDITIONAL CONTENT (Indexed By, Sponsors, Partners) --}}
    @if (!empty($journal->additional_content))
        <section class="mt-12 pt-8 border-t border-slate-200">
            <div class="prose prose-slate max-w-none text-slate-600 text-center">
                {!! clean($journal->additional_content) !!}
            </div>
        </section>
    @endif
</x-layouts.public>
