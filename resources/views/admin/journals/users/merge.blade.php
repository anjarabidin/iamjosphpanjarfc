@extends('layouts.app')

@section('title', 'Merge Users')

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
                        <span class="text-sm font-medium text-indigo-600">Merge Users</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">
            <i class="fa-solid fa-code-merge text-purple-600 mr-2"></i>
            Merge Users
        </h1>
        <p class="text-sm text-gray-500 mt-1">Consolidate duplicate user accounts by merging all data into a single
            account.</p>
    </div>

    <!-- Warning Banner -->
    <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-r-xl mb-8 shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-base font-bold text-red-800 mb-2">
                    ⚠️ Warning: This action is PERMANENT and IRREVERSIBLE
                </h3>
                <div class="text-sm text-red-700 space-y-2">
                    <p>
                        <strong class="font-semibold">{{ $sourceUser->name }}</strong>
                        <span class="text-xs">({{ $sourceUser->email }})</span>
                        will be <strong class="font-bold">PERMANENTLY DELETED</strong> after the merge.
                    </p>
                    <p class="font-medium">All associated records will be transferred to the target user:</p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Submissions & Co-authorships</li>
                        <li>Review Assignments</li>
                        <li>Editorial Decisions & Assignments</li>
                        <li>Discussion Messages & Participants</li>
                        <li>File Uploads</li>
                        <li>Journal Roles & Permissions</li>
                        <li>Notifications</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Merge Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-8">
            <form action="{{ route($routePrefix . '.execute-merge', ['journal' => $journal->slug, 'user' => $sourceUser->id]) }}"
                method="POST" id="mergeForm">
                @csrf

                <!-- Source User (Will Be Deleted) -->
                <div class="mb-8 bg-red-50 rounded-xl p-6 border border-red-200">
                    <label class="block text-xs font-bold text-red-700 uppercase tracking-wider mb-3">
                        <i class="fa-solid fa-user-xmark mr-1"></i>
                        Source User (Will Be Deleted)
                    </label>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-16 h-16 bg-red-200 rounded-full flex items-center justify-center text-red-800 font-bold text-xl">
                            {{ strtoupper(substr($sourceUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $sourceUser->name }}</p>
                            <p class="text-sm text-gray-600">{{ $sourceUser->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if ($sourceUser->username)
                                    @{{ $sourceUser->username }} •
                                @endif
                                Registered: {{ $sourceUser->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center my-6">
                    <div class="flex items-center gap-2 text-purple-600">
                        <div class="w-8 h-0.5 bg-purple-600"></div>
                        <i class="fa-solid fa-arrow-down text-2xl"></i>
                        <div class="w-8 h-0.5 bg-purple-600"></div>
                    </div>
                </div>

                <!-- Target User (Will Keep All Data) -->
                <div class="mb-8">
                    <label for="targetUserId" class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-bullseye text-indigo-600 mr-1"></i>
                        Select Target User (Will Keep All Data)
                    </label>
                    <select id="targetUserId" name="target_user_id" required
                        class="w-full px-4 py-4 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base font-medium">
                        <option value="">-- Select Target User --</option>
                        @foreach ($potentialTargets as $target)
                            <option value="{{ $target->id }}" {{ old('target_user_id') == $target->id ? 'selected' : '' }}>
                                {{ $target->name }} ({{ $target->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('target_user_id')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Preview Selected Target -->
                <div id="targetPreview" class="mb-8 bg-indigo-50 rounded-xl p-6 border border-indigo-200 hidden">
                    <label class="block text-xs font-bold text-indigo-700 uppercase tracking-wider mb-3">
                        <i class="fa-solid fa-user-check mr-1"></i>
                        Target User (Will Keep All Data)
                    </label>
                    <div id="targetPreviewContent"></div>
                </div>

                <!-- Confirmation Text Input -->
                <div class="mb-8">
                    <label for="confirmationText" class="block text-sm font-bold text-gray-700 mb-3">
                        Type <span
                            class="px-3 py-1 bg-red-100 text-red-800 rounded-lg font-mono text-base mx-1">MERGE</span> to
                        confirm this permanent action:
                    </label>
                    <input type="text" id="confirmationText" name="confirmation_text" required
                        class="w-full px-4 py-4 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-base font-mono uppercase"
                        placeholder="Type MERGE here" autocomplete="off">
                    @error('confirmation_text')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route($routePrefix . '.index', ['journal' => $journal->slug]) }}"
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" id="mergeButton" disabled
                        class="px-8 py-3 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-red-200">
                        <i class="fa-solid fa-code-merge mr-2"></i>
                        Merge Users Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const targetSelect = document.getElementById('targetUserId');
                const confirmInput = document.getElementById('confirmationText');
                const mergeButton = document.getElementById('mergeButton');
                const targetPreview = document.getElementById('targetPreview');
                const targetPreviewContent = document.getElementById('targetPreviewContent');

                // User data for preview
                const users = @json($potentialTargets);

                // Update target preview
                targetSelect.addEventListener('change', function() {
                    const selectedId = this.value;
                    if (selectedId) {
                        const user = users.find(u => u.id == selectedId);
                        if (user) {
                            targetPreviewContent.innerHTML = `
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-indigo-200 rounded-full flex items-center justify-center text-indigo-800 font-bold text-xl">
                                        ${user.name.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-900">${user.name}</p>
                                        <p class="text-sm text-gray-600">${user.email}</p>
                                    </div>
                                </div>
                            `;
                            targetPreview.classList.remove('hidden');
                        }
                    } else {
                        targetPreview.classList.add('hidden');
                    }
                    validateForm();
                });

                // Validate confirmation text
                confirmInput.addEventListener('input', function() {
                    validateForm();
                });

                function validateForm() {
                    const targetSelected = targetSelect.value !== '';
                    const confirmationValid = confirmInput.value.toUpperCase() === 'MERGE';
                    mergeButton.disabled = !(targetSelected && confirmationValid);
                }

                // Confirm before submit
                document.getElementById('mergeForm').addEventListener('submit', function(e) {
                    if (!confirm(
                            'Are you absolutely sure? This will PERMANENTLY DELETE {{ $sourceUser->name }} and transfer all data to the selected user. This action CANNOT be undone!'
                        )) {
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush
@endsection
