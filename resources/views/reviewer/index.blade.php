@php
$journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">My Reviews</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">My Reviews</h1>
        <p class="mt-1 text-sm text-gray-500">Submissions assigned to you for peer review.</p>
    </x-slot>

    <!-- Tabs & Filters Navigation -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between border-b border-gray-200 mb-6 gap-4">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            @php
            $currentStatus = $status;
            @endphp

            {{-- MyQueue Tab --}}
            <a href="{{ route('journal.reviewer.index', ['journal' => $journal->slug, 'status' => 'myqueue']) }}"
                class="{{ $currentStatus === 'myqueue'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                My Queue
                <span class="{{ $currentStatus === 'myqueue' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }} ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block">
                    {{ $statusCounts['myqueue'] }}
                </span>
            </a>

            {{-- Archives Tab --}}
            <a href="{{ route('journal.reviewer.index', ['journal' => $journal->slug, 'status' => 'archives']) }}"
                class="{{ $currentStatus === 'archives'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                Archives
                <span class="{{ $currentStatus === 'archives' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }} ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block">
                    {{ $statusCounts['archives'] }}
                </span>
            </a>
        </nav>

        <div class="flex items-center gap-3 pb-3 lg:pb-0"
             x-data="{
                showFilters: false,
                search: '{{ request('search') }}',
                submitSearch() {
                    this.$refs.searchForm.submit();
                },
                clearFilters() {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('search');
                    url.searchParams.delete('sections[]');
                    url.searchParams.delete('statuses[]');
                    // Handle PHP array removal correctly
                    let cleanUrl = url.toString().replace(/sections%5B%5D=[^&]*&?/g, '').replace(/statuses%5B%5D=[^&]*&?/g, '');
                    window.location.href = cleanUrl;
                }
             }">

            <!-- Search Input -->
            <form x-ref="searchForm" action="{{ url()->current() }}" method="GET" class="relative group">
                <input type="hidden" name="status" value="{{ $status }}">

                {{-- Forward existing filters as hidden inputs to preserve them --}}
                @foreach(request('sections', []) as $secId)
                    <input type="hidden" name="sections[]" value="{{ $secId }}">
                @endforeach
                @foreach(request('statuses', []) as $statName)
                    <input type="hidden" name="statuses[]" value="{{ $statName }}">
                @endforeach

                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-colors group-focus-within:text-indigo-500"></i>
                <input type="text" name="search" x-model="search" @keydown.enter.prevent="submitSearch()"
                    placeholder="Search by title or ID..."
                    class="pl-9 pr-4 py-2 text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 w-48 lg:w-64 transition-all">
            </form>

            <!-- Filters Toggle -->
            <div class="relative">
                <button @click="showFilters = !showFilters"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all {{ request()->hasAny(['sections', 'statuses']) ? 'ring-2 ring-indigo-500 border-transparent text-indigo-700' : '' }}">
                    <i class="fa-solid fa-filter mr-2 {{ request()->hasAny(['sections', 'statuses']) ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                    Filters
                    @php
                        $activeFilters = count(request('sections', [])) + count(request('statuses', []));
                    @endphp
                    @if($activeFilters > 0)
                        <span class="ml-2 bg-indigo-600 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">
                            {{ $activeFilters }}
                        </span>
                    @endif
                </button>

                <!-- Filter Dropdown -->
                <div x-show="showFilters" 
                    @click.away="showFilters = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 top-full mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-200 z-[60] overflow-hidden" 
                    x-cloak>

                    <form action="{{ url()->current() }}" method="GET" class="flex flex-col max-h-[80vh]">
                        <input type="hidden" name="status" value="{{ $status }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Filters</h3>
                            <button type="button" @click="clearFilters()" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                                Clear All
                            </button>
                        </div>

                        <div class="overflow-y-auto p-4 space-y-6 custom-scrollbar">
                            {{-- Status Section --}}
                            <div>
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Review Status</h4>
                                <div class="space-y-2.5">
                                    @php
                                        $statusOptions = [
                                            'pending' => 'Pending Response',
                                            'accepted' => 'Active / In Progress',
                                            'overdue' => 'Overdue',
                                            'completed' => 'Completed'
                                        ];
                                    @endphp
                                    @foreach($statusOptions as $val => $label)
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <input type="checkbox" name="statuses[]" value="{{ $val }}" 
                                                {{ in_array($val, request('statuses', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Sections Section --}}
                            @if($journalSections->count() > 0)
                            <div>
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Journal Sections</h4>
                                <div class="space-y-2.5">
                                    @foreach($journalSections as $section)
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <input type="checkbox" name="sections[]" value="{{ $section->id }}"
                                                {{ in_array($section->id, request('sections', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                            <span class="text-sm text-gray-600 group-hover:text-gray-900 truncate transition-colors">{{ $section->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg text-sm font-bold shadow-md shadow-indigo-100 transition-all transform active:scale-[0.98]">
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    @if ($assignments->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        @if ($currentStatus === 'myqueue')
        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-list-check text-blue-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-1">No Reviews in Your Queue</h3>
        <p class="text-gray-500">You're all caught up! New review invitations will appear here.</p>
        @else
        <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-archive text-green-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-1">No Archived Reviews</h3>
        <p class="text-gray-500">Reviews you complete will be archived here.</p>
        @endif
    </div>
    @else
    <div class="space-y-4" x-data="{ activeRow: null }">
        @foreach ($assignments as $assignment)
        <div x-data="{ expanded: false }"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6 cursor-pointer hover:bg-gray-50/50 transition-colors" @click="expanded = !expanded">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <!-- Status Badge -->
                        <div class="flex items-center gap-2 mb-3">
                            @switch($assignment->status)
                            @case('pending')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending Response
                            </span>
                            @break

                            @case('accepted')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                In Progress
                            </span>
                            @break

                            @case('completed')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Completed
                            </span>
                            @break

                            @case('declined')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Declined
                            </span>
                            @break
                            @endswitch

                            @if ($assignment->isOverdue())
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Overdue
                            </span>
                            @endif

                            <span class="text-xs text-gray-500">Round {{ $assignment->round }}</span>
                        </div>

                        <!-- Title (Blind Review - No Author Info) -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $assignment->submission->title }}
                        </h3>

                        <!-- Meta Info -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                            <span>{{ $assignment->submission->section->name ?? 'Uncategorized' }}</span>
                            <span>•</span>
                            <span>Assigned: {{ $assignment->assigned_at?->format('M j, Y') }}</span>
                            @if ($assignment->due_date)
                            <span>•</span>
                            <span class="{{ $assignment->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                                Due: {{ $assignment->due_date->format('M j, Y') }}
                                @if (!$assignment->isOverdue() && $assignment->days_until_due !== null)
                                ({{ $assignment->days_until_due }} days)
                                @endif
                            </span>
                            @endif
                        </div>

                        <!-- Recommendation (if completed) -->
                        @if ($assignment->status === 'completed')
                        <div
                            class="mt-3 inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-{{ $assignment->recommendation_color }}-100 text-{{ $assignment->recommendation_color }}-800">
                            Your recommendation: {{ $assignment->recommendation_label }}
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="ml-4 flex-shrink-0 flex items-start gap-3" @click.stop>
                        <div class="flex flex-col items-end gap-2">
                            @if ($assignment->status === 'pending')
                            <div class="flex items-center gap-2">
                                <form action="{{ route('journal.reviewer.accept', ['journal' => $journal->slug, 'assignment' => $assignment]) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                        Accept
                                    </button>
                                </form>
                                <form action="{{ route('journal.reviewer.decline', ['journal' => $journal->slug, 'assignment' => $assignment]) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                        Decline
                                    </button>
                                </form>
                            </div>
                            @elseif($assignment->status === 'accepted')
                            <a href="{{ route('journal.reviewer.show', ['journal' => $journal->slug, 'identifier' => $assignment->slug]) }}"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Submit Review
                            </a>
                            @elseif($assignment->status === 'completed')
                            <a href="{{ route('journal.reviewer.show', ['journal' => $journal->slug, 'identifier' => $assignment->slug]) }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                View Review
                            </a>
                            @endif
                            
                            <button @click="expanded = !expanded" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100 transition-all">
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': expanded }"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expanded Details (OJS Style) -->
            <div x-show="expanded" x-collapse x-cloak class="bg-gray-50/50 border-t border-gray-100">
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="space-y-3">
                            <h4 class="font-bold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-info-circle text-gray-400"></i>
                                Review Details
                            </h4>
                            <div class="space-y-2 text-gray-600">
                                <p class="flex items-center gap-2">
                                    <span class="text-gray-400 w-24">Submission ID:</span>
                                    <span class="font-mono text-gray-900">{{ $assignment->submission->submission_code }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <span class="text-gray-400 w-24">Section:</span>
                                    <span class="text-gray-900">{{ $assignment->submission->section->name ?? 'N/A' }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <span class="text-gray-400 w-24">Method:</span>
                                    <span class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $assignment->review_method)) }}</span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <h4 class="font-bold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-calendar text-gray-400"></i>
                                Key Dates
                            </h4>
                            <div class="space-y-2 text-gray-600">
                                <p class="flex items-center gap-2">
                                    <span class="text-gray-400 w-32">Response Due:</span>
                                    <span class="text-gray-900 font-medium">{{ $assignment->response_due_date?->format('F j, Y') ?? 'N/A' }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <span class="text-gray-400 w-32">Review Due:</span>
                                    <span class="text-gray-900 font-medium {{ $assignment->isOverdue() ? 'text-red-600' : '' }}">{{ $assignment->due_date?->format('F j, Y') ?? 'N/A' }}</span>
                                </p>
                                @if($assignment->completed_at)
                                <p class="flex items-center gap-2 text-green-600 font-medium">
                                    <i class="fa-solid fa-check-circle"></i>
                                    <span>Completed on {{ $assignment->completed_at->format('F j, Y') }}</span>
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Activity Log & Notes -->
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="button" 
                                @click.stop="openLogModal('{{ route('submission.log.history', $assignment->submission->id) }}')"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline transition-all">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            Activity Log & Notes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $assignments->links() }}
    </div>
    @endif

    {{-- Activity Log & Notes Modal --}}
    <div id="logModalBackdrop" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 hidden">
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in duration-200">
            
            {{-- Content will be injected here --}}
            <div id="logModalContent" class="h-full">
                {{-- Loading Spinner Default --}}
                <div class="flex flex-col items-center justify-center h-96 space-y-4">
                    <svg class="animate-spin h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-500">Fetching activity records...</p>
                </div>
            </div>
            
        </div>
    </div>

    {{-- Modal Script --}}
    <script>
        function openLogModal(url) {
            const modal = document.getElementById('logModalBackdrop');
            const content = document.getElementById('logModalContent');
            
            // 1. Show Modal with Loading State
            modal.classList.remove('hidden');
            content.innerHTML = '<div class="flex flex-col items-center justify-center h-96 space-y-4 shadow-inner"><svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><p class="text-sm font-medium text-gray-500">Fetching activity records...</p></div>';

            // 2. Fetch Data
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = '<div class="p-12 text-center h-96 flex flex-col items-center justify-center"><i class="fa-solid fa-circle-exclamation text-red-500 text-4xl mb-4"></i><p class="text-red-500 font-bold">Failed to load activity logs.</p><button onclick="closeLogModal()" class="mt-4 text-sm text-gray-500 underline">Close</button></div>';
                });
        }

        function closeLogModal() {
            document.getElementById('logModalBackdrop').classList.add('hidden');
        }

        // Close on click outside
        document.getElementById('logModalBackdrop').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogModal();
            }
        });
    </script>
</x-app-layout>
