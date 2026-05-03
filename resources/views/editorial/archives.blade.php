<x-app-layout>
    <x-slot name="title">Archives</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Archives</h1>
        <p class="mt-1 text-sm text-gray-500">View completed submissions (published, rejected, or withdrawn).</p>
    </x-slot>

    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No archived submissions</h3>
            <p class="text-gray-500">Completed submissions will be archived here.</p>
        </div>
    </div>
</x-app-layout>
