@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Edit Role: {{ $role->name }}</h1>
    <p class="text-sm text-gray-500 mt-1">Update properties, permission levels, and workflow access.</p>
</div>

@php
// Infer mock values from Role Name since we don't have DB columns yet
$roleName = $role->name;

$currentLevel = $role->permission_level ?? 6; // Default to Reader (6) if null

$currentColor = match (true) {
    str_contains($roleName, 'Manager') || str_contains($roleName, 'Admin') => 'red',
    str_contains($roleName, 'Editor') => 'blue',
    str_contains($roleName, 'Reviewer') => 'amber',
    str_contains($roleName, 'Author') => 'green',
    default => 'gray',
};
@endphp


<form action="{{ route($routePrefix . '.roles.update', ['journal' => $journal->slug, 'role' => $role->id]) }}"
    method="POST" class="pb-24 space-y-8" x-data="{ selectedLevel: {{ $currentLevel }} }">
    @csrf
    @method('PUT')

    <!-- Section 1: Identity -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">1. Role Identity</h2>
            <p class="text-sm text-gray-500">Basic information and visual identification for this role.</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
                    <input type="text" name="name" id="name" value="{{ $role->name }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5"
                        required>
                </div>
                <div>
                    <label for="abbreviation" class="block text-sm font-medium text-gray-700">Abbreviation</label>
                    <input type="text" name="abbreviation" id="abbreviation"
                        value="{{ strtoupper(substr($role->name, 0, 2)) }}" maxlength="5"
                        class="mt-1 block w-24 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5">
                    <p class="mt-1 text-xs text-gray-500">Max 5 characters.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role Color</label>
                <div class="flex flex-wrap gap-3" x-data="{ selectedColor: '{{ $currentColor }}' }">
                    <!-- Hidden Input -->
                    <input type="hidden" name="color" x-model="selectedColor">

                    @php
                    $colors = [
                    'red' => 'bg-red-500',
                    'orange' => 'bg-orange-500',
                    'amber' => 'bg-amber-500',
                    'green' => 'bg-emerald-500',
                    'teal' => 'bg-teal-500',
                    'blue' => 'bg-blue-500',
                    'indigo' => 'bg-indigo-500',
                    'purple' => 'bg-purple-500',
                    'pink' => 'bg-pink-500',
                    'gray' => 'bg-gray-500',
                    ];
                    @endphp

                    @foreach ($colors as $name => $class)
                    <button type="button" @click="selectedColor = '{{ $name }}'"
                        class="w-10 h-10 rounded-full {{ $class }} flex items-center justify-center transition-transform hover:scale-110 focus:outline-none ring-offset-2"
                        :class="{ 'ring-2 ring-gray-400': selectedColor === '{{ $name }}' }">
                        <i x-show="selectedColor === '{{ $name }}'"
                            class="fa-solid fa-check text-white text-sm"></i>
                    </button>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500">Used for badges and identification in the grid.</p>
            </div>
        </div>
    </div>

    <!-- Section 2: Permission Level -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">2. Permission Level</h2>
            <p class="text-sm text-gray-500">Determines the core capabilities and access scope of this role.</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $levels = [
            [
            'val' => 1,
            'label' => 'Journal Manager',
            'desc' => 'Full access to all settings, users, and submissions within this journal.',
            'icon' => 'fa-screwdriver-wrench',
            ],
            [
            'val' => 2,
            'label' => 'Section Editor',
            'desc' => 'Can edit assigned submissions and make editorial decisions.',
            'icon' => 'fa-pen-to-square',
            ],
            [
            'val' => 3,
            'label' => 'Assistant',
            'desc' => 'Restricted access. Can only work on specific workflow stages of assigned items.',
            'icon' => 'fa-hand-holding-hand',
            ],
            [
            'val' => 4,
            'label' => 'Reviewer',
            'desc' => 'Can only access and perform reviews on assigned submissions.',
            'icon' => 'fa-magnifying-glass',
            ],
            [
            'val' => 5,
            'label' => 'Author',
            'desc' => 'Can submit articles and track their own progress only.',
            'icon' => 'fa-user-pen',
            ],
            [
            'val' => 6,
            'label' => 'Reader',
            'desc' => 'Read-only access to published content.',
            'icon' => 'fa-book-open',
            ],
            ];
            @endphp

            @foreach ($levels as $level)
            <div class="relative flex flex-col h-full">
                <label class="relative flex flex-col p-4 bg-white border rounded-xl cursor-pointer hover:border-indigo-500 hover:bg-indigo-50/30 transition-all group h-full"
                    :class="selectedLevel == {{ $level['val'] }} ? 'border-indigo-600 ring-1 ring-indigo-600 bg-indigo-50/20' : 'border-gray-200'">

                    {{-- Important: x-model binds to the parent scope property 'selectedLevel' --}}
                    <input type="radio" name="permission_level" value="{{ $level['val'] }}"
                        x-model="selectedLevel" class="sr-only">

                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors"
                            :class="selectedLevel == {{ $level['val'] }} ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-500 group-hover:bg-indigo-100 group-hover:text-indigo-600'">
                            <i class="fa-solid {{ $level['icon'] }} text-lg"></i>
                        </div>
                        <span class="font-semibold text-gray-900"
                            :class="selectedLevel == {{ $level['val'] }} ? 'text-indigo-700' : ''">
                            {{ $level['label'] }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ $level['desc'] }}</p>

                    <!-- Checkmark Badge -->
                    <div class="absolute top-3 right-3" x-show="selectedLevel == {{ $level['val'] }}" x-transition>
                        <i class="fa-solid fa-circle-check text-indigo-600 text-lg"></i>
                    </div>
                </label>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Section 3: Workflow Stage Access -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">3. Workflow Stage Assignment</h2>
            <p class="text-sm text-gray-500">Configure which stages of the editorial workflow this role can access.</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <label
                    class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="stages[]" value="submission"
                            {{ $role->permit_submission ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <span class="font-medium text-gray-900">Submission</span>
                        <p class="text-gray-500 text-xs mt-0.5">Initial checks & assignment</p>
                    </div>
                </label>

                <label
                    class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="stages[]" value="review"
                            {{ $role->permit_review ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <span class="font-medium text-gray-900">Review</span>
                        <p class="text-gray-500 text-xs mt-0.5">Peer review management</p>
                    </div>
                </label>

                <label
                    class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="stages[]" value="copyediting"
                            {{ $role->permit_copyediting ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <span class="font-medium text-gray-900">Copyediting</span>
                        <p class="text-gray-500 text-xs mt-0.5">Grammar & formatting</p>
                    </div>
                </label>

                <label
                    class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="stages[]" value="production"
                            {{ $role->permit_production ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <span class="font-medium text-gray-900">Production</span>
                        <p class="text-gray-500 text-xs mt-0.5">Final galley creation</p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- Section 4: Role Options (Refactored) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-base font-bold text-slate-900 leading-6">4. Role Options</h3>
            <p class="text-sm text-slate-500 mt-1">Additional configuration for visibility and user capabilities.</p>
        </div>

        <div class="px-6 pb-2">

            {{-- OPTION 1: Self Registration --}}
            <div class="flex items-center justify-between py-5 border-b border-slate-100 group">
                <div class="flex flex-col pr-8 max-w-2xl">
                    <label for="allow_registration" class="text-sm font-semibold text-slate-800 cursor-pointer group-hover:text-indigo-600 transition-colors">
                        Allow user self-registration
                    </label>
                    <span class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Users can select this role when registering an account. Useful for Authors and Reviewers.
                    </span>
                </div>

                {{-- Select Input --}}
                <div class="flex-shrink-0 ml-4">
                    <select name="allow_registration" id="allow_registration"
                        class="block w-28 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3">
                        <option value="1" {{ $role->allow_registration ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$role->allow_registration ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>

            {{-- OPTION 2: Contributor List --}}
            <div class="flex items-center justify-between py-5 border-b border-slate-100 group">
                <div class="flex flex-col pr-8 max-w-2xl">
                    <label for="show_contributor" class="text-sm font-semibold text-slate-800 cursor-pointer group-hover:text-indigo-600 transition-colors">
                        Show role title in contributor list
                    </label>
                    <span class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Displays the role name next to the user's name in publication details.
                    </span>
                </div>

                <div class="flex-shrink-0 ml-4">
                    <select name="show_contributor" id="show_contributor"
                        class="block w-28 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3">
                        <option value="1" {{ $role->show_contributor ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$role->show_contributor ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>

            {{-- OPTION 3: Make Submissions --}}
            <div class="flex items-center justify-between py-5 group">
                <div class="flex flex-col pr-8 max-w-2xl">
                    <label for="allow_submission" class="text-sm font-semibold text-slate-800 cursor-pointer group-hover:text-indigo-600 transition-colors">
                        Allow this role to make new submissions
                    </label>
                    <span class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Users with this role can start the submission wizard. Typically enabled for Authors.
                    </span>
                </div>

                <div class="flex-shrink-0 ml-4">
                    <select name="allow_submission" id="allow_submission"
                        class="block w-28 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3">
                        <option value="1" {{ $role->allow_submission ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$role->allow_submission ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
        <a href="{{ route($routePrefix . '.roles', ['journal' => $journal->slug]) }}"
            class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
            Cancel
        </a>
        <button type="submit"
            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium shadow-sm transition-colors flex items-center gap-2">
            <i class="fa-solid fa-save"></i>
            Save Role
        </button>
    </div>
</form>
@endsection