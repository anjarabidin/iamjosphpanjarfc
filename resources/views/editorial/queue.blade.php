<x-app-layout>
    <x-slot name="title">Submission Queue</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Submission Queue</h1>
        <p class="mt-1 text-sm text-gray-500">Manage incoming submissions awaiting editorial decision.</p>
    </x-slot>

    <!-- Queue Type Tabs (OJS 3.3 Style) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('journal.editorial.queue', ['journal' => $journal->slug, 'queue' => 'unassigned']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $queueType === 'unassigned' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Unassigned
                    @if (isset($queueCounts['unassigned']) && $queueCounts['unassigned'] > 0)
                        <span
                            class="ml-2 bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $queueCounts['unassigned'] }}</span>
                    @endif
                </a>
                <a href="{{ route('journal.editorial.queue', ['journal' => $journal->slug, 'queue' => 'active']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $queueType === 'active' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Active
                    @if (isset($queueCounts['active']) && $queueCounts['active'] > 0)
                        <span
                            class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $queueCounts['active'] }}</span>
                    @endif
                </a>
                <a href="{{ route('journal.editorial.queue', ['journal' => $journal->slug, 'queue' => 'all']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $queueType === 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    All
                    @if (isset($queueCounts['all']) && $queueCounts['all'] > 0)
                        <span
                            class="ml-2 bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $queueCounts['all'] }}</span>
                    @endif
                </a>
            </nav>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('journal.editorial.queue', ['journal' => $journal->slug]) }}"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <input type="hidden" name="queue" value="{{ $queueType }}">
            <div class="flex items-center space-x-4">
                <select name="status" onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 bg-white text-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>New Submissions</option>
                    <option value="in_review" {{ $status === 'in_review' ? 'selected' : '' }}>In Review</option>
                    <option value="revision_required" {{ $status === 'revision_required' ? 'selected' : '' }}>Revision
                        Required</option>
                </select>
            </div>
            <div class="flex items-center">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Search submissions..."
                        class="pl-10 pr-4 py-2 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 w-64">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </form>
    </div>

    <!-- Submissions List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if ($submissions->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach ($submissions as $submission)
                    <li class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('journal.workflow.show', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                    class="text-lg font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                                    {{ $submission->title }}
                                </a>
                                <div class="mt-1 flex items-center gap-4 text-sm text-gray-500">
                                    <span>
                                        <i class="fa-regular fa-user mr-1"></i>
                                        {{ $submission->author?->name ?? 'Unknown Author' }}
                                    </span>
                                    @if ($submission->section)
                                        <span>
                                            <i class="fa-regular fa-folder mr-1"></i>
                                            {{ $submission->section->title }}
                                        </span>
                                    @endif
                                    <span>
                                        <i class="fa-regular fa-calendar mr-1"></i>
                                        {{ $submission->submitted_at?->format('M d, Y') ?? 'Not submitted' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 ml-4">
                                <!-- Status Badge -->
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @switch($submission->status)
                                        @case('submitted') bg-blue-100 text-blue-800 @break
                                        @case('in_review') bg-yellow-100 text-yellow-800 @break
                                        @case('revision_required') bg-orange-100 text-orange-800 @break
                                        @case('accepted') bg-green-100 text-green-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ $submission->status_label }}
                                </span>

                                <!-- Actions -->
                                @if ($submission->status === 'submitted' && $submission->stage_id === 1)
                                    <form
                                        action="{{ route('journal.editorial.assign', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Assign to Me
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('journal.workflow.show', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fa-solid fa-arrow-right mr-1"></i> Workflow
                                </a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $submissions->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if ($queueType === 'unassigned')
                        No unassigned submissions
                    @elseif($queueType === 'active')
                        No active submissions
                    @else
                        No submissions in queue
                    @endif
                </h3>
                <p class="text-gray-500">
                    @if ($queueType === 'unassigned')
                        New submissions will appear here once authors submit their articles.
                    @else
                        Submissions you're working on will appear here.
                    @endif
                </p>
            </div>
        @endif
    </div>
</x-app-layout>
