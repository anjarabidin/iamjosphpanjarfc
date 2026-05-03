<x-app-layout>
    <x-slot name="title">Pending Reviews</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Pending Reviews</h1>
        <p class="mt-1 text-sm text-gray-500">Articles assigned to you for peer review.</p>
    </x-slot>

    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No pending reviews</h3>
            <p class="text-gray-500">You have no articles waiting for your review.</p>
        </div>
    </div>
</x-app-layout>
