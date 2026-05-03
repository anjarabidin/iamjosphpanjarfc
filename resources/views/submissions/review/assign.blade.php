@extends('layouts.app')

@section('title', 'Assign Reviewer')

@section('content')
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="{{ route('journal.dashboard', ['journal' => $journal->slug]) }}"
                            class="text-gray-400 hover:text-gray-500">
                            <i class="fa-solid fa-house flex-shrink-0 h-5 w-5" aria-hidden="true"></i>
                            <span class="sr-only">Dashboard</span>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right flex-shrink-0 h-5 w-5 text-gray-300" aria-hidden="true"></i>
                        <a href="{{ route('journal.submissions.show', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                            class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Submission
                            #{{ $submission->submission_code ?? $submission->id }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right flex-shrink-0 h-5 w-5 text-gray-300" aria-hidden="true"></i>
                        <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                            aria-current="page">Assign Reviewer</a>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Assign Reviewer
                </h2>
                <p class="mt-1 text-sm text-gray-500 truncate">{{ $submission->title }}</p>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form
                    action="{{ route('journal.workflow.assign-reviewer', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                    method="POST" x-data="{
                        reviewerSearch: '',
                        isSearching: false,
                        reviewerResults: [],
                        selectedReviewer: null,
                        reviewMethod: '{{ $defaultReviewMode }}',
                        responseDueDate: '{{ $defaultResponseDate }}',
                        reviewDueDate: '{{ $defaultReviewDate }}',
                        submitting: false,
                    
                        async init() {
                            await this.searchReviewers();
                        },
                    
                        async searchReviewers() {
                            this.isSearching = true;
                            try {
                                let url = '{{ route('api.journal.reviewers', ['journal' => $journal->slug]) }}?submission_id={{ $submission->id }}';
                                if (this.reviewerSearch.length > 0) {
                                    url += `&q=${this.reviewerSearch}`;
                                }
                                const response = await fetch(url);
                                this.reviewerResults = await response.json();
                            } catch (error) {
                                console.error('Search failed:', error);
                            } finally {
                                this.isSearching = false;
                            }
                        },
                    
                        selectReviewer(reviewer) {
                            this.selectedReviewer = reviewer;
                            this.reviewerSearch = '';
                            // Keep results populating background or clear? 
                            // User wants card to show list if not searching. 
                            // But when selected, we show selected card.
                            // keeping results is fine.
                        }
                    }" @submit="submitting = true">
                    @csrf
                    <input type="hidden" name="reviewer_id" x-bind:value="selectedReviewer?.id || ''">

                    <div class="space-y-6">
                        {{-- Reviewer Selection Section --}}
                        <div class="space-y-4">
                            {{-- Search Input (Hidden when reviewer selected) --}}
                            <div x-show="!selectedReviewer">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Reviewer</label>
                                <div class="relative">
                                    <input type="text" x-model="reviewerSearch" @input.debounce.500ms="searchReviewers()"
                                        placeholder="Search by name, email, or affiliation..."
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-search text-gray-400"></i>
                                    </div>
                                    <div x-show="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fa-solid fa-spinner fa-spin text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Results Table --}}
                            <div x-show="!selectedReviewer && reviewerResults.length > 0"
                                class="border border-gray-200 rounded-md overflow-hidden">
                                <!-- List Header -->
                                <div
                                    class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="text-sm font-medium text-gray-700">Available Reviewers</h3>
                                    <span class="text-xs text-gray-500" x-text="reviewerResults.length + ' found'"></span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-left border-collapse">
                                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                                            <tr>
                                                <th class="pl-4 pr-2 py-3 w-10"></th>
                                                <th class="px-2 py-3">Reviewer</th>
                                                <th class="px-4 py-3">Stats</th>
                                                <th class="px-6 py-3 text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <template x-for="reviewer in reviewerResults" :key="reviewer.id">
                                            <tbody x-data="{ expanded: false }"
                                                class="border-b border-gray-200 last:border-0 hover:bg-slate-50 transition">
                                                <tr>
                                                    {{-- Expand Button --}}
                                                    <td class="pl-4 pr-2 py-4 align-top">
                                                        <button type="button" @click="expanded = !expanded"
                                                            class="text-slate-400 hover:text-indigo-600 transition p-1">
                                                            <i class="fa-solid fa-chevron-down transition-transform duration-200"
                                                                :class="expanded ? 'rotate-180' : ''"></i>
                                                        </button>
                                                    </td>

                                                    {{-- Name & Rating --}}
                                                    <td class="px-2 py-4 align-top">
                                                        <div class="font-bold text-slate-800 text-sm"
                                                            x-text="reviewer.name"></div>
                                                        <div class="text-xs text-slate-500 italic"
                                                            x-text="reviewer.affiliation || '-'"></div>
                                                        <div class="flex items-center mt-1">
                                                            <template x-for="i in 5">
                                                                <i class="fa-solid fa-star text-[10px]"
                                                                    :class="i <= Math.round(reviewer.avg_rating || 0) ?
                                                                        'text-yellow-400' : 'text-slate-200'"></i>
                                                            </template>
                                                            <span class="ml-1 text-[10px] text-slate-400 font-medium"
                                                                x-text="reviewer.avg_rating ? Number(reviewer.avg_rating).toFixed(1) : ''"></span>
                                                        </div>
                                                    </td>

                                                    {{-- Summary Stats --}}
                                                    <td class="px-4 py-4 align-top text-sm">
                                                        <div class="flex flex-col gap-1">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                <span x-text="reviewer.active_count"
                                                                    class="mr-1 font-bold"></span> Active
                                                            </span>
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                <span x-text="reviewer.completed_count"
                                                                    class="mr-1 font-bold"></span> Completed
                                                            </span>
                                                        </div>
                                                    </td>

                                                    {{-- Action --}}
                                                    <td class="px-6 py-4 align-top text-right">
                                                        <button type="button" @click="selectReviewer(reviewer)"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Select
                                                        </button>
                                                    </td>
                                                </tr>

                                                {{-- EXPANDED DETAILS --}}
                                                <tr x-show="expanded" x-transition style="display: none;">
                                                    <td colspan="4" class="px-6 pb-6 pt-0">
                                                        <div
                                                            class="bg-gray-50 border border-gray-200 rounded p-4 text-sm grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-8 mt-2">
                                                            <div
                                                                class="flex justify-between items-center border-b border-gray-200 pb-1">
                                                                <span class="text-gray-600">Active reviews</span>
                                                                <span class="font-bold text-gray-800"
                                                                    x-text="reviewer.active_count"></span>
                                                            </div>
                                                            <div
                                                                class="flex justify-between items-center border-b border-gray-200 pb-1">
                                                                <span class="text-gray-600">Completed reviews</span>
                                                                <span class="font-bold text-gray-800"
                                                                    x-text="reviewer.completed_count"></span>
                                                            </div>
                                                            <div
                                                                class="flex justify-between items-center border-b border-gray-200 pb-1">
                                                                <span class="text-gray-600">Declined reviews</span>
                                                                <span class="font-bold text-gray-800"
                                                                    x-text="reviewer.declined_count"></span>
                                                            </div>
                                                            <div
                                                                class="flex justify-between items-center border-b border-gray-200 pb-1">
                                                                <span class="text-gray-600">Cancelled reviews</span>
                                                                <span class="font-bold text-gray-800"
                                                                    x-text="reviewer.cancelled_count"></span>
                                                            </div>
                                                            <div
                                                                class="flex justify-between items-center border-b border-gray-200 pb-1">
                                                                <span class="text-gray-600">Days since last assigned</span>
                                                                <span class="font-bold text-gray-800"
                                                                    x-text="reviewer.days_since_last"></span>
                                                            </div>
                                                            <div
                                                                class="flex justify-between items-center border-b border-gray-200 pb-1">
                                                                <span class="text-gray-600">Avg days to complete</span>
                                                                <span class="font-bold text-gray-800"
                                                                    x-text="reviewer.avg_completion_days"></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </template>
                                    </table>
                                </div>
                            </div>

                            {{-- No Results --}}
                            {{-- No Results --}}
                            <div x-show="!selectedReviewer && !isSearching && reviewerResults.length === 0"
                                class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-gray-200 border-dashed">
                                <i class="fa-solid fa-user-slash text-2xl mb-2 text-gray-400"></i>
                                <p x-show="reviewerSearch.length > 0">No reviewers found matching "<span
                                        x-text="reviewerSearch"></span>"</p>
                                <p x-show="reviewerSearch.length === 0">No reviewers found.</p>
                            </div>

                            {{-- Selected Reviewer Display --}}
                            <template x-if="selectedReviewer">
                                <div
                                    class="bg-indigo-50 p-4 rounded-lg border border-indigo-200 flex items-start justify-between">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg border-2 border-indigo-200">
                                            <span x-text="selectedReviewer.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-sm font-bold text-indigo-900" x-text="selectedReviewer.name">
                                            </h3>
                                            <p class="text-xs text-indigo-700 mb-1" x-text="selectedReviewer.email"></p>
                                            <p class="text-xs text-indigo-600 italic"
                                                x-text="selectedReviewer.affiliation || 'No affiliation'"></p>

                                            <div class="flex gap-3 mt-2">
                                                <span class="inline-flex items-center text-xs font-medium text-indigo-800">
                                                    <i class="fa-solid fa-circle-check mr-1 text-indigo-500"></i>
                                                    <span x-text="selectedReviewer.completed_count"></span> Completed
                                                </span>
                                                <span class="inline-flex items-center text-xs font-medium text-indigo-800">
                                                    <i class="fa-solid fa-star mr-1 text-yellow-500"></i>
                                                    <span
                                                        x-text="selectedReviewer.avg_rating ? Number(selectedReviewer.avg_rating).toFixed(1) : '-'"></span>
                                                    Rating
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" @click="selectedReviewer = null; reviewerSearch = ''"
                                        class="text-indigo-400 hover:text-indigo-600 p-1">
                                        <span class="sr-only">Remove</span>
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Review Method --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Review Method</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-3xl">
                                <label
                                    class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                                    :class="reviewMethod === 'double_blind' ? 'border-indigo-500 ring-2 ring-indigo-500' :
                                        'border-gray-300'">
                                    <input type="radio" name="review_method" value="double_blind"
                                        x-model="reviewMethod" class="sr-only">
                                    <div class="flex flex-1 flex-col text-center">
                                        <i class="fa-solid fa-eye-slash text-gray-500 text-2xl mb-2"></i>
                                        <span class="block text-sm font-medium text-gray-900">Double Blind</span>
                                        <span class="mt-1 text-xs text-gray-500">Both reviewer and author differ</span>
                                    </div>
                                </label>
                                <label
                                    class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                                    :class="reviewMethod === 'blind' ? 'border-indigo-500 ring-2 ring-indigo-500' :
                                        'border-gray-300'">
                                    <input type="radio" name="review_method" value="blind" x-model="reviewMethod"
                                        class="sr-only">
                                    <div class="flex flex-1 flex-col text-center">
                                        <i class="fa-solid fa-user-secret text-gray-500 text-2xl mb-2"></i>
                                        <span class="block text-sm font-medium text-gray-900">Blind</span>
                                        <span class="mt-1 text-xs text-gray-500">Reviewer is anonymous</span>
                                    </div>
                                </label>
                                <label
                                    class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                                    :class="reviewMethod === 'open' ? 'border-indigo-500 ring-2 ring-indigo-500' :
                                        'border-gray-300'">
                                    <input type="radio" name="review_method" value="open" x-model="reviewMethod"
                                        class="sr-only">
                                    <div class="flex flex-1 flex-col text-center">
                                        <i class="fa-solid fa-eye text-gray-500 text-2xl mb-2"></i>
                                        <span class="block text-sm font-medium text-gray-900">Open</span>
                                        <span class="mt-1 text-xs text-gray-500">Identity is visible</span>
                                    </div>
                                </label>
                            </div>
                            
                            {{-- Double Blind Warning --}}
                            <div x-show="reviewMethod === 'double_blind'" x-transition 
                                class="mt-3 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-md">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fa-solid fa-triangle-exclamation text-yellow-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            <strong>Peringatan:</strong> Dalam mode Double-blind, pastikan file naskah telah disensor dari identitas penulis sebelum dikirim ke reviewer.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Due Dates --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Response Due Date</label>
                                <p class="text-xs text-gray-500 mb-2">Date by which the reviewer must accept or decline.
                                </p>
                                <input type="date" name="response_due_date" x-model="responseDueDate"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Review Due Date</label>
                                <p class="text-xs text-gray-500 mb-2">Date by which the review must be completed.</p>
                                <input type="date" name="review_due_date" x-model="reviewDueDate"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="pt-5 border-t border-gray-200 flex justify-end gap-3">
                            <a href="{{ route('journal.submissions.show', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                                Cancel
                            </a>
                            <button type="submit" :disabled="!selectedReviewer || submitting"
                                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed sm:text-sm">
                                <i class="fa-solid fa-paper-plane mr-2 mt-0.5" x-show="!submitting"></i>
                                <i class="fa-solid fa-spinner fa-spin mr-2 mt-0.5" x-show="submitting"
                                    style="display: none;"></i>
                                <span x-text="submitting ? 'Assigning...' : 'Assign Reviewer'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
