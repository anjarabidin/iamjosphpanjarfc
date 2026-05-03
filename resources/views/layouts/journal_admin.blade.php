<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Journal Manager')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
                    boxShadow: {
                        'subtle': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>

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
        // Fallback if not set (should be set by middleware/controller)
        $journalSlug = $journal ? $journal->slug : request()->route('journal');

        // Helper specifically for this view to avoid clutter
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
        class="fixed top-0 left-0 z-50 h-full bg-white border-r border-gray-200 flex flex-col transition-all duration-300 ease-in-out"
        :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full lg:translate-x-0': !sidebarOpen,
            'w-64': !sidebarCollapsed,
            'w-20': sidebarCollapsed
        }">

        <!-- 1. Journal Context Switcher -->
        <div class="h-16 px-4 flex items-center border-b border-gray-100 relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors text-left group">
                <!-- Logo -->
                <div
                    class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center flex-shrink-0 font-bold shadow-sm group-hover:bg-indigo-700 transition-colors">
                    {{ $journal ? strtoupper(substr($journal->abbreviation ?? $journal->name, 0, 2)) : 'JM' }}
                </div>

                <!-- Text (Hidden when collapsed) -->
                <div class="min-w-0 flex-1" x-show="!sidebarCollapsed"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100">
                    <p class="text-sm font-semibold text-gray-900 truncate">
                        {{ $journal->abbreviation ?? Str::limit($journal->name ?? 'Journal', 15) }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide font-medium truncate">Switch Journal</p>
                </div>

                <!-- Icon -->
                <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-auto" x-show="!sidebarCollapsed"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute top-14 left-4 right-4 z-50 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-1"
                :class="sidebarCollapsed ? 'left-16 top-2' : 'left-4 right-4 top-14'">

                <div class="px-3 py-2 border-b border-gray-50 bg-gray-50/50">
                    <span class="text-xs font-semibold text-gray-500 uppercase">My Journals</span>
                </div>

                @php
                    $userJournals = \App\Models\JournalUserRole::getUserJournals(auth()->user());
                    // Fallback: if user has no registered journals but is viewing a journal, show current
                    if ($userJournals->isEmpty() && $journal) {
                        $userJournals = collect([$journal]);
                    }
                @endphp

                <div class="max-h-60 overflow-y-auto">
                    @forelse ($userJournals as $j)
                        <a href="{{ route('journal.dashboard', ['journal' => $j->slug]) }}"
                            class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 {{ $journal && $journal->id == $j->id ? 'bg-indigo-50/50' : '' }}">
                            <div
                                class="w-6 h-6 rounded flex-shrink-0 flex items-center justify-center text-[10px] font-bold {{ $journal && $journal->id == $j->id ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ strtoupper(substr($j->abbreviation ?? $j->name, 0, 2)) }}
                            </div>
                            <span
                                class="text-sm text-gray-700 truncate {{ $journal && $journal->id == $j->id ? 'font-medium text-indigo-900' : '' }}">{{ $j->name }}</span>
                            @if ($journal && $journal->id == $j->id)
                                <i class="fa-solid fa-check text-indigo-600 text-xs ml-auto"></i>
                            @endif
                        </a>
                    @empty
                        <div class="px-4 py-3 text-center">
                            <p class="text-xs text-gray-500">No journals yet</p>
                            <a href="{{ route('register') }}" class="text-xs text-indigo-600 hover:underline">Join a journal</a>
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-gray-100 pt-1 mt-1">
                    <a href="{{ route('journal.select') }}"
                        class="block px-4 py-2 text-xs text-gray-500 hover:text-indigo-600 hover:bg-gray-50">
                        <i class="fa-solid fa-grid-2 text-gray-400 mr-2"></i> View All Journals
                    </a>
                </div>
            </div>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false"
                class="lg:hidden absolute right-4 top-5 p-1 text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- 2. Main Navigation -->
        <nav class="flex-1 px-3 py-6 space-y-6 overflow-y-auto sidebar-scroll">

            <!-- Group: Workflow -->
            <div class="space-y-1">
                <div class="px-3 mb-2" x-show="!sidebarCollapsed">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Workflow</span>
                </div>

                <!-- Dashboard -->
                <a href="{{ route('journal.dashboard', ['journal' => $journalSlug]) }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative
                   {{ request()->routeIs('journal.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                    :title="sidebarCollapsed ? 'Dashboard' : ''">

                    @if (request()->routeIs('journal.dashboard'))
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r-full"
                            x-show="!sidebarCollapsed"></div>
                    @endif

                    <i
                        class="fa-solid fa-gauge-high w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity">Dashboard</span>
                </a>

                <!-- Submissions -->
                <div x-data="{ expanded: {{ request()->routeIs('journal.submissions.*') ? 'true' : 'false' }} }">
                    <button @click="expanded = !expanded"
                        class="w-full group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                        :title="sidebarCollapsed ? 'Submissions' : ''">
                        <div class="flex items-center gap-3">
                            <i
                                class="fa-solid fa-inbox w-5 text-center transition-transform group-hover:scale-110 text-gray-400 group-hover:text-gray-600"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Submissions</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200"
                            :class="{ 'rotate-180': expanded }" x-show="!sidebarCollapsed"></i>
                    </button>

                    <div x-show="expanded && !sidebarCollapsed" x-collapse>
                        <div class="pl-10 pr-2 py-1 space-y-1">
                            <a href="{{ route('journal.submissions.index', ['journal' => $journalSlug]) }}?filter=queue"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md {{ request('filter') == 'queue' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">My
                                Queue</a>
                            <a href="{{ route('journal.submissions.index', ['journal' => $journalSlug]) }}?filter=unassigned"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md {{ request('filter') == 'unassigned' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">Unassigned</a>
                            <a href="{{ route('journal.submissions.index', ['journal' => $journalSlug]) }}"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md {{ !request('filter') && request()->routeIs('journal.submissions.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">All
                                Active</a>
                            <a href="{{ route('journal.submissions.index', ['journal' => $journalSlug]) }}?filter=archives"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md {{ request('filter') == 'archives' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">Archives</a>
                        </div>
                    </div>
                </div>

                <!-- Issues -->
                <div x-data="{ expanded: {{ request()->routeIs('journal.issues.*') ? 'true' : 'false' }} }">
                    <button @click="expanded = !expanded"
                        class="w-full group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                        :title="sidebarCollapsed ? 'Issues' : ''">
                        <div class="flex items-center gap-3">
                            <i
                                class="fa-solid fa-layer-group w-5 text-center transition-transform group-hover:scale-110 text-gray-400 group-hover:text-gray-600"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Issues</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200"
                            :class="{ 'rotate-180': expanded }" x-show="!sidebarCollapsed"></i>
                    </button>

                    <div x-show="expanded && !sidebarCollapsed" x-collapse>
                        <div class="pl-10 pr-2 py-1 space-y-1">
                            <a href="{{ route('journal.issues.index', ['journal' => $journalSlug]) }}?status=future"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md {{ request('status') == 'future' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">Future
                                Issues</a>
                            <a href="{{ route('journal.issues.index', ['journal' => $journalSlug]) }}?status=published"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md {{ request('status') == 'published' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">Back
                                Issues</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group: Management -->
            <div class="space-y-1">
                <div class="px-3 mb-2 mt-6" x-show="!sidebarCollapsed">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Setting</span>
                </div>

                <!-- Users & Roles -->
                <a href="{{ route($usersRoutePrefix . '.index', ['journal' => $journalSlug]) }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative
                   {{ request()->routeIs($usersRoutePrefix . '.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                    :title="sidebarCollapsed ? 'Users & Roles' : ''">

                    @if (request()->routeIs($usersRoutePrefix . '.*'))
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r-full"
                            x-show="!sidebarCollapsed"></div>
                    @endif

                    <i
                        class="fa-solid fa-users w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs($usersRoutePrefix . '.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity">Users & Roles</span>
                </a>

                <!-- Settings -->
                <div x-data="{ expanded: {{ request()->routeIs('journal.settings.*') ? 'true' : 'false' }} }">
                    <button @click="expanded = !expanded"
                        class="w-full group flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative {{ request()->routeIs('journal.settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                        :title="sidebarCollapsed ? 'Settings' : ''">
                        <div class="flex items-center gap-3">
                            <i
                                class="fa-solid fa-gear w-5 text-center transition-transform group-hover:scale-110 {{ request()->routeIs('journal.settings.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Settings</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200"
                            :class="{ 'rotate-180': expanded }" x-show="!sidebarCollapsed"></i>
                    </button>

                    <div x-show="expanded && !sidebarCollapsed" x-collapse>
                        <div class="pl-10 pr-2 py-1 space-y-1">
                            <a href="{{ route('journal.settings.index', ['journal' => $journalSlug]) }}"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('journal.settings.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">Journal</a>
                            <a href="#"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100">Website</a>
                            <a href="#"
                                class="block px-2 py-1.5 text-xs font-medium rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100">Workflow</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 3. Bottom Controls -->
        <div class="p-4 border-t border-gray-100 bg-white">
            <!-- User Profile -->
            <div class="flex items-center gap-3 mb-2" :class="sidebarCollapsed ? 'justify-center' : ''">
                <div
                    class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-semibold text-xs flex-shrink-0 cursor-pointer hover:ring-2 hover:ring-indigo-100 transition-all">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1 overflow-hidden" x-show="!sidebarCollapsed">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->username ?? 'admin' }}</p>
                </div>

                <!-- Logout/Action (Icon only) -->
                <form action="{{ route('logout') }}" method="POST" x-show="!sidebarCollapsed">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1"
                        title="Sign Out">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>

            <!-- Sidebar Toggle -->
            <button @click="toggleSidebar()"
                class="w-full flex items-center justify-center p-2 rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-colors border border-dashed border-transparent hover:border-gray-200 mt-2">
                <i class="fa-solid" :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="min-h-screen transition-all duration-300 ease-in-out"
        :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">

        <!-- Mobile Header -->
        <header
            class="lg:hidden sticky top-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center px-4 gap-4 justify-between">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fa-solid fa-bars text-gray-600 text-xl"></i>
                </button>
                <span class="font-semibold text-gray-900">{{ $journal->abbreviation ?? 'JM' }} Dashboard</span>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

    <!-- Flash Messages (Reused from existing) -->
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

    @stack('scripts')
</body>

</html>
