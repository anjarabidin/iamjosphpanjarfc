@php $title = $submission->title; @endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">

    <article class="bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Breadcrumb -->
            <nav class="text-sm text-gray-500 mb-6">
                <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}"
                    class="hover:text-primary-600">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('journal.public.archives', ['journal' => $journal->slug]) }}"
                    class="hover:text-primary-600">Archives</a>
                @if ($submission->issue)
                    <span class="mx-2">/</span>
                    <a href="{{ route('journal.public.issue', ['journal' => $journal->slug, 'issue' => $submission->issue->seq_id]) }}"
                        class="hover:text-primary-600">{{ $submission->issue->identifier }}</a>
                @endif
            </nav>

            <!-- Article Header -->
            <header class="mb-8">
                @if ($submission->section)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800 mb-4">
                        {{ $submission->section->name }}
                    </span>
                @endif

                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-6">
                    {{ $submission->title }}
                </h1>

                <!-- Authors -->
                <div class="mb-6">
                    <h2 class="sr-only">Authors</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($submission->authors as $author)
                            <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2">
                                <div
                                    class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-semibold text-sm mr-2">
                                    {{ strtoupper(substr($author->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $author->name }}
                                        @if ($author->is_corresponding)
                                            <span class="ml-1 text-xs text-primary-600"
                                                title="Corresponding Author">✉</span>
                                        @endif
                                    </p>
                                    @if ($author->affiliation)
                                        <p class="text-xs text-gray-500">{{ $author->affiliation }}</p>
                                    @endif
                                </div>
                                @if ($author->orcid_url)
                                    <a href="{{ $author->orcid_url }}" target="_blank"
                                        class="ml-2 text-green-600 hover:text-green-700" title="ORCID">
                                        <svg class="w-4 h-4" viewBox="0 0 256 256">
                                            <path fill="currentColor"
                                                d="M128,0C57.42,0,0,57.42,0,128s57.42,128,128,128s128-57.42,128-128S198.58,0,128,0z M86.09,210.82h-18V86.18h18V210.82z M77.09,73.82c-6.62,0-12-5.38-12-12s5.38-12,12-12s12,5.38,12,12S83.71,73.82,77.09,73.82z M193.91,210.82h-50.73v-60.27c0-16.09-5.78-27.09-20.18-27.09c-11,0-17.55,7.42-20.41,14.59c-1.05,2.56-1.31,6.14-1.31,9.73v63.04H83.27c0.24-102.18,0-112.82,0-112.82h18.01v16.26h-0.24c2.39-3.69,6.67-8.95,16.22-8.95c11.84,0,20.72,7.73,20.72,24.35v81.16h0.01z" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 pb-6 border-b border-gray-200">
                    <span>Published: {{ $submission->published_at?->format('F j, Y') }}</span>
                    @if ($submission->issue)
                        <span>•</span>
                        <span>{{ $submission->issue->identifier }}</span>
                    @endif
                </div>
            </header>

            <!-- Downloads / Read Full Text -->
            @if ($submission->files->isNotEmpty())
                <div
                    class="bg-gradient-to-r from-primary-50 to-primary-100 rounded-xl p-6 mb-8 border border-primary-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Full Text Available</h3>
                            <p class="text-sm text-gray-600">Read or download the article in PDF format</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <!-- Read Full Text Button -->
                            <a href="{{ route('journal.public.article.reader', ['journal' => $journal->slug, 'submission' => $submission]) }}"
                                class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Read Full Text
                            </a>

                            <!-- Download Buttons -->
                            @foreach ($submission->files as $file)
                                <a href="{{ route('files.download', $file) }}"
                                    class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M14,2H6A2,2,0,0,0,4,4V20a2,2,0,0,0,2,2H18a2,2,0,0,0,2-2V8ZM12,18,8,14l1.41-1.41L11,14.17V10h2v4.17l1.59-1.58L16,14ZM13,9V3.5L18.5,9Z" />
                                    </svg>
                                    Download {{ strtoupper(pathinfo($file->file_name, PATHINFO_EXTENSION)) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Abstract -->
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Abstract</h2>
                <div class="prose prose-lg max-w-none text-gray-600">
                    {!! nl2br(e($submission->abstract)) !!}
                </div>
            </section>

            <!-- Keywords -->
            @if ($submission->keywords)
                <section class="mb-8">
                    <h2 class="text-sm font-semibold text-gray-900 mb-3">Keywords</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($submission->keywords_array as $keyword)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                                {{ $keyword }}
                            </span>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Citation -->
            <section class="bg-gray-50 rounded-xl p-6 mb-8">
                <h2 class="text-sm font-semibold text-gray-900 mb-3">How to Cite</h2>
                <p class="text-sm text-gray-600">
                    {{ $submission->authors->pluck('name')->join(', ') }} ({{ $submission->published_at?->year }}).
                    {{ $submission->title }}.
                    <em>{{ $submission->journal->name }}</em>,
                    @if ($submission->issue)
                        {{ $submission->issue->volume }}({{ $submission->issue->number }}).
                    @endif
                </p>
            </section>
        </div>
    </article>

    <!-- Related Articles -->
    @if ($relatedArticles->isNotEmpty())
        <section class="bg-gray-100 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Related Articles</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach ($relatedArticles as $related)
                        <article class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow">
                            <h3 class="font-medium text-gray-900 line-clamp-2 hover:text-primary-600">
                                <a
                                    href="{{ route('journal.public.article', ['journal' => $journal->slug, 'submission' => $related]) }}">{{ $related->title }}</a>
                            </h3>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ $related->authors->pluck('name')->first() ?: 'Unknown Author' }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.public>
