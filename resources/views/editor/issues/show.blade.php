@php
    $journal = current_journal();
@endphp

@extends('layouts.app')

@section('title', 'Issue: ' . $issue->identifier)

@section('content')
    <div x-data="{
        showAddArticleModal: false,
        selectedArticles: [],
        toggleArticle(id) {
            const index = this.selectedArticles.indexOf(id);
            if (index === -1) {
                this.selectedArticles.push(id);
            } else {
                this.selectedArticles.splice(index, 1);
            }
        }
    }" class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                    <a href="{{ route('journal.issues.index', ['journal' => $journal->slug]) }}"
                        class="hover:text-indigo-600 transition-colors">Issues</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900">{{ $issue->identifier }}</span>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <!-- Issue Info -->
                    <div class="flex items-start gap-6">
                        <!-- Cover -->
                        <div
                            class="w-32 h-44 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-xl overflow-hidden shadow-lg flex-shrink-0">
                            @if ($issue->cover_path)
                                <img src="{{ Storage::url($issue->cover_path) }}" alt="{{ $issue->display_title }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-indigo-600">
                                    <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $issue->identifier }}</h1>
                                @if ($issue->is_published)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Published
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span>
                                        Upcoming
                                    </span>
                                @endif
                            </div>

                            @if ($issue->title)
                                <p class="text-lg text-gray-600 mb-3">{{ $issue->title }}</p>
                            @endif

                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ $issue->submissions->count() }} articles
                                </span>
                                @if ($issue->published_at)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Published {{ $issue->published_at->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('journal.issues.edit', ['journal' => $journal->slug, 'issue' => $issue]) }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Issue
                        </a>

                        @if ($issue->is_published)
                            <form
                                action="{{ route('journal.issues.unpublish', ['journal' => $journal->slug, 'issue' => $issue]) }}"
                                method="POST">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to unpublish this issue?')"
                                    class="inline-flex items-center px-4 py-2 bg-orange-50 border border-orange-200 rounded-lg text-orange-700 font-medium hover:bg-orange-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Unpublish
                                </button>
                            </form>
                            <a href="{{ route('journal.public.issue', ['journal' => $journal->slug, 'issue' => $issue->seq_id]) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 font-medium hover:bg-emerald-100 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                View Public
                            </a>
                        @else
                            <form
                                action="{{ route('journal.issues.publish', ['journal' => $journal->slug, 'issue' => $issue]) }}"
                                method="POST">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Publish this issue? All assigned articles will also be published.')"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:bg-emerald-700 transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Publish Issue
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Table of Contents (Main Column) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Table of Contents</h2>
                                <p class="text-sm text-gray-500">{{ $issue->submissions->count() }} articles in this issue
                                </p>
                            </div>
                            @if (!$issue->is_published)
                                <button @click="showAddArticleModal = true"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Article
                                </button>
                            @endif
                        </div>

                        @if ($issue->submissions->count() > 0)
                            <div class="divide-y divide-gray-100">
                                @foreach ($articlesBySection as $sectionName => $articles)
                                    <div class="p-6">
                                        <!-- Section Header -->
                                        <h3 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-4">
                                            {{ $sectionName }}
                                        </h3>

                                        <!-- Articles in Section -->
                                        <div class="space-y-4">
                                            @foreach ($articles as $index => $article)
                                                <div
                                                    class="group relative bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100 hover:border-indigo-200 hover:shadow-md transition-all">
                                                    <div class="flex items-start gap-4">
                                                        <!-- Article Number -->
                                                        <div
                                                            class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-700 font-bold text-sm">
                                                            {{ $loop->parent->iteration }}.{{ $loop->iteration }}
                                                        </div>

                                                        <!-- Article Info -->
                                                        <div class="flex-1 min-w-0">
                                                            <a href="{{ route('journal.submissions.show', ['journal' => $journal->slug, 'submission' => $article]) }}"
                                                                class="text-base font-semibold text-gray-900 hover:text-indigo-600 transition-colors line-clamp-2">
                                                                {{ $article->title }}
                                                            </a>

                                                            <div
                                                                class="flex items-center gap-2 mt-2 text-sm text-gray-500">
                                                                <span>{{ $article->authors->pluck('name')->join(', ') }}</span>
                                                            </div>

                                                            @if ($article->keywords)
                                                                <div class="flex flex-wrap gap-1 mt-2">
                                                                    @foreach (array_slice($article->keywords_array, 0, 3) as $keyword)
                                                                        <span
                                                                            class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                                                            {{ $keyword }}
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Status & Actions -->
                                                        <div class="flex-shrink-0 flex items-center gap-2">
                                                            @if ($article->status === 'published')
                                                                <span
                                                                    class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium">
                                                                    Published
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-medium">
                                                                    {{ $article->status_label }}
                                                                </span>
                                                            @endif

                                                            @if (!$issue->is_published)
                                                                <form
                                                                    action="{{ route('journal.issues.remove-article', ['journal' => $journal->slug, 'issue' => $issue, 'submission' => $article]) }}"
                                                                    method="POST"
                                                                    class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        onclick="return confirm('Remove this article from the issue?')"
                                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                                        title="Remove from issue">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                        </svg>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Articles Yet</h3>
                                <p class="text-gray-500 mb-6">Start adding accepted submissions to this issue.</p>
                                @if (!$issue->is_published)
                                    <button @click="showAddArticleModal = true"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add First Article
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Issue Details Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Issue Details</h3>

                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Volume</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $issue->volume }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Number</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $issue->number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Year</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $issue->year }}</dd>
                            </div>
                            @if ($issue->title)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                                    <dd class="text-gray-900">{{ $issue->title }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @if ($issue->is_published)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                            Published
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            Unpublished
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            @if ($issue->published_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Published Date</dt>
                                    <dd class="text-gray-900">{{ $issue->published_at->format('F d, Y') }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="text-gray-900">{{ $issue->created_at->format('F d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Quick Stats Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Article Statistics</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm text-gray-600">Total Articles</span>
                                <span class="text-lg font-bold text-gray-900">{{ $issue->submissions->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                                <span class="text-sm text-emerald-700">Published</span>
                                <span class="text-lg font-bold text-emerald-700">
                                    {{ $issue->submissions->where('status', 'published')->count() }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-xl">
                                <span class="text-sm text-yellow-700">Pending</span>
                                <span class="text-lg font-bold text-yellow-700">
                                    {{ $issue->submissions->where('status', '!=', 'published')->count() }}
                                </span>
                            </div>
                        </div>

                        @if (!$issue->is_published && $issue->submissions->where('status', 'accepted')->count() > 0)
                            <div class="mt-4 p-3 bg-blue-50 rounded-xl">
                                <p class="text-sm text-blue-700">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $issue->submissions->where('status', 'accepted')->count() }} article(s) will be
                                    published when you publish this issue.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Danger Zone -->
                    @if (!$issue->is_published && $issue->submissions->count() === 0)
                        <div class="bg-red-50 rounded-2xl border border-red-100 p-6">
                            <h3 class="text-lg font-bold text-red-900 mb-4">Danger Zone</h3>
                            <p class="text-sm text-red-700 mb-4">Delete this issue permanently. This action cannot be
                                undone.</p>
                            <form
                                action="{{ route('journal.issues.destroy', ['journal' => $journal->slug, 'issue' => $issue]) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this issue? This action cannot be undone.')"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                                    Delete Issue
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Article Modal -->
        <div x-show="showAddArticleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showAddArticleModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"
                    @click="showAddArticleModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="showAddArticleModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                    <form
                        action="{{ route('journal.issues.add-articles', ['journal' => $journal->slug, 'issue' => $issue]) }}"
                        method="POST">
                        @csrf

                        <div class="px-6 py-5 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Add Articles to Issue
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">Select accepted submissions to add to this issue
                                    </p>
                                </div>
                                <button type="button" @click="showAddArticleModal = false"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-4 max-h-96 overflow-y-auto">
                            @if ($availableSubmissions->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($availableSubmissions as $submission)
                                        <label
                                            class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 cursor-pointer transition-colors"
                                            :class="{
                                                'ring-2 ring-indigo-500 bg-indigo-50': selectedArticles.includes(
                                                    '{{ $submission->id }}')
                                            }">
                                            <input type="checkbox" name="submission_ids[]" value="{{ $submission->id }}"
                                                @change="toggleArticle('{{ $submission->id }}')"
                                                class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 line-clamp-2">{{ $submission->title }}
                                                </p>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ $submission->authors->pluck('name')->join(', ') }}
                                                </p>
                                                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                                    <span
                                                        class="px-2 py-0.5 bg-gray-200 rounded">{{ $submission->section->name ?? 'Uncategorized' }}</span>
                                                    <span>Accepted
                                                        {{ $submission->accepted_at?->format('M d, Y') ?? 'Recently' }}</span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Available Submissions</h3>
                                    <p class="text-gray-500">All accepted submissions have already been assigned to issues.
                                    </p>
                                </div>
                            @endif
                        </div>

                        @if ($availableSubmissions->count() > 0)
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <p class="text-sm text-gray-600">
                                    <span x-text="selectedArticles.length"></span> article(s) selected
                                </p>
                                <div class="flex gap-3">
                                    <button type="button" @click="showAddArticleModal = false"
                                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" :disabled="selectedArticles.length === 0"
                                        :class="{ 'opacity-50 cursor-not-allowed': selectedArticles.length === 0 }"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                                        Add Selected
                                    </button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
