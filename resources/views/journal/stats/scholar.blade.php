@extends('layouts.app')

@section('title', 'Scholar IAMJOS Monitor - ' . $journal->name)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('journal.settings.tools.index', ['journal' => $journal->slug]) }}"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-primary-600 hover:border-primary-100 hover:bg-primary-50 transition-all shadow-sm"
                    title="Back to Tools">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Scholar IAMJOS Monitor</h1>
                    <p class="text-sm text-gray-500 mt-1">Real-time monitoring of article visibility on Google Scholar.</p>
                </div>
            </div>
            <div>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="fa-solid fa-robot mr-1.5"></i> Auto-checking enabled (Every 7 days)
                </span>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Total Monitored --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Monitored</p>
                    <div class="flex items-baseline mt-1">
                        <span class="text-2xl font-bold text-gray-900">{{ number_format($totalMonitored) }}</span>
                        <span class="ml-2 text-xs text-gray-500">articles</span>
                    </div>
                </div>
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                    <i class="fa-solid fa-list-check text-lg"></i>
                </div>
            </div>

            {{-- Indexed --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Indexed</p>
                    <div class="flex items-baseline mt-1">
                        <span class="text-2xl font-bold text-emerald-600">{{ number_format($indexedCount) }}</span>
                        <span class="ml-2 text-xs text-gray-500">found</span>
                    </div>
                </div>
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-check-circle text-lg"></i>
                </div>
            </div>

            {{-- Issues --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Issues</p>
                    <div class="flex items-baseline mt-1">
                        <span class="text-2xl font-bold text-rose-600">{{ number_format($issuesCount) }}</span>
                        <span class="ml-2 text-xs text-gray-500">missing</span>
                    </div>
                </div>
                <div class="w-10 h-10 bg-rose-50 rounded-lg flex items-center justify-center text-rose-600">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
            </div>

            {{-- Success Rate --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Indexed Rate</p>
                    <div class="flex items-baseline mt-1">
                        <span class="text-2xl font-bold text-gray-900">{{ $successRate }}%</span>
                    </div>
                </div>
                <div
                    class="w-10 h-10 rounded-lg flex items-center justify-center
                {{ $successRate >= 90 ? 'bg-emerald-50 text-emerald-600' : ($successRate >= 70 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">
                    <i class="fa-solid fa-chart-pie text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Main Monitor List --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Monitor List</h3>
                <div class="flex items-center gap-2">
                    {{-- Optional Filter Buttons could go here --}}
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($submissions as $submission)
                    @php
                        $stat = $submission->indexStat;
                        // Determine status based on monitoring setting
                        $isMonitored = $stat && $stat->is_monitored;

                        if (!$isMonitored) {
                            $status = 'not_monitored';
                        } else {
                            $status = $stat
                                ? ($stat->is_indexed
                                    ? 'indexed'
                                    : ($stat->last_check_status === 'not_found'
                                        ? 'missing'
                                        : ($stat->last_check_status === 'error'
                                            ? 'error'
                                            : 'pending')))
                                : 'pending';
                        }

                        // Colors based on status
                        $borderClass = match ($status) {
                            'indexed' => 'bg-emerald-500',
                            'missing' => 'bg-rose-500',
                            'error' => 'bg-amber-500',
                            'not_monitored' => 'bg-gray-300',
                            default => 'bg-blue-400',
                        };

                        $badgeClass = match ($status) {
                            'indexed' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
                            'missing' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
                            'error' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
                            'not_monitored' => 'bg-gray-100 text-gray-600 ring-1 ring-gray-600/20',
                            default => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
                        };

                        $badgeLabel = match ($status) {
                            'indexed' => 'Indexed',
                            'missing' => 'Not Found',
                            'error' => 'Check Failed',
                            'not_monitored' => 'Not Monitored',
                            default => 'Pending Check',
                        };
                    @endphp

                    <div class="group relative flex items-center justify-between p-4 hover:bg-gray-50/80 transition-colors">
                        {{-- Left Color Indicator --}}
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $borderClass }}"></div>

                        {{-- Main Content --}}
                        <div class="flex items-center gap-4 pl-3 flex-1 min-w-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <h4
                                        class="text-sm font-semibold text-gray-900 truncate group-hover:text-primary-600 transition-colors">
                                        {{ $submission->title }}
                                    </h4>
                                    <span
                                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span class="flex items-center truncate max-w-[200px]">
                                        <i class="fa-regular fa-user mr-1.5 text-gray-400"></i>
                                        {{ $submission->author->name ?? 'Unknown Author' }}
                                    </span>
                                    @if ($isMonitored && $stat && $stat->last_checked_at)
                                        <span class="flex items-center"
                                            title="{{ $stat->last_checked_at->format('d M Y H:i') }}">
                                            <i class="fa-regular fa-clock mr-1.5 text-gray-400"></i>
                                            Checked {{ $stat->last_checked_at->diffForHumans() }}
                                        </span>
                                    @elseif($isMonitored)
                                        <span class="flex items-center">
                                            <i class="fa-regular fa-clock mr-1.5 text-gray-400"></i>
                                            In Queue
                                        </span>
                                    @endif

                                    @if ($isMonitored && $stat && $stat->scholar_url)
                                        <span class="flex items-center text-blue-600" title="Manual URL Configured">
                                            <i class="fa-solid fa-link mr-1"></i> Public URL
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 ml-4">
                            @if ($stat && $stat->scholar_url)
                                <a href="{{ $stat->scholar_url }}" target="_blank"
                                    class="p-2 text-gray-400 hover:text-blue-600 transition-colors rounded-full hover:bg-blue-50"
                                    title="View on Google Scholar">
                                    <i class="fa-brands fa-google-scholar text-base"></i>
                                </a>
                            @endif

                            @if (!$isMonitored)
                                <button type="button"
                                    @click="$dispatch('open-scholar-modal', { 
                                            title: '{{ addslashes($submission->title) }}', 
                                            id: '{{ $submission->id }}', 
                                            url: '{{ $stat->scholar_url ?? route('journal.public.article', ['journal' => $journal->slug, 'article' => $submission->seq_id]) }}',
                                            isMonitored: false
                                        })"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm">
                                    <i class="fa-solid fa-plus text-gray-400"></i>
                                    Add to Watchlist
                                </button>
                            @else
                                <div class="flex gap-2">
                                    <button type="button"
                                        @click="$dispatch('open-scholar-modal', { 
                                            title: '{{ addslashes($submission->title) }}', 
                                            id: '{{ $submission->id }}', 
                                            url: '{{ $stat->scholar_url ?? route('journal.public.article', ['journal' => $journal->slug, 'article' => $submission->seq_id]) }}',
                                            isMonitored: true
                                        })"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm">
                                        <i class="fa-solid fa-gear text-gray-400"></i>
                                        Config
                                    </button>

                                    {{-- Quick Check (Only if monitored) --}}
                                    <form
                                        action="{{ route('journal.settings.stats.scholar.check', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="p-2 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-indigo-50"
                                            title="Check Now">
                                            <i class="fa-solid fa-rotate-right"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div
                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="fa-solid fa-robot text-2xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">No Articles Monitored Yet</h3>
                        <p class="text-sm text-gray-500 mt-1">Once you publish articles, they will appear here
                            automatically.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($submissions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>
    {{-- Shared Modal --}}
    <div x-data="{
        open: false,
        title: '',
        submissionId: '',
        url: '',
        action: '',
        initModal(title, id, currentUrl, isMonitored) {
            this.title = title;
            this.submissionId = id;
            this.url = currentUrl;
            this.open = true;
        }
    }"
        @open-scholar-modal.window="initModal($event.detail.title, $event.detail.id, $event.detail.url, $event.detail.isMonitored)"
        x-show="open" class="relative z-50" style="display: none;">

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form method="POST"
                        :action="'{{ route('journal.settings.stats.scholar.index', ['journal' => $journal->slug]) }}/' +
                        submissionId">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-base font-semibold leading-6 text-gray-900"
                                x-text="'Configure Monitoring: ' + title"></h3>
                            <div class="mt-4">
                                <label for="scholar_url" class="block text-sm font-medium leading-6 text-gray-900">Article
                                    Public URL</label>
                                <div class="mt-2">
                                    <input type="url" name="scholar_url" id="scholar_url" x-model="url"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        placeholder="https://journal.com/index.php/abc/article/view/100">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Enter the full URL of this article on your journal
                                    website. We will search Google Scholar for this exact link.</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 justify-between">
                            <div class="flex flex-row-reverse gap-2">
                                <button type="submit" name="action" value="monitor"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                                    Save & Monitor
                                </button>
                                <button type="button" @click="open = false"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                            </div>

                            <button type="submit" name="action" value="pause"
                                class="inline-flex w-full justify-center rounded-md bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-600 shadow-sm hover:bg-rose-100 sm:w-auto border border-rose-200"
                                onclick="return confirm('Are you sure you want to pause monitoring for this article?')">
                                Pause Monitoring
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
