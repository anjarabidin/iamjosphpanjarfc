@php
    $journal = $journal ?? ((isset($attributes) ? $attributes->get('journal') : null) ?? current_journal());
    $journalSlug =
        $journalSlug ??
        ((isset($attributes) ? $attributes->get('journalSlug') : null) ?? ($journal ? $journal->slug : null));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? 'Dashboard') - {{ config('app.name', 'IAMJOS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Note: Alpine.js is loaded by Livewire via @livewireScripts to avoid multiple instances -->

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 20px;
        }

        /* Form Elements Base Styles */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        input[type="url"],
        input[type="tel"],
        input[type="search"],
        textarea,
        select {
            display: block;
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #374151;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="url"]:focus,
        input[type="tel"]:focus,
        input[type="search"]:focus,
        textarea:focus,
        select:focus {
            border-color: #6366f1;
            outline: 0;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        input::placeholder,
        textarea::placeholder {
            color: #9ca3af;
        }

        /* Checkbox and Radio */
        input[type="checkbox"],
        input[type="radio"] {
            width: 1rem;
            height: 1rem;
            color: #6366f1;
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            cursor: pointer;
        }

        input[type="radio"] {
            border-radius: 50%;
        }

        input[type="checkbox"]:checked,
        input[type="radio"]:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }

        input[type="checkbox"]:focus,
        input[type="radio"]:focus {
            outline: 0;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* File Input */
        input[type="file"] {
            font-size: 0.875rem;
        }

        /* Select Arrow */
        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Disabled State */
        input:disabled,
        textarea:disabled,
        select:disabled {
            background-color: #f3f4f6;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Button Base Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-secondary {
            background-color: #fff;
            color: #374151;
            border-color: #d1d5db;
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
        }

        .btn-success {
            background-color: #059669;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #047857;
        }

        .btn-danger {
            background-color: #dc2626;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        /* Tailwind Primary Colors fallback */
        .bg-primary-500 {
            background-color: #6366f1 !important;
        }

        .bg-primary-600 {
            background-color: #4f46e5 !important;
        }

        .bg-primary-700 {
            background-color: #4338ca !important;
        }

        .text-primary-500 {
            color: #6366f1 !important;
        }

        .text-primary-600 {
            color: #4f46e5 !important;
        }

        .text-primary-700 {
            color: #4338ca !important;
        }

        .border-primary-500 {
            border-color: #6366f1 !important;
        }

        .hover\:bg-primary-600:hover {
            background-color: #4f46e5 !important;
        }

        .hover\:bg-primary-700:hover {
            background-color: #4338ca !important;
        }

        .bg-primary-50 {
            background-color: #eef2ff !important;
        }

        .bg-primary-100 {
            background-color: #e0e7ff !important;
        }

        .text-white {
            color: #fff !important;
        }
    </style>

    @stack('styles')
</head>

<body class="h-full bg-gray-50 text-gray-900" x-data="{
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
    }
}">

    @php
        $journal = current_journal();
        $journalSlug = $journal ? $journal->slug : request()->route('journal');
        $isAdminContext = request()->routeIs('journal.admin.*');
        $usersRoutePrefix = $isAdminContext ? 'journal.admin.users' : 'journal.users';
    @endphp

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
        @click="sidebarOpen = false" x-cloak>
    </div>

    <!-- Sidebar -->
    <aside
        class="fixed top-0 left-0 z-50 h-full bg-white border-r border-gray-200 flex flex-col transition-all duration-300 ease-in-out w-64 -translate-x-full lg:translate-x-0"
        :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full lg:translate-x-0': !sidebarOpen,
            'w-64': !sidebarCollapsed,
            'w-20': sidebarCollapsed
        }">

        <!-- 1. Journal Context Switcher -->
        <div class="relative border-b border-gray-100" @click.outside="openJournalSwitcher = false" x-data="{ openJournalSwitcher: false }">
            @php
                $user = auth()->user();
                if ($user->hasRole(\App\Models\Role::ROLE_SUPERADMIN)) {
                    $userJournals = \App\Models\Journal::all();
                } else {
                    $userJournals = \App\Models\JournalUserRole::getUserJournals($user);
                }

                if ($userJournals->isEmpty() && $journal) {
                    $userJournals = collect([$journal]);
                }
            @endphp

            <div class="flex items-center justify-between px-4 py-4 bg-white relative z-10 w-full">
                <!-- Left Action: Visit Site -->
                @if ($journal)
                    <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}" target="_blank"
                        class="flex items-center gap-3 group flex-1 min-w-0" title="View Journal Homepage">
                        <div
                            class="w-10 h-10 rounded bg-indigo-600 flex items-center justify-center text-white font-bold shrink-0 transition-colors group-hover:bg-indigo-700">
                            {{ strtoupper(substr($journal->abbreviation ?? $journal->name, 0, 2)) }}
                        </div>

                        <div class="truncate" x-show="!sidebarCollapsed" x-transition>
                            <h2 class="text-sm font-bold text-gray-900 truncate group-hover:text-indigo-600 transition">
                                {{ $journal->name }}
                            </h2>
                            <span class="text-xs text-gray-500 group-hover:text-indigo-500 transition">View Site
                                &rarr;</span>
                        </div>
                    </a>
                @else
                    <div class="flex items-center gap-3 group flex-1 min-w-0" title="No Journal Selected">
                        <div
                            class="w-10 h-10 rounded bg-gray-400 flex items-center justify-center text-white font-bold shrink-0">
                            <i class="fa-solid fa-book"></i>
                        </div>

                        <div class="truncate" x-show="!sidebarCollapsed" x-transition>
                            <h2 class="text-sm font-bold text-gray-900 truncate">
                                IAMJOS
                            </h2>
                            <span class="text-xs text-gray-500">Select Journal &darr;</span>
                        </div>
                    </div>
                @endif

                <!-- Right Action: Switcher Toggle -->
                <button @click="openJournalSwitcher = !openJournalSwitcher" x-show="!sidebarCollapsed"
                    class="ml-2 p-1.5 rounded-md hover:bg-gray-100 text-gray-500 transition focus:outline-none border border-transparent hover:border-gray-200"
                    title="Switch JournalContext" :class="openJournalSwitcher ? 'bg-gray-100 text-indigo-600' : ''">
                    <svg class="w-5 h-5 transition-transform duration-200"
                        :class="openJournalSwitcher ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <!-- Dropdown Menu -->
            <div x-show="openJournalSwitcher" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-[-10px]"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-[-10px]"
                class="absolute left-2 top-[calc(100%-0.5rem)] min-w-[calc(100%-1rem)] w-max max-w-[90vw] bg-white border border-gray-200 shadow-xl rounded-xl z-[100] overflow-hidden">
                <div class="py-2">
                    <p class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                        Switch Journal
                    </p>
                    <div class="max-h-[60vh] overflow-y-auto custom-scrollbar">
                        @foreach ($userJournals as $j)
                            <a href="{{ route('journal.submissions.index', $j->slug) }}"
                                class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition flex items-center justify-between group gap-6">
                                <span class="whitespace-nowrap font-medium">{{ $j->name }}</span>
                                @if ($journal && $j->id === $journal->id)
                                    <div class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 shrink-0">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 mt-1 pt-1">
                        <a href="{{ route('journal.select') }}"
                            class="block px-4 py-3 text-sm font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors">
                            <i class="fa-solid fa-grid-2 mr-2"></i> View All Journals
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false"
                class="lg:hidden absolute right-4 top-5 p-1 text-gray-400 hover:text-gray-600 bg-white/80 rounded backdrop-blur-sm z-20">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- 2. Main Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto sidebar-scroll">

            @if ($journalSlug)
                <!-- Group: Workflow -->
                <div class="space-y-1">
                    <div class="px-3 mb-2" x-show="!sidebarCollapsed">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Workflow</span>
                    </div>

                    <!-- Submissions (Single Menu - OJS 3.3 Style, tabs are in content page) -->
                    @journalRole([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR, \App\Models\Role::LEVEL_AUTHOR], $journal->id)
                        <a href="{{ route('journal.submissions.index', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative
                            {{ request()->routeIs('journal.submissions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Submissions' : ''">

                            @if (request()->routeIs('journal.submissions.*'))
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r-full"
                                    x-show="!sidebarCollapsed"></div>
                            @endif

                            <i
                                class="fa-solid fa-inbox w-5 text-center transition-transform group-hover:scale-110 
                                {{ request()->routeIs('journal.submissions.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Submissions</span>
                        </a>
                    @endjournalRole

                    <!-- Reviewer: My Reviews -->
                    @journalPermission([\App\Models\Role::LEVEL_REVIEWER], $journal->id)
                        <a href="{{ route('journal.reviewer.index', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative
                   {{ request()->routeIs('journal.reviewer.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'My Reviews' : ''">

                            @if (request()->routeIs('journal.reviewer.*'))
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r-full"
                                    x-show="!sidebarCollapsed"></div>
                            @endif

                            <i
                                class="fa-solid fa-clipboard-check w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.reviewer.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity">My
                                Reviews</span>
                        </a>
                    @endjournalPermission

                    <!-- Issues -->
                    @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                        <a href="{{ route('journal.issues.index', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative
                        {{ request()->routeIs('journal.issues.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Issues' : ''">

                            @if (request()->routeIs('journal.issues.*'))
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-primary-600 rounded-r-full"
                                    x-show="!sidebarCollapsed"></div>
                            @endif

                            <i
                                class="fa-solid fa-layer-group w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.issues.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity">Issues</span>
                        </a>
                    @endjournalPermission

                    <!-- Announcements -->
                    @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                        <a href="{{ route('journal.announcements.index', ['journal' => $journalSlug]) }}"
                            class="w-full group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('journal.announcements.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Announcements' : ''">
                            <i
                                class="fa-solid fa-bullhorn w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.announcements.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Announcements</span>
                        </a>
                    @endjournalPermission
                </div>

                <!-- Group: Management -->
                @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                    <div class="space-y-1">
                        <div class="px-3 mb-2 mt-4" x-show="!sidebarCollapsed">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</span>
                        </div>

                        <!-- Journal -->
                        <a href="{{ route('journal.settings.index', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('journal.settings.index') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Journal' : ''">
                            <i
                                class="fa-solid fa-book w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.settings.index') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Journal</span>
                        </a>

                        <!-- Website -->
                        <a href="{{ route('journal.settings.website.edit', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('journal.settings.website*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Website' : ''">
                            <i
                                class="fa-solid fa-desktop w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.settings.website*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Website</span>
                        </a>

                        <!-- Workflow -->
                        <a href="{{ route('journal.settings.workflow.index', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('journal.settings.workflow*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Workflow' : ''">
                            <i
                                class="fa-solid fa-sliders w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.settings.workflow*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Workflow</span>
                        </a>

                        <!-- Distribution -->
                        <a href="{{ route('journal.settings.distribution.edit', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('journal.settings.distribution*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Distribution' : ''">
                            <i
                                class="fa-solid fa-globe w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.settings.distribution*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Distribution</span>
                        </a>

                        <!-- Users & Roles -->
                        <div x-data="{ expanded: {{ request()->routeIs($usersRoutePrefix . '.*') ? 'true' : 'false' }} }">
                            <button @click="expanded = !expanded"
                                class="w-full group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs($usersRoutePrefix . '.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                                :title="sidebarCollapsed ? 'Users & Roles' : ''">
                                <div class="flex items-center gap-3">
                                    <i
                                        class="fa-solid fa-users w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs($usersRoutePrefix . '.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Users & Roles</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200"
                                    :class="{ 'rotate-180': expanded }" x-show="!sidebarCollapsed"></i>
                            </button>

                            <div x-show="expanded && !sidebarCollapsed" x-collapse>
                                <div class="pl-10 pr-2 py-1 space-y-1">
                                    <a href="{{ route($usersRoutePrefix . '.index', ['journal' => $journalSlug]) }}"
                                        class="flex items-center gap-2 px-2 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs($usersRoutePrefix . '.index') || request()->routeIs($usersRoutePrefix . '.create') || request()->routeIs($usersRoutePrefix . '.edit') ? 'text-primary-700 bg-primary-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <i class="fa-solid fa-user-group w-4 text-center"></i>
                                        Users
                                    </a>
                                    <a href="{{ route($usersRoutePrefix . '.roles', ['journal' => $journalSlug]) }}"
                                        class="flex items-center gap-2 px-2 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs($usersRoutePrefix . '.roles*') ? 'text-primary-700 bg-primary-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <i class="fa-solid fa-user-tag w-4 text-center"></i>
                                        Roles
                                    </a>
                                    <a href="{{ route($usersRoutePrefix . '.access', ['journal' => $journalSlug]) }}"
                                        class="flex items-center gap-2 px-2 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs($usersRoutePrefix . '.access') ? 'text-primary-700 bg-primary-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <i class="fa-solid fa-lock w-4 text-center"></i>
                                        Site Access
                                    </a>
                                    <a href="{{ route($usersRoutePrefix . '.notify', ['journal' => $journalSlug]) }}"
                                        class="flex items-center gap-2 px-2 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs($usersRoutePrefix . '.notify') ? 'text-primary-700 bg-primary-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <i class="fa-solid fa-envelope w-4 text-center"></i>
                                        Notify Users
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="px-3 mb-2 mt-4" x-show="!sidebarCollapsed">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Statistics</span>
                        </div>
                        <a href="{{ route('journal.settings.statistics.articles', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('statistics.articles') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Articles' : ''">
                            <i
                                class="fa-solid fa-chart-line w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('statistics.articles') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Articles</span>
                        </a>
                        <a href="{{ route('journal.settings.statistics.editorial', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('statistics.editorial') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Editorial' : ''">
                            <i
                                class="fa-solid fa-users-gear w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('statistics.editorial') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Editorial</span>
                        </a>
                        <a href="{{ route('journal.settings.statistics.users', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('statistics.users') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Users' : ''">
                            <i
                                class="fa-solid fa-user-group w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('statistics.users') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Users</span>
                        </a>
                        <a href="{{ route('journal.settings.statistics.reports', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('statistics.reports') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Reports' : ''">
                            <i
                                class="fa-solid fa-file-export w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('statistics.reports') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Reports</span>
                        </a>

                        <!-- Maintenance -->
                        <div class="px-3 mb-2 mt-4" x-show="!sidebarCollapsed">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Maintenance</span>
                        </div>
                        <a href="{{ route('journal.settings.tools.index', ['journal' => $journalSlug]) }}"
                            class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('tools.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Tools' : ''">
                            <i
                                class="fa-solid fa-screwdriver-wrench w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('tools.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Tools</span>
                        </a>
                    </div>
                @endjournalPermission

                <!-- Group: Administration (Super Admin Only) -->
                @journalPermission([\App\Models\Role::LEVEL_SUPER_ADMIN], $journal->id)
                    <div class="space-y-1">
                        <div class="px-3 mb-2 mt-4" x-show="!sidebarCollapsed">
                            <span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</span>
                        </div>

                        <!-- Site Administration -->
                        <a href="{{ route('admin.site.index') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('admin.site.*') ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="sidebarCollapsed ? 'Site Administration' : ''">

                            @if (request()->routeIs('admin.site.*'))
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-red-600 rounded-r-full"
                                    x-show="!sidebarCollapsed"></div>
                            @endif

                            <i
                                class="fa-solid fa-cog w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('admin.site.*') ? 'text-red-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity">Site
                                Administration</span>
                        </a>
                    </div>
                @endjournalPermission
            @endif
        </nav>

        <!-- 3. Bottom Controls -->
        <div class="p-4 border-t border-gray-100 bg-white">
            <!-- Sidebar Toggle -->
            <button @click="toggleSidebar()"
                class="w-full flex items-center justify-center p-2 rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-colors border border-dashed border-transparent hover:border-gray-200">
                <i class="fa-solid" :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="min-h-screen transition-all duration-300 ease-in-out flex flex-col lg:ml-64"
        :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">

        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

                <!-- Left: Mobile Menu Button + Breadcrumb -->
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = true"
                        class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Logo (visible on mobile when sidebar is hidden) -->
                    <a href="{{ $journalSlug ? route('journal.submissions.index', ['journal' => $journalSlug]) : url('/') }}"
                        class="lg:hidden ml-2 flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="font-bold text-lg text-gray-900">{{ $journal->abbreviation ?? 'IAMJOS' }}</span>
                    </a>

                    <!-- Breadcrumb (desktop) -->
                    <nav class="hidden lg:flex items-center space-x-2 text-sm">
                        <a href="{{ $journalSlug ? route('journal.submissions.index', ['journal' => $journalSlug]) : url('/') }}"
                            class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </a>
                        <span class="text-gray-300">/</span>
                        <span class="text-gray-700 font-medium">{{ $journal->name ?? 'Journal' }}</span>
                    </nav>
                </div>

                <!-- Right: Notifications + User Dropdown -->
                <div class="flex items-center space-x-4">

                    <!-- Notifications Dropdown -->
                    <div x-data="{
                        open: false,
                        notifications: [],
                        unreadCount: 0,
                        isLoading: false,
                    
                        async fetchNotifications() {
                            this.isLoading = true;
                            try {
                                const res = await fetch('{{ route('notifications.index') }}', {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                const data = await res.json();
                                this.notifications = data.notifications;
                                this.unreadCount = data.unread_count;
                            } catch (e) {
                                console.error('Failed to fetch notifications:', e);
                            }
                            this.isLoading = false;
                        },
                    
                        async markAsRead(id) {
                            try {
                                await fetch(`/notifications/${id}/mark-read`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Content-Type': 'application/json'
                                    }
                                });
                                const notif = this.notifications.find(n => n.id === id);
                                if (notif) notif.read_at = new Date().toISOString();
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                            } catch (e) {
                                console.error('Failed to mark notification as read:', e);
                            }
                        },
                    
                        async markAllAsRead() {
                            try {
                                await fetch('{{ route('notifications.mark-all-read') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Content-Type': 'application/json'
                                    }
                                });
                                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                                this.unreadCount = 0;
                            } catch (e) {
                                console.error('Failed to mark all as read:', e);
                            }
                        },
                    
                        getIcon(type) {
                            const icons = {
                                'new_submission': 'fa-file-circle-plus',
                                'editor_assignment': 'fa-user-tie',
                                'review_invitation': 'fa-clipboard-check',
                                'review_completed': 'fa-check-circle',
                                'new_discussion_message': 'fa-comments',
                                'submission_decision': 'fa-gavel',
                                'submission_received': 'fa-inbox',
                                'default': 'fa-bell'
                            };
                            return icons[type] || icons['default'];
                        },
                    
                        getIconColor(type) {
                            const colors = {
                                'new_submission': 'text-indigo-500',
                                'editor_assignment': 'text-purple-500',
                                'review_invitation': 'text-blue-500',
                                'review_completed': 'text-emerald-500',
                                'new_discussion_message': 'text-blue-500',
                                'submission_decision': 'text-amber-500',
                                'submission_received': 'text-emerald-500',
                                'default': 'text-gray-500'
                            };
                            return colors[type] || colors['default'];
                        }
                    }" x-init="fetchNotifications()" class="relative">
                        <!-- Bell Button -->
                        <button @click="open = !open; if(open) fetchNotifications()"
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg relative transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Notification Badge -->
                            <span x-show="unreadCount > 0" x-cloak
                                class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full animate-pulse">
                                <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                            </span>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="open" @click.away="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">

                            <!-- Header -->
                            <div
                                class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900">
                                    <i class="fa-solid fa-bell text-gray-400 mr-2"></i>Notifications
                                </h3>
                                <button x-show="unreadCount > 0" @click="markAllAsRead()"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                    Mark all as read
                                </button>
                            </div>

                            <!-- Notification List -->
                            <div class="max-h-80 overflow-y-auto">
                                <!-- Loading State -->
                                <template x-if="isLoading">
                                    <div class="px-4 py-8 text-center">
                                        <i class="fa-solid fa-spinner fa-spin text-gray-400 text-xl"></i>
                                        <p class="text-sm text-gray-500 mt-2">Loading notifications...</p>
                                    </div>
                                </template>

                                <!-- Empty State -->
                                <template x-if="!isLoading && notifications.length === 0">
                                    <div class="px-4 py-8 text-center">
                                        <i class="fa-solid fa-bell-slash text-gray-300 text-3xl"></i>
                                        <p class="text-sm text-gray-500 mt-2">No notifications yet</p>
                                    </div>
                                </template>

                                <!-- Notification Items -->
                                <template x-if="!isLoading && notifications.length > 0">
                                    <ul class="divide-y divide-gray-100">
                                        <template x-for="notif in notifications" :key="notif.id">
                                            <li>
                                                <a :href="`/notifications/${notif.id}/read`"
                                                    class="block px-4 py-3 hover:bg-gray-50 transition-colors"
                                                    :class="{
                                                        'bg-blue-50 border-l-4 border-blue-500': !notif
                                                            .read_at,
                                                        'bg-white': notif.read_at
                                                    }">
                                                    <div class="flex items-start gap-3">
                                                        <!-- Icon -->
                                                        <div class="flex-shrink-0 mt-0.5">
                                                            <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                                                :class="notif.read_at ? 'bg-gray-100' : 'bg-indigo-100'">
                                                                <i class="fa-solid"
                                                                    :class="[notif.icon || getIcon(notif.type), notif.read_at ?
                                                                        'text-gray-400' : getIconColor(notif.type)
                                                                    ]"></i>
                                                            </div>
                                                        </div>
                                                        <!-- Content -->
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-semibold"
                                                                :class="notif.read_at ? 'text-gray-600' : 'text-gray-900'"
                                                                x-text="notif.title || 'Notification'"></p>
                                                            <p class="text-xs mt-0.5 line-clamp-2"
                                                                :class="notif.read_at ? 'text-gray-500' : 'text-gray-700'"
                                                                x-text="notif.message"></p>
                                                            <p class="text-xs text-gray-400 mt-1"
                                                                x-text="notif.created_at"></p>
                                                        </div>
                                                        <!-- Unread Indicator -->
                                                        <div x-show="!notif.read_at" class="flex-shrink-0">
                                                            <span
                                                                class="w-2 h-2 bg-blue-500 rounded-full block"></span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                            </div>

                            <!-- Footer -->
                            <div
                                class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    <span x-text="notifications.length"></span> recent
                                </span>
                                <a href="{{ route('notifications.index') }}"
                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                    View All →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-3 p-1.5 rounded-full hover:bg-gray-50 border border-transparent hover:border-gray-200 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">

                            <!-- Avatar -->
                            @if (session()->has('impersonator_id'))
                                {{-- Impersonation Mode: Two Icons --}}
                                <div class="relative w-11 h-9">
                                    {{-- Icon 1: The "Mask" / Admin behind --}}
                                    <div
                                        class="absolute top-0 left-0 w-7 h-7 rounded-full bg-gray-800 border-2 border-white flex items-center justify-center text-gray-200 shadow-sm z-10">
                                        <i class="fa-solid fa-user-secret text-[10px]"></i>
                                    </div>

                                    {{-- Icon 2: The User being impersonated (front) --}}
                                    <div
                                        class="absolute bottom-0 right-0 w-7 h-7 rounded-full overflow-hidden border-2 border-white shadow-md z-20">
                                        @if (Auth::user()->profile_photo_path)
                                            <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}"
                                                alt="{{ Auth::user()->name }}"
                                                class="w-full h-full object-cover bg-gray-100"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div
                                                class="hidden w-full h-full bg-primary-600 flex items-center justify-center text-white font-bold text-[10px]">
                                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @else
                                            <div
                                                class="w-full h-full bg-primary-600 flex items-center justify-center text-white font-bold text-[10px]">
                                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                {{-- Standard Mode: Single Avatar --}}
                                <div
                                    class="relative w-9 h-9 rounded-full overflow-hidden shadow-sm ring-2 ring-white group-hover:ring-gray-200 transition-all">
                                    @if (Auth::user()->profile_photo_path)
                                        <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}"
                                            alt="{{ Auth::user()->name }}"
                                            class="w-full h-full object-cover bg-gray-100"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <!-- Fallback if Image Fails to Load -->
                                        <div
                                            class="hidden absolute inset-0 bg-primary-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @else
                                        <div
                                            class="w-full h-full bg-primary-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Name & Role (Hidden on mobile) -->
                            <div class="hidden sm:block text-left pr-2">
                                <p class="text-sm font-semibold text-gray-900 leading-tight">
                                    {{ Auth::user()->name ?? 'User' }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span
                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wide">
                                        {{ Auth::user()->primary_role_label }}
                                    </span>
                                </div>
                            </div>

                            <!-- Chevron -->
                            <svg class="w-4 h-4 text-gray-400 sm:ml-1" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 py-1 z-50">
                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? '' }}</p>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-1">
                                <a href="{{ route('journal.profile.edit', request()->route('journal') ?? \App\Models\Journal::first()) }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    My Profile
                                </a>
                                @journalPermission([\App\Models\Role::LEVEL_SUPER_ADMIN], $journal->id)
                                    <a href="{{ route('admin.site.index') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Administration
                                    </a>
                                @endjournalPermission
                            </div>

                            <!-- Logout / Stop Impersonating -->
                            <div class="border-t border-gray-100 py-1">
                                @if (session()->has('impersonator_id'))
                                    {{-- STOP IMPERSONATING (OJS 3.3 Style) --}}
                                    <div class="px-4 py-2 bg-amber-50 border-b border-amber-100">
                                        <p class="text-xs font-medium text-amber-800 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Viewing as {{ Auth::user()->name }}
                                        </p>
                                    </div>
                                    <form method="POST"
                                        action="{{ route('journal.users.stop-impersonating', current_journal()->slug ?? 'default') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Logout as {{ Auth::user()->name }}
                                        </button>
                                    </form>
                                @else
                                    {{-- STANDARD LOGOUT --}}
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Sign Out
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 p-6 lg:p-8">
            <!-- Page Header (for x-slot) -->
            @isset($header)
                <div class="mb-6">
                    {{ $header }}
                </div>
            @endisset

            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-6 mt-auto">
            <p class="text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'IAMJOS') }}.
            </p>
        </footer>
    </main>

    <!-- Flash Messages -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-6 right-6 z-50">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 shadow-lg flex items-center gap-3">
                <i class="fa-solid fa-check-circle text-emerald-600"></i>
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-6 right-6 z-50">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 shadow-lg flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-600"></i>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                <button @click="show = false" class="text-red-500 hover:text-red-700"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    @endif


    <script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
    @livewireScripts
    @stack('scripts')

</body>

</html>
