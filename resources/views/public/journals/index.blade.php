@extends('layouts.portal')

@section('title', 'Browse Journals')
@section('description', 'Jelajahi koleksi jurnal akademik berkualitas dari berbagai disiplin ilmu')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-accent-900 py-16 lg:py-24 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 hero-pattern"></div>
        
        <!-- Floating Shapes -->
        <div class="absolute top-10 left-10 w-64 h-64 bg-primary-500/20 rounded-full blur-3xl float-animation"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 bg-accent-500/20 rounded-full blur-3xl float-animation" style="animation-delay: -3s;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-6">
                    <i class="fas fa-book-open text-primary-300 mr-2"></i>
                    <span class="text-white/90 text-sm font-medium">{{ $totalJournals }} Jurnal Tersedia</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-display text-white mb-6">
                    Jelajahi Jurnal Kami
                </h1>
                <p class="text-lg text-white/70 max-w-2xl mx-auto mb-10">
                    Temukan jurnal akademik berkualitas dari berbagai disiplin ilmu
                </p>

                <!-- Search Bar -->
                <form action="{{ route('portal.journals') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="relative flex items-center bg-white rounded-2xl shadow-2xl overflow-hidden">
                        <i class="fas fa-search absolute left-5 text-gray-400"></i>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Cari jurnal berdasarkan nama atau topik..."
                               class="w-full pl-14 pr-4 py-4 text-gray-700 placeholder-gray-400 focus:outline-none text-lg">
                        @if($alpha)
                            <input type="hidden" name="alpha" value="{{ $alpha }}">
                        @endif
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-primary-600 to-accent-600 text-white font-semibold hover:from-primary-700 hover:to-accent-700 transition-all">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Sidebar Filters -->
                <aside class="lg:w-1/4 flex-shrink-0" x-data="{ filtersOpen: true }">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden sticky top-24">
                        <!-- Sidebar Header -->
                        <button @click="filtersOpen = !filtersOpen" class="w-full flex items-center justify-between p-6 border-b border-gray-100 lg:cursor-default">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-filter text-white"></i>
                                </div>
                                <h3 class="font-bold text-gray-900">Refine Results</h3>
                            </div>
                            <i class="fas fa-chevron-down lg:hidden transition-transform" :class="filtersOpen && 'rotate-180'"></i>
                        </button>

                        <form action="{{ route('portal.journals') }}" method="GET" x-show="filtersOpen" x-collapse>
                            @if($search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            @if($alpha)
                                <input type="hidden" name="alpha" value="{{ $alpha }}">
                            @endif

                            <div class="p-6 space-y-6">
                                <!-- Access Type -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Tipe Akses</h4>
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="checkbox" name="access[]" value="open_access"
                                                   {{ in_array('open_access', (array)$access) ? 'checked' : '' }}
                                                   class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                            <span class="flex items-center gap-2">
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                <span class="text-gray-700">Open Access</span>
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="checkbox" name="access[]" value="subscription"
                                                   {{ in_array('subscription', (array)$access) ? 'checked' : '' }}
                                                   class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                            <span class="flex items-center gap-2">
                                                <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                                                <span class="text-gray-700">Subscription</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Topics -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Bidang Ilmu</h4>
                                    <div class="space-y-2 max-h-64 overflow-y-auto">
                                        @foreach($availableTopics as $key => $label)
                                            <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                                <input type="checkbox" name="topics[]" value="{{ $key }}"
                                                       {{ in_array($key, (array)$topics) ? 'checked' : '' }}
                                                       class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                                <span class="text-gray-700">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="pt-4 border-t border-gray-100 space-y-3">
                                    <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl font-semibold hover:from-primary-700 hover:to-accent-700 transition-all shadow-lg shadow-primary-500/25">
                                        <i class="fas fa-check mr-2"></i>
                                        Apply Filters
                                    </button>
                                    <a href="{{ route('portal.journals') }}" class="block w-full py-3 px-4 text-center text-gray-600 hover:text-primary-600 font-medium transition-colors">
                                        <i class="fas fa-times mr-2"></i>
                                        Clear All Filters
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </aside>

                <!-- Journal Listing -->
                <div class="lg:w-3/4">
                    <!-- A-Z Filter Bar -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-6 overflow-x-auto">
                        <div class="flex items-center gap-1 min-w-max">
                            @foreach($alphabet as $letter)
                                <a href="{{ route('portal.journals', array_merge(request()->except('alpha', 'page'), $letter !== 'all' ? ['alpha' => $letter] : [])) }}"
                                   class="px-3 py-2 rounded-lg text-sm font-medium transition-all
                                          {{ ($alpha === $letter || ($letter === 'all' && !$alpha)) 
                                              ? 'bg-gradient-to-r from-primary-600 to-accent-600 text-white shadow-lg shadow-primary-500/25' 
                                              : 'text-gray-600 hover:bg-gray-100 hover:text-primary-600' }}">
                                    {{ $letter === 'all' ? 'All' : $letter }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Results Count -->
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-gray-600">
                            Ditemukan <span class="font-bold text-primary-600">{{ $journals->total() }}</span> jurnal
                            @if($search)
                                untuk "<span class="font-semibold">{{ $search }}</span>"
                            @endif
                            @if($alpha && $alpha !== 'all')
                                dimulai dengan huruf "<span class="font-semibold">{{ $alpha }}</span>"
                            @endif
                        </p>
                    </div>

                    <!-- Journal Cards -->
                    <div class="space-y-4">
                        @forelse($journals as $journal)
                            <article class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-primary-200 transition-all duration-300 overflow-hidden group">
                                <div class="flex flex-col sm:flex-row">
                                    <!-- Cover Image / Placeholder -->
                                    <div class="sm:w-48 lg:w-56 flex-shrink-0">
                                        @if($journal->thumbnail_path)
                                            <img src="{{ Storage::url($journal->thumbnail_path) }}" 
                                                 alt="{{ $journal->name }}"
                                                 class="w-full h-48 sm:h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-48 sm:h-full bg-gradient-to-br from-primary-500 via-accent-500 to-pink-500 flex items-center justify-center">
                                                <i class="fas fa-book-open text-white text-4xl"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 p-6">
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            <!-- Open Access Badge -->
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-primary-100 to-accent-100 text-primary-700 border border-primary-200">
                                                <i class="fas fa-unlock-alt mr-1"></i>
                                                Open Access
                                            </span>
                                            @if($journal->issn_online)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    eISSN: {{ $journal->issn_online }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Publisher/Association -->
                                        @if($journal->publisher)
                                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                                {{ $journal->publisher }}
                                            </p>
                                        @endif

                                        <!-- Title -->
                                        <h2 class="text-xl lg:text-2xl font-bold font-display text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
                                            <a href="{{ url('/' . $journal->path) }}">
                                                {{ $journal->name }}
                                            </a>
                                        </h2>

                                        <!-- Latest Issue Info -->
                                        @if($journal->issues->isNotEmpty())
                                            @php $latestIssue = $journal->issues->first(); @endphp
                                            <p class="text-sm text-gray-500 mb-3">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                Vol {{ $latestIssue->volume }}, No {{ $latestIssue->number }} ({{ $latestIssue->year }})
                                            </p>
                                        @endif

                                        <!-- Description -->
                                        @if($journal->description)
                                            <p class="text-gray-600 line-clamp-2 mb-4">
                                                {{ Str::limit(strip_tags($journal->description), 180) }}
                                            </p>
                                        @endif

                                        <!-- Stats & Action -->
                                        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-gray-100">
                                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-file-alt text-primary-400"></i>
                                                    {{ $journal->submissions_count }} Artikel
                                                </span>
                                            </div>
                                            <a href="{{ url('/' . $journal->path) }}" 
                                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl font-semibold text-sm hover:from-primary-700 hover:to-accent-700 transition-all shadow-lg shadow-primary-500/25">
                                                View Journal
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <!-- Empty State -->
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-12 text-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-search text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Jurnal Ditemukan</h3>
                                <p class="text-gray-500 mb-6">Coba ubah kata kunci pencarian atau hapus filter yang diterapkan.</p>
                                <a href="{{ route('portal.journals') }}" 
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl font-semibold hover:from-primary-700 hover:to-accent-700 transition-all shadow-lg shadow-primary-500/25">
                                    <i class="fas fa-redo"></i>
                                    Reset Pencarian
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($journals->hasPages())
                        <div class="mt-8">
                            {{ $journals->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom scrollbar for filter panel */
    aside ::-webkit-scrollbar { width: 4px; }
    aside ::-webkit-scrollbar-track { background: transparent; }
    aside ::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 2px; }
</style>
@endpush
