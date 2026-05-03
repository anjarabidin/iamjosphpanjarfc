@php
    $journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">User Management</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="mt-1 text-sm text-gray-500">Manage users and their roles.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('journal.admin.users.create', ['journal' => $journal->slug]) }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add User
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <input type="text" placeholder="Search users..."
                    class="pl-10 pr-4 py-2 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <select class="rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option>All Roles</option>
                <option>Admin</option>
                <option>Editor</option>
                <option>Reviewer</option>
                <option>Author</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined
                    </th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        No users found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</x-app-layout>
