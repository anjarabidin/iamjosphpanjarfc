@php
    $journal = current_journal();
@endphp

@extends('layouts.app')

@section('title', 'Issue Management')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-indigo-700">
                        Issue Management
                    </h1>
                    <p class="mt-1 text-gray-500">
                        Manage journal issues and publication schedule
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('journal.issues.create', ['journal' => $journal->slug]) }}"
                        class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:bg-indigo-700 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Issue
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Issues</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalIssues }}</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Published</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $publishedCount }}</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Upcoming</p>
                            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $upcomingCount }}</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Articles</p>
                            <p class="text-3xl font-bold text-purple-600 mt-1">{{ $totalArticles }}</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div x-data="{ activeTab: 'future' }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Tab Headers -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button @click="activeTab = 'future'"
                            :class="activeTab === 'future'
                                ?
                                'border-indigo-500 text-indigo-600 bg-indigo-50/50' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-all duration-200">
                            <svg class="w-5 h-5 inline-block mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Future Issues
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full"
                                :class="activeTab === 'future' ? 'bg-indigo-100 text-indigo-700' :
                                    'bg-gray-100 text-gray-600'">
                                {{ $upcomingCount }}
                            </span>
                        </button>
                        <button @click="activeTab = 'back'"
                            :class="activeTab === 'back'
                                ?
                                'border-indigo-500 text-indigo-600 bg-indigo-50/50' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-all duration-200">
                            <svg class="w-5 h-5 inline-block mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            Back Issues
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full"
                                :class="activeTab === 'back' ? 'bg-indigo-100 text-indigo-700' :
                                    'bg-gray-100 text-gray-600'">
                                {{ $publishedCount }}
                            </span>
                        </button>
                    </nav>
                </div>

                <!-- Tab Content: Future Issues -->
                <div x-show="activeTab === 'future'" x-cloak class="p-6">
                    @if ($futureIssues->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($futureIssues as $issue)
                                <div
                                    class="group bg-gradient-to-br from-white to-gray-50 rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-indigo-200 transition-all duration-300">
                                    <!-- Cover Image -->
                                    <div
                                        class="aspect-[3/4] bg-gradient-to-br from-indigo-100 to-indigo-200 relative overflow-hidden">
                                        @if ($issue->cover_path)
                                            <img src="{{ Storage::url($issue->cover_path) }}"
                                                alt="{{ $issue->display_title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div
                                                class="w-full h-full flex flex-col items-center justify-center text-indigo-600">
                                                <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <span class="text-sm mt-2 opacity-50">No Cover</span>
                                            </div>
                                        @endif

                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                <span
                                                    class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span>
                                                Upcoming
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $issue->identifier }}</h3>
                                        @if ($issue->title)
                                            <p class="text-gray-600 text-sm mb-3">{{ $issue->title }}</p>
                                        @endif

                                        <div class="flex items-center text-sm text-gray-500 mb-4">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ $issue->submissions_count }} articles
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('journal.issues.show', ['journal' => $journal->slug, 'issue' => $issue]) }}"
                                                class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium text-center hover:bg-gray-200 transition-colors">
                                                View Details
                                            </a>
                                            <form
                                                action="{{ route('journal.issues.publish', ['journal' => $journal->slug, 'issue' => $issue]) }}"
                                                method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Publish this issue? All assigned articles will also be published.')"
                                                    class="w-full px-3 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg text-sm font-medium hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-sm">
                                                    Publish
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Future Issues</h3>
                            <p class="text-gray-500 mb-6">Create a new issue to start scheduling articles for publication.
                            </p>
                            <a href="{{ route('journal.issues.create', ['journal' => $journal->slug]) }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Create First Issue
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Tab Content: Back Issues (Published) -->
                <div x-show="activeTab === 'back'" x-cloak class="p-6">
                    @if ($backIssues->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($backIssues as $issue)
                                <div
                                    class="group bg-gradient-to-br from-white to-gray-50 rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-emerald-200 transition-all duration-300">
                                    <!-- Cover Image -->
                                    <div
                                        class="aspect-[3/4] bg-gradient-to-br from-emerald-50 to-emerald-100 relative overflow-hidden">
                                        @if ($issue->cover_path)
                                            <img src="{{ Storage::url($issue->cover_path) }}"
                                                alt="{{ $issue->display_title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div
                                                class="w-full h-full flex flex-col items-center justify-center text-emerald-600">
                                                <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <span class="text-sm mt-2 opacity-50">No Cover</span>
                                            </div>
                                        @endif

                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Published
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $issue->identifier }}</h3>
                                        @if ($issue->title)
                                            <p class="text-gray-600 text-sm mb-2">{{ $issue->title }}</p>
                                        @endif

                                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ $issue->submissions_count }} articles
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $issue->published_at?->format('M d, Y') }}
                                            </span>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('journal.issues.show', ['journal' => $journal->slug, 'issue' => $issue]) }}"
                                                class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium text-center hover:bg-gray-200 transition-colors">
                                                Manage
                                            </a>
                                            <a href="{{ route('journal.public.issue', ['journal' => $journal->slug, 'issue' => $issue->seq_id]) }}"
                                                target="_blank"
                                                class="flex-1 px-3 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium text-center hover:bg-emerald-100 transition-colors">
                                                <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($backIssues->hasPages())
                            <div class="mt-8">
                                {{ $backIssues->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Published Issues</h3>
                            <p class="text-gray-500">Issues will appear here once they are published.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Handle successful operations
            @if (session('success'))
                // You can add toast notification here
            @endif
        </script>
    @endpush
@endsection
