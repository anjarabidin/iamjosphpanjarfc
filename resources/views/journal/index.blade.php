{{-- Journal Landing Page (OJS 3.3 Modern Style) --}}
@php
$primaryColor = $settings['primary_color'] ?? '#0369a1';
$secondaryColor = $settings['secondary_color'] ?? '#7c3aed';
@endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$journal->name . ' - Home'">
    {{-- Announcement Banner (if any urgent announcements) --}}
    @if($announcements->where('is_urgent', true)->isNotEmpty())
        @php $urgentAnnouncement = $announcements->where('is_urgent', true)->first(); @endphp
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-bullhorn text-amber-500 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-semibold text-amber-800">{{ $urgentAnnouncement->title }}</p>
                    <p class="text-sm text-amber-700 mt-1">{{ Str::limit($urgentAnnouncement->excerpt ?? $urgentAnnouncement->content, 150) }}</p>
                </div>
                <a href="#announcements" class="text-sm text-amber-600 hover:underline">View all</a>
            </div>
        </div>
    @endif

    {{-- Current Issue Section --}}
    @if($currentIssue)
        <section class="mb-8">
            {{-- Section Header --}}
            <div class="flex items-center justify-between mb-6 pb-3 border-b-2" style="border-color: {{ $primaryColor }};">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-book-open text-lg" style="color: {{ $primaryColor }};"></i>
                    Current Issue
                </h2>
                <a href="{{ route('journal.public.current', $journal->slug) }}" 
                   class="text-sm font-medium hover:underline" style="color: {{ $primaryColor }};">
                    View All <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            {{-- Issue Header Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                <div class="flex flex-col md:flex-row gap-6">
                    {{-- Issue Cover (Left) --}}
                    @if($currentIssue->cover_path)
                        <div class="flex-shrink-0">
                            <a href="{{ route('journal.public.current', $journal->slug) }}" class="block">
                                <img src="{{ Storage::url($currentIssue->cover_path) }}" 
                                     alt="{{ $currentIssue->display_title }}"
                                     class="w-48 h-auto rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                            </a>
                        </div>
                    @endif

                    {{-- Issue Details (Right) --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-2xl font-bold text-slate-900 mb-1">
                                    {{ $currentIssue->display_title }}
                                </h3>
                                <p class="text-sm text-slate-500">
                                    Vol. {{ $currentIssue->volume }} No. {{ $currentIssue->number }} ({{ $currentIssue->year }})
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold text-white rounded-full" style="background: {{ $primaryColor }};">
                                Latest
                            </span>
                        </div>

                        @if($currentIssue->published_at)
                            <p class="text-sm text-slate-600 mb-3">
                                <i class="fa-regular fa-calendar mr-1"></i>
                                Published: {{ $currentIssue->published_at->format('F d, Y') }}
                            </p>
                        @endif

                        @if($currentIssue->metadata['description'] ?? false)
                            <p class="text-sm text-slate-600 mb-4 line-clamp-3">
                                {{ $currentIssue->metadata['description'] }}
                            </p>
                        @endif

                        {{-- Issue Stats --}}
                        @php
                            $issueArticles = $currentIssue->submissions()->where('status', 'published')->get();
                            $articleCount = $issueArticles->count();
                        @endphp
                        <div class="flex items-center gap-4 text-sm">
                            <span class="flex items-center gap-1 text-slate-600">
                                <i class="fa-regular fa-file-lines text-slate-400"></i>
                                {{ $articleCount }} {{ Str::plural('Article', $articleCount) }}
                            </span>
                            @if($currentIssue->metadata['doi'] ?? false)
                                <a href="https://doi.org/{{ $currentIssue->metadata['doi'] }}" 
                                   target="_blank" 
                                   class="flex items-center gap-1 text-blue-600 hover:underline">
                                    <i class="fa-solid fa-link"></i>
                                    DOI
                                </a>
                            @endif
                        </div>

                        {{-- Quick Actions --}}
                        <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-slate-100">
                            <a href="{{ route('journal.public.current', $journal->slug) }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all hover:shadow-md"
                               style="background: {{ $primaryColor }};">
                                <i class="fa-solid fa-book-open mr-2"></i>
                                Browse Issue
                            </a>
                            @if($currentIssue->cover_path || $currentIssue->metadata['pdf_path'] ?? false)
                                <a href="#" 
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium border rounded-lg transition-colors hover:bg-slate-50"
                                   style="color: {{ $primaryColor }}; border-color: {{ $primaryColor }};">
                                    <i class="fa-solid fa-download mr-2"></i>
                                    Full Issue PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Articles Grid (Grouped by Section) --}}
            @php
                $sections = $journal->sections()->orderBy('sort_order')->get();
                $groupedArticles = $issueArticles->groupBy('section_id');
            @endphp

            @foreach($sections as $section)
                @if(isset($groupedArticles[$section->id]) && $groupedArticles[$section->id]->isNotEmpty())
                    <div class="mb-8">
                        {{-- Section Title --}}
                        <h4 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" style="background: {{ $primaryColor }};"></span>
                            {{ $section->name }}
                        </h4>

                        {{-- Articles List --}}
                        <div class="space-y-4">
                            @foreach($groupedArticles[$section->id] as $article)
                                <x-article-card :article="$article" :journal="$journal" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Articles without Section --}}
            @if(isset($groupedArticles[null]) || isset($groupedArticles[""]) || isset($groupedArticles[0]))
                @php
                    $uncategorizedArticles = $groupedArticles[null] ?? $groupedArticles[""] ?? $groupedArticles[0] ?? collect();
                @endphp
                @if($uncategorizedArticles->isNotEmpty())
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            Articles
                        </h4>
                        <div class="space-y-4">
                            @foreach($uncategorizedArticles as $article)
                                <x-article-card :article="$article" :journal="$journal" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            {{-- No Articles Message --}}
            @if($issueArticles->isEmpty())
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
    @if($announcements->isNotEmpty())
        <section id="announcements" class="mb-8">
            <div class="flex items-center justify-between mb-6 pb-3 border-b-2" style="border-color: {{ $primaryColor }};">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-bullhorn text-lg" style="color: {{ $primaryColor }};"></i>
                    Announcements
                </h2>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($announcements as $announcement)
                    <div class="bg-white rounded-lg shadow-sm border {{ $announcement->is_urgent ? 'border-amber-300 bg-amber-50/50' : 'border-slate-200' }} p-5 card-hover">
                        @if($announcement->is_urgent)
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-amber-700 bg-amber-100 rounded-full mb-2">
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

    {{-- Editorial Team Preview --}}
    @if($editorialTeam->isNotEmpty())
        <section class="mb-8">
            <div class="flex items-center justify-between mb-6 pb-3 border-b-2" style="border-color: {{ $primaryColor }};">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-users text-lg" style="color: {{ $primaryColor }};"></i>
                    Editorial Board
                </h2>
                <a href="{{ route('journal.public.editorial-team', $journal->slug) }}" 
                   class="text-sm font-medium hover:underline" style="color: {{ $primaryColor }};">
                    View Full Board <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($editorialTeam->take(6) as $editor)
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 flex items-center gap-4 card-hover">
                        {{-- Avatar --}}
                        @if($editor->avatar ?? false)
                            <img src="{{ Storage::url($editor->avatar) }}" 
                                 alt="{{ $editor->name }}"
                                 class="w-14 h-14 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                                 style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                {{ strtoupper(substr($editor->name, 0, 1)) }}
                            </div>
                        @endif
                        
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900 truncate">{{ $editor->name }}</h3>
                            <p class="text-sm font-medium truncate" style="color: {{ $primaryColor }};">
                                {{ $editor->role }}
                            </p>
                            @if($editor->affiliation ?? false)
                                <p class="text-xs text-slate-500 truncate">{{ $editor->affiliation }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Indexed In Section --}}
    @if(!empty($settings['show_indexed_in']) && count($indexedInImages ?? []) > 0)
        <section class="mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider text-center mb-6">
                    Indexed & Abstracted In
                </h3>
                <div class="flex flex-wrap justify-center items-center gap-6 md:gap-10">
                    @foreach($indexedInImages as $image)
                        <img src="{{ Storage::url($image) }}" 
                             alt="Indexer Logo"
                             class="h-10 md:h-12 w-auto grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition-all">
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Call to Action --}}
    <section class="mt-8">
        <div class="rounded-xl p-8 text-center relative overflow-hidden"
             style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
            {{-- Decorative Pattern --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                    <rect width="100%" height="100%" fill="url(#grid)"/>
                </svg>
            </div>
            
            <div class="relative">
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
                    Share Your Research With the World
                </h2>
                <p class="text-white/90 mb-6 max-w-xl mx-auto">
                    Submit your manuscript to {{ $journal->name }} and join our community of researchers and scholars.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('journal.submissions.create', $journal->slug) }}"
                       class="inline-flex items-center px-6 py-3 text-sm font-semibold bg-white rounded-lg shadow-lg hover:shadow-xl transition-all"
                       style="color: {{ $primaryColor }};">
                        <i class="fa-solid fa-paper-plane mr-2"></i>
                        Submit Manuscript
                    </a>
                    <a href="{{ route('journal.public.author-guidelines', $journal->slug) }}"
                       class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white border-2 border-white/30 rounded-lg hover:bg-white/10 transition-all">
                        <i class="fa-solid fa-book mr-2"></i>
                        Author Guidelines
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
