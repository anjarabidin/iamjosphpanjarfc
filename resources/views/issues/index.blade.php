@php
    $journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">Issues</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Issues</h1>
                <p class="mt-1 text-sm text-gray-500">Manage journal volumes and issues.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('journal.issues.create', ['journal' => $journal->slug]) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Issue
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No issues yet</h3>
            <p class="text-gray-500 mb-6">Create your first issue to start publishing articles.</p>
            <a href="{{ route('journal.issues.create', ['journal' => $journal->slug]) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create First Issue
            </a>
        </div>
    </div>
</x-app-layout>
