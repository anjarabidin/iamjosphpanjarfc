{{-- Latest Articles Block - Cross-Journal Feed --}}
@props(['block', 'data' => []])

@php
    $config = $block->config ?? [];
    $title = $config['title'] ?? 'Latest Publications';
    $subtitle = $config['subtitle'] ?? 'Recently published research articles';
    $layout = $config['layout'] ?? 'cards';
    $columns = $config['columns'] ?? 3;
    $limit = $config['limit'] ?? 6;
    $showAbstract = $config['show_abstract'] ?? true;
    $showAuthors = $config['show_authors'] ?? true;
    $showJournal = $config['show_journal'] ?? true;
    $abstractLength = $config['abstract_length'] ?? 150;

    $articles = $data['latest_articles'] ?? collect();
@endphp

<section class="py-16 md:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-12">
            <div>
                <span
                    class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-600 bg-green-100 rounded-full mb-4">
                    <i class="fa-solid fa-clock mr-2"></i>
                    Just Published
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    {{ $title }}
                </h2>
                <p class="text-lg text-gray-600">
                    {{ $subtitle }}
                </p>
            </div>
            <a href="{{ route('portal.search', ['sort' => 'newest']) }}"
                class="mt-4 md:mt-0 inline-flex items-center text-blue-600 font-semibold hover:text-blue-700">
                View All
                <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>

        {{-- Articles Grid --}}
        @if ($articles->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $columns }} gap-6">
                @foreach ($articles->take($limit) as $article)
                    <article
                        class="bg-white rounded-xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 group">
                        {{-- Article Header --}}
                        <div class="p-6 pb-4">
                            {{-- Journal Badge --}}
                            @if ($showJournal && $article->journal)
                                <a href="{{ route('journal.public.home', $article->journal->slug) }}"
                                    class="inline-flex items-center text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full hover:bg-blue-100 transition-colors mb-3">
                                    @if ($article->journal->abbreviation)
                                        {{ $article->journal->abbreviation }}
                                    @else
                                        {{ Str::limit($article->journal->name, 20) }}
                                    @endif
                                </a>
                            @endif

                            {{-- Title --}}
                            <h3
                                class="font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                <a
                                    href="{{ route('journal.public.article', [$article->journal->slug ?? 'journal', $article->seq_id]) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            {{-- Authors --}}
                            @if ($showAuthors && $article->authors && $article->authors->isNotEmpty())
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                                    <i class="fa-regular fa-user"></i>
                                    <span class="line-clamp-1">
                                        {{ $article->authors->pluck('name')->take(3)->join(', ') }}
                                        @if ($article->authors->count() > 3)
                                            <span class="text-gray-400">+{{ $article->authors->count() - 3 }}
                                                more</span>
                                        @endif
                                    </span>
                                </div>
                            @endif

                            {{-- Abstract --}}
                            @if ($showAbstract && $article->abstract)
                                <p class="text-sm text-gray-600 line-clamp-3">
                                    {{ Str::limit(strip_tags($article->abstract), $abstractLength) }}
                                </p>
                            @endif
                        </div>

                        {{-- Article Footer --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                {{-- Date --}}
                                @if ($article->published_at || $article->issue?->published_at)
                                    <span class="flex items-center gap-1">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ ($article->published_at ?? $article->issue->published_at)->format('M d, Y') }}
                                    </span>
                                @endif

                                {{-- DOI Badge --}}
                                @if ($article->doi)
                                    <span class="flex items-center gap-1 text-blue-600">
                                        <i class="fa-solid fa-link"></i>
                                        DOI
                                    </span>
                                @endif
                            </div>

                            {{-- Read More --}}
                            <a href="{{ route('journal.public.article', [$article->journal->slug ?? 'journal', $article->seq_id]) }}"
                                class="text-xs font-medium text-blue-600 hover:text-blue-700">
                                Read More →
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <i class="fa-solid fa-file-lines text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No articles published yet.</p>
            </div>
        @endif
    </div>
</section>
