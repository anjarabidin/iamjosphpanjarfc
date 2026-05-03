@extends('layouts.app')

@section('title', 'Site Access Options')

@section('content')
    <!-- Header -->
    <div class="mb-8">
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
                        <span class="text-sm font-medium text-indigo-600">Access</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Site Access & Registration</h1>
        <p class="text-sm text-gray-500 mt-1">Configure who can register and access this journal.</p>
    </div>

    <!-- Configuration Form -->
    <form action="{{ route($routePrefix . '.access.update', ['journal' => $journal->slug]) }}" method="POST"
        class="relative pb-20">
        @csrf

        <div class="space-y-6">
            <!-- User Registration -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-600"></i>
                    User Registration
                </h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="reg_open" name="registration_mode" type="radio" value="open"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" checked>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="reg_open" class="font-medium text-gray-700">Open Registration</label>
                            <p class="text-gray-500">Visitors can register a user account with the journal.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="reg_invite" name="registration_mode" type="radio" value="invite"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="reg_invite" class="font-medium text-gray-700">Register by Invite Only</label>
                            <p class="text-gray-500">Only users invited by a Journal Manager can register.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="reg_disable" name="registration_mode" type="radio" value="disable"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="reg_disable" class="font-medium text-gray-700">Disable Registration</label>
                            <p class="text-gray-500">No new users can register.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Registration -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-id-badge text-indigo-600"></i>
                    Role Registration
                </h2>
                <p class="text-sm text-gray-500 mb-4">Select which roles users can self-register for (if registration is
                    open).</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        class="relative flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center h-5">
                            <input id="role_reader" name="allow_roles[]" value="reader" type="checkbox"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" checked
                                disabled>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role_reader" class="font-medium text-gray-700">Reader</label>
                            <p class="text-xs text-gray-500">Default role for all registered users.</p>
                        </div>
                    </div>

                    <div
                        class="relative flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center h-5">
                            <input id="role_author" name="allow_roles[]" value="author" type="checkbox"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role_author" class="font-medium text-gray-700">Author</label>
                            <p class="text-xs text-gray-500">Allows users to submit articles.</p>
                        </div>
                    </div>

                    <div
                        class="relative flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center h-5">
                            <input id="role_reviewer" name="allow_roles[]" value="reviewer" type="checkbox"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role_reviewer" class="font-medium text-gray-700">Reviewer</label>
                            <p class="text-xs text-gray-500">Allows users to be selected for peer review.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-check-double text-indigo-600"></i>
                    Validation
                </h2>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="require_validation" name="require_validation" type="checkbox"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="require_validation" class="font-medium text-gray-700">Require email validation</label>
                        <p class="text-gray-500">Users will not be able to log in until they verify their email address.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div
            class="fixed bottom-0 left-0 right-0 z-20 bg-white border-t border-gray-200 p-4 lg:pl-[300px] shadow-custom-top backdrop-blur-sm bg-white/90">
            <div class="max-w-7xl mx-auto flex justify-end gap-3">
                <button type="button"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm hover:shadow-md transition-all">
                    Save Changes
                </button>
            </div>
        </div>
    </form>

    <style>
        .shadow-custom-top {
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05), 0 -2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        @media (min-width: 1024px) {
            .fixed.bottom-0.left-0.right-0.lg\:pl-\[300px\] {
                padding-left: calc(var(--sidebar-width) + 1.5rem);
            }
        }
    </style>
@endsection
