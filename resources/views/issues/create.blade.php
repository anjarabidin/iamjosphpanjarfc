@php
    $journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">Create Issue</x-slot>

    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('journal.issues.index', ['journal' => $journal->slug]) }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Issue</h1>
                <p class="mt-1 text-sm text-gray-500">Add a new volume or issue to the journal.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="#">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Volume -->
                        <div>
                            <label for="volume" class="block text-sm font-medium text-gray-700">Volume *</label>
                            <input type="number" name="volume" id="volume" min="1" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>

                        <!-- Number -->
                        <div>
                            <label for="number" class="block text-sm font-medium text-gray-700">Number *</label>
                            <input type="number" name="number" id="number" min="1" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>

                        <!-- Year -->
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700">Year *</label>
                            <input type="number" name="year" id="year" min="2000" max="2100"
                                value="{{ date('Y') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Title (Optional) -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title (Optional)</label>
                        <input type="text" name="title" id="title"
                            placeholder="e.g., Special Edition: AI in Healthcare"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Leave empty for regular issues.</p>
                    </div>

                    <!-- Cover Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                        <div
                            class="mt-1 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors">
                            <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <label class="text-sm text-primary-600 hover:text-primary-700 font-medium cursor-pointer">
                                Upload cover image
                                <input type="file" name="cover" class="hidden" accept="image/*">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('journal.issues.index', ['journal' => $journal->slug]) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Create Issue
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
