@php
    $journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">Sections</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sections</h1>
                <p class="mt-1 text-sm text-gray-500">Manage article sections/categories.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('journal.admin.sections.create', ['journal' => $journal->slug]) }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Section
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Sections List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <ul class="divide-y divide-gray-200">
            <li class="p-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-900">Original Articles</h4>
                        <p class="text-sm text-gray-500">Primary research articles</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-900">Review Articles</h4>
                        <p class="text-sm text-gray-500">Literature reviews and meta-analyses</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-900">Case Studies</h4>
                        <p class="text-sm text-gray-500">Case reports and studies</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</x-app-layout>
