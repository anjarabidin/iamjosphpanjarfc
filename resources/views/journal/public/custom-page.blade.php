<x-layouts.public :journal="$journal" :title="$page->title . ' - ' . $journal->name">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Page Header --}}
        @if ($page->show_title ?? true)
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    @if ($page->icon)
                        <i class="{{ $page->icon }} mr-3 text-primary-600"></i>
                    @endif
                    {{ $page->title }}
                </h1>
                <div class="h-1 w-24 bg-primary-600 rounded"></div>
            </div>
        @endif

        {{-- Page Content --}}
        <div class="prose prose-lg max-w-none">
            {!! clean($page->content) !!}
        </div>

        {{-- Back Button --}}
        <div class="mt-12 pt-8 border-t border-gray-200">
            <a href="{{ route('journal.public.home', $journal->slug) }}"
                class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Home
            </a>
        </div>
    </div>
</x-layouts.public>
