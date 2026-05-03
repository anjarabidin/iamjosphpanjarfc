@extends('layouts.app')

@php
    $currentJournal = current_journal();
@endphp

@section('title', 'Dashboard - ' . ($currentJournal?->abbreviation ?? 'IAMJOS'))

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome back! Viewing <span
                    class="font-semibold text-indigo-600">{{ $currentJournal?->abbreviation ?? $currentJournal?->name }}</span>
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('journal.submissions.create', ['journal' => $currentJournal?->slug]) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Submission
            </a>
        </div>
    </div>

    <!-- Welcome Card -->

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stat Card: Total Submissions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Submissions</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $submissionStats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center border border-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500">Your submissions</span>
            </div>
        </div>

        <!-- Stat Card: In Review -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">In Review</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $submissionStats['in_review'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center border border-amber-100">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500">Pending review</span>
            </div>
        </div>

        <!-- Stat Card: Published -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Published</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $submissionStats['published'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center border border-emerald-100">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500">Total articles</span>
            </div>
        </div>

        <!-- Stat Card: Rejected -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Rejected</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $submissionStats['rejected'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center border border-red-100">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500">This year</span>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Submissions -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Submissions</h3>
                    <a href="{{ route('journal.submissions.index', ['journal' => $currentJournal?->slug]) }}"
                        class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        View all &rarr;
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if ($recentSubmissions->count() > 0)
                    <div class="space-y-4">
                        @foreach ($recentSubmissions as $submission)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $submission->title }}</h4>
                                    <p class="text-sm text-gray-500">Submitted on
                                        {{ $submission->created_at->format('M d, Y') }}</p>
                                </div>
                                @php
                                    $statusColors = [
                                        'gray' => 'bg-gray-100 text-gray-800',
                                        'blue' => 'bg-blue-100 text-blue-800',
                                        'amber' => 'bg-amber-100 text-amber-800',
                                        'orange' => 'bg-orange-100 text-orange-800',
                                        'green' => 'bg-green-100 text-green-800',
                                        'cyan' => 'bg-cyan-100 text-cyan-800',
                                        'purple' => 'bg-purple-100 text-purple-800',
                                        'indigo' => 'bg-indigo-100 text-indigo-800',
                                        'red' => 'bg-red-100 text-red-800',
                                        'emerald' => 'bg-emerald-100 text-emerald-800',
                                    ];
                                    $statusClass =
                                        $statusColors[$submission->status_color] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $submission->status_label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 mb-4">No submissions yet</p>
                        <a href="{{ route('journal.submissions.create', ['journal' => $currentJournal?->slug]) }}"
                            class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create your first submission
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('journal.submissions.create', ['journal' => $currentJournal?->slug]) }}"
                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div
                        class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">New Submission</p>
                        <p class="text-xs text-gray-500">Submit a new article</p>
                    </div>
                </a>

                <a href="{{ route('journal.profile.edit', ['journal' => $currentJournal ? $currentJournal->slug : \App\Models\Journal::first()]) }}"
                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div
                        class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Edit Profile</p>
                        <p class="text-xs text-gray-500">Update your information</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div
                        class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Help Center</p>
                        <p class="text-xs text-gray-500">Get help & documentation</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
