<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Site Administration') | IAMJOS Admin</title>

    {{-- SEO & Meta Tags --}}
    <meta name="description" content="Site Administration for IAMJOS - Indonesian Academic Journal System">
    <meta name="robots" content="noindex, nofollow">

    {{-- Favicon --}}
    <link rel="icon" type="image/webp" href="{{ asset('assets/media/logos/logo.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/media/logos/logo.webp') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Site Administration') | IAMJOS Admin">
    <meta property="og:description" content="Site Administration for IAMJOS - Indonesian Academic Journal System">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="IAMJOS Admin">
    <meta property="og:image" content="{{ asset('assets/media/logos/logo.webp') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Site Administration') | IAMJOS Admin">
    <meta name="twitter:description" content="Site Administration for IAMJOS - Indonesian Academic Journal System">
    <meta name="twitter:image" content="{{ asset('assets/media/logos/logo.webp') }}">

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

    <!-- TinyMCE -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .sidebar {
            width: var(--sidebar-width);
        }

        .main-content {
            margin-left: var(--sidebar-width);
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="h-full bg-gray-50" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
        @click="sidebarOpen = false" x-cloak>
    </div>

    <!-- Sidebar -->
    <aside
        class="sidebar fixed top-0 left-0 z-50 h-full bg-slate-900 text-white flex flex-col transition-transform duration-300 lg:translate-x-0"
        :class="{ 'open': sidebarOpen }">

        <!-- Brand -->
        <div class="h-16 px-6 flex items-center border-b border-slate-700/50">
            <a href="{{ route('admin.site.index') }}" class="flex items-center gap-3">
                <div
                    class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-tight">IAMJOS Admin</span>
            </a>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false"
                class="lg:hidden ml-auto p-1.5 rounded-lg hover:bg-slate-700/50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Journal Switcher -->
        @php
            $userJournals = \App\Models\JournalUserRole::getUserJournals(auth()->user());
        @endphp
        @if ($userJournals->count() > 0)
            <div class="px-4 py-3 border-b border-slate-700/50" x-data="{ journalOpen: false, search: '' }">
                <button @click="journalOpen = !journalOpen"
                    class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-slate-800 transition-colors text-left group">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-sm">
                        <i class="fa-solid fa-book-open text-xs"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium">Switch Journal</p>
                        <p class="text-sm font-medium text-white truncate">{{ $userJournals->count() }} Journal(s)</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform"
                        :class="{ 'rotate-180': journalOpen }"></i>
                </button>

                <!-- Journal Dropdown -->
                <div x-show="journalOpen" x-cloak x-collapse class="mt-2">
                    <!-- Search -->
                    <div class="mb-2">
                        <input type="text" x-model="search" placeholder="Search journal..."
                            class="w-full px-3 py-1.5 text-xs bg-slate-800 border border-slate-700 rounded-md text-white placeholder-slate-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <!-- Journal List -->
                    <div class="max-h-40 overflow-y-auto space-y-1 custom-scrollbar">
                        @foreach ($userJournals as $j)
                            <a href="{{ route('journal.submissions.index', ['journal' => $j->slug]) }}"
                                x-show="search === '' || '{{ strtolower($j->name . ' ' . ($j->abbreviation ?? '')) }}'.includes(search.toLowerCase())"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors group/j">
                                <div
                                    class="w-6 h-6 rounded bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-300 group-hover/j:bg-indigo-600 group-hover/j:text-white transition-colors">
                                    {{ strtoupper(substr($j->abbreviation ?? $j->name, 0, 2)) }}
                                </div>
                                <span
                                    class="text-sm text-slate-300 truncate group-hover/j:text-white">{{ $j->name }}</span>
                            </a>
                        @endforeach
                    </div>
                    <!-- View All -->
                    <a href="{{ route('journal.select') }}"
                        class="mt-2 flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-slate-400 bg-slate-800 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                        <i class="fa-solid fa-grid-2"></i> View All Journals
                    </a>
                </div>
            </div>
        @endif

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('admin.site.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('admin.site.index') ? 'bg-indigo-500/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Hosted Journals -->
            <a href="{{ route('admin.journals.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('admin.journals.*') ? 'bg-indigo-500/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Hosted Journals</span>
            </a>

            <!-- Site Settings -->
            <a href="{{ route('admin.site.settings.form') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                       {{ request()->routeIs('admin.site.settings.form') ? 'bg-indigo-500/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Site Settings</span>
            </a>

            <!-- Site Appearance (Page Builder) -->
            <a href="{{ route('admin.site.appearance.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                       {{ request()->routeIs('admin.site.appearance.*') ? 'bg-purple-500/20 text-purple-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                </svg>
                <span>Page Builder</span>
            </a>

            <!-- Site Pages (CMS) -->
            <a href="{{ route('admin.site-pages.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                       {{ request()->routeIs('admin.site-pages.*') ? 'bg-teal-500/20 text-teal-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Site Pages</span>
            </a>

            <!-- Site Navigation -->
            <a href="{{ route('admin.site-navigation.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                       {{ request()->routeIs('admin.site-navigation.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>Site Navigation</span>
            </a>
            <!-- System & Maintenance -->
            <a href="{{ route('admin.site.system-info') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                {{ request()->routeIs('admin.site.system-info') ? 'bg-indigo-500/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
                <span>System & Maintenance</span>
            </a>

            <div class="pt-6 mt-6 border-t border-slate-700/50">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Quick Links</p>

                <!-- Back to Portal -->
                <a href="{{ route('portal.home') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-all duration-200">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Back to Public Page</span>
                </a>
            </div>
        </nav>

        <!-- User Profile -->
        <div class="p-4 border-t border-slate-700/50" x-data="{ userMenuOpen: false }"> <button
                @click="userMenuOpen = !userMenuOpen"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-colors">
                <div
                    class="w-9 h-9 bg-gradient-to-br from-slate-600 to-slate-700 rounded-full flex items-center justify-center text-sm font-semibold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 text-left">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400 truncate">Super Admin</p>
                </div> <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': userMenuOpen }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
            </button>
            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak x-transition
                class="mt-2 py-1 bg-slate-800 rounded-lg shadow-lg"> <a
                    href="{{ ($j = request()->route('journal') ?? \App\Models\Journal::first()) ? route('journal.profile.edit', $j) : '#' }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg> Profile Settings </a>
                <form action="{{ route('logout') }}" method="POST"> @csrf <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-slate-700 hover:text-red-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg> Sign Out </button> </form>
            </div>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="main-content min-h-screen">
        <!-- Top Header Bar -->
        <header
            class="sticky top-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
            <!-- Left: Mobile menu + Breadcrumb -->
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-sm text-gray-500 hidden lg:block">Site Administration</span>
            </div>

            <!-- Right: Notifications + Profile -->
            <div class="flex items-center gap-4">
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
                        <!-- Badge -->
                        <span x-show="unreadCount > 0" x-cloak
                            class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full animate-pulse">
                            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                        </span>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-100 z-50">

                        <!-- Header -->
                        <div
                            class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between rounded-t-xl">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            <button @click="markAllAsRead()" x-show="unreadCount > 0"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Mark all as read
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="max-h-80 overflow-y-auto">
                            <!-- Loading -->
                            <template x-if="isLoading">
                                <div class="p-8 text-center">
                                    <div
                                        class="animate-spin w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full mx-auto">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2">Loading...</p>
                                </div>
                            </template>

                            <!-- Empty -->
                            <template x-if="!isLoading && notifications.length === 0">
                                <div class="p-8 text-center">
                                    <i class="fa-solid fa-bell-slash text-gray-300 text-3xl"></i>
                                    <p class="text-sm text-gray-500 mt-2">No notifications</p>
                                </div>
                            </template>

                            <!-- Items -->
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
                                                    <div class="flex-shrink-0 mt-0.5">
                                                        <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                                            :class="notif.read_at ? 'bg-gray-100' : 'bg-indigo-100'">
                                                            <i class="fa-solid"
                                                                :class="[notif.icon || getIcon(notif.type), notif.read_at ?
                                                                    'text-gray-400' : getIconColor(notif.type)
                                                                ]"></i>
                                                        </div>
                                                    </div>
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
                                                    <div x-show="!notif.read_at" class="flex-shrink-0">
                                                        <span class="w-2 h-2 bg-blue-500 rounded-full block"></span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </div>

                        <!-- Footer -->
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 text-center rounded-b-xl">
                            <a href="{{ route('notifications.index') }}"
                                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown (Desktop) -->
                <div x-data="{ profileOpen: false }" class="hidden lg:block relative">
                    <button @click="profileOpen = !profileOpen"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <div
                            class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1">
                        <a href="{{ ($j = request()->route('journal') ?? \App\Models\Journal::first()) ? route('journal.profile.edit', $j) : '#' }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fa-solid fa-user text-gray-400"></i> Profile Settings
                        </a>
                        <hr class="my-1">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fa-solid fa-right-from-bracket text-red-400"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

    <!-- Flash Messages -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-6 right-6 z-50">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 shadow-lg flex items-center gap-3">
                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-6 right-6 z-50">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 shadow-lg flex items-center gap-3">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                <button @click="show = false" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @stack('scripts')
</body>

</html>
