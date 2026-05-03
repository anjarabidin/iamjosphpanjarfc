@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <span class="text-gray-400 text-sm font-medium">Journal Manager</span>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-gray-500">Users & Roles</span>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-indigo-600">Users</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Users</h1>
            <p class="text-sm text-gray-500 mt-1">Manage users enrolled in <strong>{{ $journal->name }}</strong>.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route($routePrefix . '.enroll', ['journal' => $journal->slug]) }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors shadow-sm">
                <i class="fa-solid fa-user-plus"></i>
                Enroll Existing User
            </a>
            <a href="{{ route($routePrefix . '.create', ['journal' => $journal->slug]) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors shadow-sm shadow-indigo-200">
                <i class="fa-solid fa-plus"></i>
                Create New User
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
        <form action="{{ route($routePrefix . '.index', ['journal' => $journal->slug]) }}" method="GET"
            class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors"
                    placeholder="Search by name, username, or email...">
            </div>

            <div class="relative min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-filter text-gray-400"></i>
                </div>
                <select name="role"
                    class="block w-full pl-10 pr-10 py-2 border border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white appearance-none cursor-pointer"
                    onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    @foreach ($roles as $roleName)
                        <option value="{{ $roleName }}" {{ request('role') == $roleName ? 'selected' : '' }}>
                            {{ $roleName }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name /
                            Username</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Roles
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">@
                                            {{ $user->username ?? Str::slug($user->name) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                @if ($user->email_verified_at)
                                    <span
                                        class="text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Verified</span>
                                @else
                                    <span
                                        class="text-[10px] text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">Unverified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @php
                                        // Use per-journal roles from controller (journal_roles attribute)
                                        $userRoles = $user->journal_roles->pluck('name')->toArray();
                                        $isSuperAdmin = in_array('Super Admin', $userRoles);
                                        if (empty($userRoles)) {
                                            $userRoles = ['Reader'];
                                        }
                                    @endphp

                                    @foreach ($userRoles as $role)
                                        @php
                                            $badgeClass = match ($role) {
                                                'Super Admin'
                                                    => 'bg-purple-100 text-purple-800 border-purple-200 ring-1 ring-purple-500/20',
                                                'Admin', 'Journal Manager' => 'bg-red-50 text-red-700 border-red-100',
                                                'Editor',
                                                'Section Editor'
                                                    => 'bg-blue-50 text-blue-700 border-blue-100',
                                                'Reviewer' => 'bg-amber-50 text-amber-700 border-amber-100',
                                                'Author' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                'Reader' => 'bg-gray-50 text-gray-600 border-gray-100',
                                                default => 'bg-gray-50 text-gray-600 border-gray-100',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $badgeClass }}">
                                            @if ($role === 'Super Admin')
                                                <i class="fa-solid fa-shield-halved mr-1 text-[10px]"></i>
                                            @endif
                                            {{ $role }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($isSuperAdmin)
                                        {{-- Super Admin indicator --}}
                                        <span class="text-xs text-purple-600 font-medium px-2 py-1 bg-purple-50 rounded-lg">
                                            <i class="fa-solid fa-shield-halved mr-1"></i>
                                            Full Access
                                        </span>
                                    @else
                                        <!-- Login As -->
                                        <form
                                            action="{{ route($routePrefix . '.login-as', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                title="Login As User">
                                                <i class="fa-solid fa-right-to-bracket"></i>
                                            </button>
                                        </form>

                                        <!-- Email -->
                                        <form
                                            action="{{ route($routePrefix . '.email', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Email User">
                                                <i class="fa-solid fa-envelope"></i>
                                            </button>
                                        </form>

                                        <!-- Edit -->
                                        <a href="{{ route($routePrefix . '.edit', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                            class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Edit User Roles">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <!-- Merge User -->
                                        <a href="{{ route($routePrefix . '.merge', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                            class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                            title="Merge This User">
                                            <i class="fa-solid fa-code-merge"></i>
                                        </a>

                                        <!-- Remove from Journal -->
                                        <form
                                            action="{{ route($routePrefix . '.destroy', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Remove this user from {{ $journal->name }}? They will no longer have access to this journal.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Remove from Journal">
                                                <i class="fa-solid fa-user-minus"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-users-slash text-gray-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">No users enrolled in this journal</p>
                                    <p class="text-xs text-gray-500 mt-1">Enroll existing users or create new ones to get
                                        started.</p>
                                    <div class="mt-4 flex gap-3">
                                        <a href="{{ route($routePrefix . '.enroll', ['journal' => $journal->slug]) }}"
                                            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                            <i class="fa-solid fa-user-plus mr-1"></i> Enroll User
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
