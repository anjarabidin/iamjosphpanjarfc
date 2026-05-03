@extends('layouts.app')

@section('title', 'Enroll User')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <span class="text-gray-400 text-sm font-medium">Journal Manager</span>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                        <a href="{{ route($routePrefix . '.index', ['journal' => $journal->slug]) }}"
                            class="text-sm font-medium text-gray-500 hover:text-indigo-600">Users</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                        <span class="text-sm font-medium text-indigo-600">Enroll User</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Enroll Existing User</h1>
        <p class="text-sm text-gray-500 mt-1">Add an existing user to <strong>{{ $journal->name }}</strong> with specific
            roles.</p>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-4xl">
        <form action="{{ route($routePrefix . '.enroll.store', ['journal' => $journal->slug]) }}" method="POST">
            @csrf

            <div class="space-y-8">
                {{-- User Selection --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-6 border-b border-gray-100 rounded-t-xl">
                        <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <i class="fa-solid fa-user-check text-indigo-500 mr-2"></i> Select User
                        </h2>
                        <p class="text-sm text-gray-500">Search for a registered user to enroll.</p>
                    </div>

                    <div class="p-6">
                        @if ($availableUsers->isEmpty())
                            <div
                                class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800 flex items-start">
                                <i class="fa-solid fa-info-circle mt-0.5 mr-2"></i>
                                <div>
                                    <p class="font-medium">No users available.</p>
                                    <p class="mt-1">All registered users are already enrolled in this journal.</p>
                                </div>
                            </div>
                        @else
                            {{-- Searchable Dropdown with Alpine.js --}}
                            <div x-data="{
                                open: false,
                                search: '',
                                selectedId: '{{ old('user_id') }}',
                                selectedName: '',
                                users: {{ $availableUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])->toJson() }},
                                get filteredUsers() {
                                    if (this.search === '') {
                                        return this.users;
                                    }
                                    return this.users.filter(user => {
                                        return user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                            user.email.toLowerCase().includes(this.search.toLowerCase());
                                    });
                                },
                                init() {
                                    if (this.selectedId) {
                                        const user = this.users.find(u => u.id == this.selectedId);
                                        if (user) this.selectedName = user.name + ' (' + user.email + ')';
                                    }
                                }
                            }" class="relative max-w-xl">
                                <label for="user_search" class="block text-sm font-medium text-gray-700 mb-1">
                                    Search User <span class="text-red-500">*</span>
                                </label>

                                <input type="hidden" name="user_id" :value="selectedId">

                                <div class="relative">
                                    <button type="button" @click="open = !open"
                                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        aria-haspopup="listbox" :aria-expanded="open" aria-labelledby="listbox-label">
                                        <span class="block truncate"
                                            x-text="selectedName ? selectedName : '-- Select a user --'"></span>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                                        </span>
                                    </button>

                                    <div x-show="open" @click.away="open = false"
                                        class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                        style="display: none;">

                                        <div class="sticky top-0 z-10 bg-white px-3 py-2 border-b border-gray-100">
                                            <div class="relative rounded-md shadow-sm">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <i class="fa-solid fa-search text-gray-400 text-xs"></i>
                                                </div>
                                                <input type="text" x-model="search"
                                                    class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs"
                                                    placeholder="Search by name or email...">
                                            </div>
                                        </div>

                                        <template x-for="user in filteredUsers" :key="user.id">
                                            <div @click="selectedId = user.id; selectedName = user.name + ' (' + user.email + ')'; open = false"
                                                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 text-gray-900 group">
                                                <div class="flex items-center">
                                                    <span class="truncate" x-text="user.name"></span>
                                                    <span class="ml-2 truncate text-gray-500 text-xs"
                                                        x-text="'(' + user.email + ')'"></span>
                                                </div>

                                                <span x-show="selectedId == user.id"
                                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600">
                                                    <i class="fa-solid fa-check"></i>
                                                </span>
                                            </div>
                                        </template>

                                        <div x-show="filteredUsers.length === 0"
                                            class="py-2 px-3 text-sm text-gray-500 text-center">
                                            No users found.
                                        </div>
                                    </div>
                                </div>
                                @error('user_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Roles Selection --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <i class="fa-solid fa-tags text-indigo-500 mr-2"></i> Assign Roles
                        </h2>
                        <p class="text-sm text-gray-500">Pick roles for {{ $journal->name }}.</p>
                    </div>
                    <div class="p-6 bg-gray-50/30">
                        @error('roles')
                            <div class="mb-3 px-3 py-2 bg-red-50 text-red-600 text-xs rounded border border-red-200">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach ($roles as $role)
                                @php
                                    $badgeClass = match ($role->name) {
                                        'Journal Manager' => 'bg-red-50 text-red-700 border-red-200 ring-red-200',
                                        'Editor' => 'bg-blue-50 text-blue-700 border-blue-200 ring-blue-200',
                                        'Section Editor' => 'text-blue-700 border-blue-200 ring-blue-200',
                                        'Reviewer' => 'bg-amber-50 text-amber-700 border-amber-200 ring-amber-200',
                                        'Author'
                                            => 'bg-emerald-50 text-emerald-700 border-emerald-200 ring-emerald-200',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200 ring-gray-200',
                                    };
                                @endphp
                                <label
                                    class="relative flex items-center p-3 rounded-lg border border-gray-200 bg-white hover:border-indigo-300 cursor-pointer transition-all shadow-sm group hover:shadow-md">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                        {{ in_array($role->name, old('roles', ['Reader'])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">

                                    <div class="ml-3 flex-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $badgeClass }}">
                                            {{ $role->name }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @if ($roles->isEmpty())
                            <p class="text-sm text-gray-500 italic">No available roles found for this journal.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route($routePrefix . '.index', ['journal' => $journal->slug]) }}"
                    class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
                    Cancel
                </a>
                @if (!$availableUsers->isEmpty())
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors shadow-sm shadow-indigo-200">
                        <i class="fa-solid fa-user-plus mr-2"></i> Enroll User
                    </button>
                @endif
            </div>
        </form>
    </div>
@endsection
