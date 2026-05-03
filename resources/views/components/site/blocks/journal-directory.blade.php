{{-- Journal Directory Block - Complete Listing with Search & Filter --}}
@props(['block', 'data' => []])

@php
$config = $block->config ?? [];
$title = $config['title'] ?? 'All Journals';
$subtitle = $config['subtitle'] ?? 'Browse our complete collection';
$layout = $config['layout'] ?? 'grid';
$columns = $config['columns'] ?? 4;
$showSearch = $config['show_search'] ?? true;
$showFilter = $config['show_filter'] ?? true;

$journals = $data['journals'] ?? collect();
@endphp

<section class="py-16 md:py-24 bg-white" 
         x-data="journalDirectory()"
         id="journal-directory">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ $title }}
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                {{ $subtitle }}
            </p>
        </div>

        {{-- Search & Filter Bar --}}
        @if($showSearch || $showFilter)
            <div class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between">
                @if($showSearch)
                    <div class="relative w-full md:w-96">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               x-model="searchQuery"
                               @input="filterJournals()"
                               placeholder="Search journals..."
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                @endif

                @if($showFilter)
                    <div class="flex items-center gap-4">
                        <select x-model="sortBy" 
                                @change="filterJournals()"
                                class="rounded-xl border border-gray-200 py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="name">Sort by Name</option>
                            <option value="articles">Most Articles</option>
                            <option value="newest">Newest</option>
                        </select>

                        {{-- Layout Toggle --}}
                        <div class="hidden md:flex items-center bg-gray-100 rounded-xl p-1">
                            <button @click="viewMode = 'grid'"
                                    :class="viewMode === 'grid' ? 'bg-white shadow-sm' : ''"
                                    class="p-2 rounded-lg transition-all">
                                <i class="fa-solid fa-grip text-gray-600"></i>
                            </button>
                            <button @click="viewMode = 'list'"
                                    :class="viewMode === 'list' ? 'bg-white shadow-sm' : ''"
                                    class="p-2 rounded-lg transition-all">
                                <i class="fa-solid fa-list text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Journals Grid/List --}}
        @if($journals->isNotEmpty())
            <div :class="viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $columns }} gap-6' : 'space-y-4'"
                 id="journals-container">
                @foreach($journals as $journal)
                    <template x-if="viewMode === 'grid'">
                        <x-site.journal-card :journal="$journal" />
                    </template>
                    <template x-if="viewMode === 'list'">
                        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4 hover:shadow-md transition-shadow">
                            {{-- Logo --}}
                            <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                @if($journal->logo_path)
                                    <img src="{{ Storage::url($journal->logo_path) }}" 
                                         alt="{{ $journal->name }}"
                                         class="w-full h-full object-contain p-2">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold">
                                        {{ strtoupper(substr($journal->abbreviation ?? $journal->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 truncate">
                                    <a href="{{ route('journal.public.home', $journal->slug) }}" class="hover:text-blue-600">
                                        {{ $journal->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500">
                                    @if($journal->abbreviation)
                                        {{ $journal->abbreviation }} •
                                    @endif
                                    @if($journal->issn_online)
                                        e-ISSN: {{ $journal->issn_online }}
                                    @endif
                                </p>
                            </div>

                            {{-- Stats --}}
                            <div class="hidden md:flex items-center gap-6 text-sm text-gray-500">
                                <span>{{ $journal->issues_count ?? 0 }} Issues</span>
                                <span>{{ $journal->submissions_count ?? 0 }} Articles</span>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('journal.public.home', $journal->slug) }}"
                                   class="px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 text-center">
                                    View Journal
                                </a>
                                @if($journal->currentIssue)
                                    <a href="{{ route('journal.public.current', $journal->slug) }}"
                                       class="px-3 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 text-center">
                                        Current Issue
                                    </a>
                                @endif
                            </div>
                        </div>
                    </template>
                @endforeach
            </div>

            {{-- Pagination / Load More --}}
            <div class="text-center mt-12">
                <a href="{{ route('portal.journals') }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                    View All {{ $journals->count() }} Journals
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <i class="fa-solid fa-book text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No journals available.</p>
            </div>
        @endif
    </div>
</section>

@pushOnce('scripts')
<script>
function journalDirectory() {
    return {
        searchQuery: '',
        sortBy: 'name',
        viewMode: 'grid',
        
        filterJournals() {
            // Client-side filtering (for small datasets)
            // For larger datasets, this should trigger an AJAX call
            const query = this.searchQuery.toLowerCase();
            const cards = document.querySelectorAll('#journals-container > div, #journals-container > template + div');
            
            // Simplified - in production, use Alpine's x-for with filtered data
        }
    }
}
</script>
@endPushOnce
