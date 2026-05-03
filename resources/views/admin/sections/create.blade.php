@php
    $journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">Add Section</x-slot>

    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('journal.admin.sections.index', ['journal' => $journal->slug]) }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add Section</h1>
                <p class="mt-1 text-sm text-gray-500">Create a new article section.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="#">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Section Name *</label>
                        <input type="text" name="name" id="name" required
                            placeholder="e.g., Original Articles"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="abbreviation" class="block text-sm font-medium text-gray-700">Abbreviation</label>
                        <input type="text" name="abbreviation" id="abbreviation" placeholder="e.g., OA"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="policy" class="block text-sm font-medium text-gray-700">Section Policy</label>
                        <textarea name="policy" id="policy" rows="4"
                            placeholder="Describe the types of articles accepted in this section..."
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" checked
                            class="rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Active (accepts submissions)</label>
                    </div>
                </div>
                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('journal.admin.sections.index', ['journal' => $journal->slug]) }}"
                        class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Create Section
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
