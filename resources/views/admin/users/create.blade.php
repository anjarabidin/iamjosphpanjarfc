@php
    $journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">Add User</x-slot>

    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('journal.admin.users.index', ['journal' => $journal->slug]) }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add User</h1>
                <p class="mt-1 text-sm text-gray-500">Create a new user account.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="#">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                        <select name="role" id="role" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select Role</option>
                            <option value="author">Author</option>
                            <option value="reviewer">Reviewer</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                        <input type="password" name="password" id="password" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>
                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('journal.admin.users.index', ['journal' => $journal->slug]) }}"
                        class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
