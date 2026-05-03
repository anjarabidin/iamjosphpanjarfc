@php $title = 'Home'; @endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-4xl lg:text-5xl font-bold leading-tight mb-6">
                        {{ $journal->name ?? 'Indonesian Academic Journal System' }}
                    </h1>
                    <p class="text-lg text-primary-100 mb-8">
                        {{ $journal->description ?? 'An open-access academic journal platform dedicated to advancing knowledge through rigorous peer review.' }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-6 py-3 bg-white text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Submit Manuscript
                        </a>
                        <a href="{{ route('journal.public.about', ['journal' => $journal->slug]) }}"
                            class="inline-flex items-center justify-center px-6 py-3 border-2 border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="absolute -top-4 -left-4 w-72 h-72 bg-primary-400/30 rounded-full blur-3xl"></div>
                        <div class="absolute -bottom-4 -right-4 w-72 h-72 bg-primary-800/50 rounded-full blur-3xl">
                        </div>
                        <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">Open Access</p>
                                        <p class="text-primary-200 text-sm">Free to read & download</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">Peer Reviewed</p>
                                        <p class="text-primary-200 text-sm">Rigorous review process</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">Fast Publication</p>
                                        <p class="text-primary-200 text-sm">Quick turnaround time</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-8 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                <div>
                    <p class="text-3xl font-bold text-primary-600">{{ \App\Models\Submission::published()->count() }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">Published Articles</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary-600">{{ \App\Models\Issue::published()->count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Issues</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary-600">{{ \App\Models\User::count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Authors</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary-600">{{ \App\Models\Section::active()->count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Sections</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Articles -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Latest Articles</h2>
                    <p class="text-gray-500 mt-1">Recently published research</p>
                </div>
                <a href="{{ route('journal.public.archives', ['journal' => $journal->slug]) }}"
                    class="text-sm font-medium text-primary-600 hover:text-primary-700">
                    View All →
                </a>
            </div>

            @if ($latestArticles->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No articles yet</h3>
                    <p class="text-gray-500">Published articles will appear here.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($latestArticles as $article)
                        <article
                            class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow overflow-hidden">
                            <div class="p-6">
                                @if ($article->section)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mb-3">
                                        {{ $article->section->name }}
                                    </span>
                                @endif
                                <h3
                                    class="text-lg font-semibold text-gray-900 mb-3 line-clamp-2 hover:text-primary-600">
                                    <a
                                        href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}">{{ $article->title }}</a>
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ $article->authors->pluck('name')->join(', ') ?: 'Unknown Author' }}
                                </p>
                                <p class="text-sm text-gray-600 line-clamp-3">
                                    {{ Str::limit(strip_tags($article->abstract), 150) }}
                                </p>
                            </div>
                            <div
                                class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    {{ $article->published_at?->format('M j, Y') }}
                                </span>
                                <a href="{{ route('journal.public.article', ['journal' => $journal->slug, 'article' => $article->seq_id]) }}"
                                    class="text-xs font-medium text-primary-600 hover:text-primary-700">
                                    Read More →
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Current Issue -->
    @if ($latestIssue)
        <section class="py-16 bg-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="grid lg:grid-cols-3 gap-8 p-8">
                        <div class="lg:col-span-1">
                            <div
                                class="aspect-[3/4] bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center">
                                @if ($latestIssue->cover_path)
                                    <img src="{{ Storage::url($latestIssue->cover_path) }}" alt="Issue Cover"
                                        class="w-full h-full object-cover rounded-xl">
                                @else
                                    <div class="text-center text-white p-6">
                                        <p class="text-lg font-bold">Vol. {{ $latestIssue->volume }}</p>
                                        <p class="text-3xl font-bold">No. {{ $latestIssue->number }}</p>
                                        <p class="text-lg mt-2">{{ $latestIssue->year }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="lg:col-span-2">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mb-4">
                                Current Issue
                            </span>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ $latestIssue->display_title }}
                            </h2>
                            <p class="text-gray-500 mb-6">
                                Published {{ $latestIssue->published_at?->format('F j, Y') }}
                            </p>
                            <p class="text-gray-600 mb-6">
                                This issue contains {{ $latestIssue->submissions()->published()->count() }} articles
                                covering various topics in our field.
                            </p>
                            <a href="{{ route('journal.public.issue', ['journal' => $journal->slug, 'issue' => $latestIssue->seq_id]) }}"
                                class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                View Issue
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Call to Action -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl p-8 lg:p-12 text-center text-white">
                <h2 class="text-3xl font-bold mb-4">Ready to Publish Your Research?</h2>
                <p class="text-primary-100 mb-8 max-w-2xl mx-auto">
                    Join our community of researchers and contribute to the advancement of knowledge.
                    We welcome submissions from all fields.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition-colors">
                        Submit Now
                    </a>
                    <a href="{{ route('journal.public.author-guidelines', ['journal' => $journal->slug]) }}"
                        class="inline-flex items-center justify-center px-6 py-3 border-2 border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors">
                        View Guidelines
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
