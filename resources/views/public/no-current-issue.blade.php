<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">No Current Issue</h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                There is no published issue available at the moment. Please check back later or browse our archives.
            </p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('journal.public.archives', ['journal' => $journal->slug]) }}"
                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    Browse Archives
                </a>
                <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}"
                    class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
