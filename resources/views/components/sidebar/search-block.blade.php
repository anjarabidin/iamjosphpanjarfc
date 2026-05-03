{{-- Search Block - Journal search form --}}
@props(['journal', 'settings' => [], 'block' => null])

<form action="{{ route('journal.public.search', $journal->slug) }}" method="GET">
    <div class="relative">
        <input type="text" name="q"
            placeholder="Search articles..."
            class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fa-solid fa-search text-gray-400"></i>
        </div>
    </div>
    <button type="submit"
        class="w-full mt-2 px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
        Search
    </button>
</form>