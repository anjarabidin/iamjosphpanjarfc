{{-- Dynamic Public Navbar Component (OJS 3.3 Style) --}}
@props(['journal', 'primaryMenu' => null, 'userMenu' => null])

@php
    $primaryColor = $journal->getWebsiteSettings()['primary_color'] ?? '#0369a1';
    $secondaryColor = $journal->getWebsiteSettings()['secondary_color'] ?? '#7c3aed';

    // Use passed menu or fallback to view composer data
    $primaryMenuItems = $primaryMenu ?? ($primaryNavItems ?? collect());
    $userMenuItems = $userMenu ?? ($userNavItems ?? collect());

    // Check if menus have items
    $hasPrimaryMenu = $primaryMenuItems->isNotEmpty();
    $hasUserMenu = $userMenuItems->isNotEmpty();
@endphp

{{-- Primary Navigation Bar --}}
<nav class="sticky top-0 z-50 shadow-sm" style="background: {{ $primaryColor }};" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-12">
            {{-- Left Side: Logo & Menu --}}
            <div class="flex items-center gap-6 flex-1">

                {{-- Desktop Navigation (Primary Menu - Always Visible) --}}
                <div class="hidden md:flex items-center space-x-1">
                    {{-- Primary Menu Items (Always shown for all users) --}}
                    @if ($hasPrimaryMenu)
                        {{-- Dynamic Primary Menu from Admin Settings --}}
                        @foreach ($primaryMenuItems as $item)
                            @if ($item->is_divider ?? false)
                                <div class="w-px h-5 bg-white/20 mx-2"></div>
                            @elseif(isset($item->children) && $item->children->isNotEmpty())
                                {{-- Dropdown Menu --}}
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.outside="open = false"
                                        class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                                        @if ($item->icon ?? false)
                                            <i class="{{ $item->icon }} text-white/70 text-xs"></i>
                                        @endif
                                        {{ $item->label }}
                                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200"
                                            :class="{ 'rotate-180': open }"></i>
                                    </button>

                                    {{-- Dropdown Panel --}}
                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 -translate-y-1"
                                        class="absolute left-0 mt-1 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                                        @foreach ($item->children as $child)
                                            @if ($child->is_divider ?? false)
                                                <hr class="my-2 border-slate-100">
                                            @else
                                                <a href="{{ $child->resolved_url }}"
                                                    target="{{ $child->target ?? '_self' }}"
                                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                                    @if ($child->icon ?? false)
                                                        <i
                                                            class="{{ $child->icon }} text-slate-400 w-4 text-center"></i>
                                                    @endif
                                                    {{ $child->label }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                {{-- Regular Link --}}
                                <a href="{{ $item->resolved_url }}" target="{{ $item->target ?? '_self' }}"
                                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-colors
                                        {{ request()->url() === $item->resolved_url ? 'bg-white/15 text-white' : '' }}">
                                    @if ($item->icon ?? false)
                                        <i class="{{ $item->icon }} text-white/70 text-xs"></i>
                                    @endif
                                    {{ $item->label }}
                                </a>
                            @endif
                        @endforeach
                    @else
                        {{-- Default Primary Navigation Links --}}
                        <a href="{{ route('journal.public.home', $journal->slug) }}"
                            class="px-3 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-colors
                                {{ request()->routeIs('journal.public.home') ? 'bg-white/15 text-white' : '' }}">
                            <i class="fa-solid fa-house mr-1.5 text-xs"></i> Home
                        </a>
                        <a href="{{ route('journal.public.about', $journal->slug) }}"
                            class="px-3 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-colors
                                {{ request()->routeIs('journal.public.about') ? 'bg-white/15 text-white' : '' }}">
                            <i class="fa-solid fa-info-circle mr-1.5 text-xs"></i> About
                        </a>
                        <a href="{{ route('journal.public.current', $journal->slug) }}"
                            class="px-3 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-colors
                                {{ request()->routeIs('journal.public.current') ? 'bg-white/15 text-white' : '' }}">
                            <i class="fa-solid fa-book-open mr-1.5 text-xs"></i> Current
                        </a>
                        <a href="{{ route('journal.public.archives', $journal->slug) }}"
                            class="px-3 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-colors
                                {{ request()->routeIs('journal.public.archives') ? 'bg-white/15 text-white' : '' }}">
                            <i class="fa-solid fa-archive mr-1.5 text-xs"></i> Archives
                        </a>

                        {{-- About Dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                                <i class="fa-solid fa-ellipsis-h text-xs"></i> More
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200"
                                    :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute left-0 mt-1 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                                <a href="{{ route('journal.public.editorial-team', $journal->slug) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="fa-solid fa-users text-slate-400 w-4 text-center"></i> Editorial Team
                                </a>
                                <a href="{{ route('journal.public.author-guidelines', $journal->slug) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="fa-solid fa-file-alt text-slate-400 w-4 text-center"></i> Author
                                    Guidelines
                                </a>
                                <hr class="my-2 border-slate-100">
                                <a href="{{ route('journal.public.search', $journal->slug) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="fa-solid fa-search text-slate-400 w-4 text-center"></i> Search
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Side: User Menu & Actions --}}
            <div class="hidden md:flex items-center space-x-4">

                {{-- Search Trigger --}}
                <div x-data="{ searchOpen: false }" class="relative">
                    <button @click="searchOpen = !searchOpen; $nextTick(() => $refs.searchInput?.focus())"
                        class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <i class="fa-solid fa-search"></i>
                    </button>

                    {{-- Search Dropdown --}}
                    <div x-show="searchOpen" x-cloak @click.outside="searchOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-100 p-4 z-50">
                        <form action="{{ route('journal.public.search', $journal->slug) }}" method="GET">
                            <div class="relative">
                                <input type="text" name="q" x-ref="searchInput"
                                    placeholder="Search articles, authors..."
                                    class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-slate-400"></i>
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full mt-3 px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors"
                                style="background: {{ $primaryColor }};">
                                Search
                            </button>
                        </form>
                    </div>
                </div>

                @auth
                    {{-- OJS 3.3 Style User Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        {{-- Trigger --}}
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 text-sm text-white focus:outline-none hover:text-white/90 transition group">

                            {{-- Avatar --}}
                            <img class="h-8 w-8 rounded-full object-cover border-2 border-white/20 group-hover:border-white/40 transition"
                                src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                                alt="{{ Auth::user()->name }}" />

                            {{-- Name & Chevron --}}
                            <span
                                class="font-medium max-w-[120px] truncate hidden lg:block">{{ Auth::user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-white/70 text-xs transition-transform duration-200"
                                :class="{ 'rotate-180': open }"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50 origin-top-right">

                            {{-- Header --}}
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs text-slate-500 uppercase tracking-wider font-bold">Signed in as</p>
                                <p class="text-sm font-medium text-slate-900 truncate">{{ Auth::user()->name }}</p>
                            </div>

                            {{-- Menu Items --}}
                            <div class="py-1">
                                @if ($hasUserMenu)
                                    @foreach ($userMenuItems as $item)
                                        @if ($item->is_divider ?? false)
                                            <div class="border-t border-gray-100 my-1"></div>
                                        @else
                                            <a href="{{ $item->resolved_url }}" target="{{ $item->target ?? '_self' }}"
                                                class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                                @if ($item->icon ?? false)
                                                    <i class="{{ $item->icon }} text-slate-400 w-4 text-center"></i>
                                                @endif
                                                {{ $item->label }}
                                            </a>
                                            @if (isset($item->children) && $item->children->isNotEmpty())
                                                @foreach ($item->children as $child)
                                                    @if ($child->is_divider ?? false)
                                                        <div class="border-t border-gray-100 my-1"></div>
                                                    @else
                                                        <a href="{{ $child->resolved_url }}" target="{{ $child->target ?? '_self' }}"
                                                            class="flex items-center gap-3 px-8 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                                            @if ($child->icon ?? false)
                                                                <i class="{{ $child->icon }} text-slate-400 w-4 text-center"></i>
                                                            @endif
                                                            {{ $child->label }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <a href="{{ route('journal.submissions.index', $journal->slug) }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                        <i class="fa-solid fa-gauge-high text-slate-400 w-4 text-center"></i>
                                        Dashboard
                                    </a>

                                    <a href="{{ route('journal.profile.edit', $journal->slug) }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                        <i class="fa-solid fa-user-circle text-slate-400 w-4 text-center"></i>
                                        View Profile
                                    </a>
                                @endif
                            </div>

                            <div class="border-t border-gray-100 my-1"></div>

                            {{-- Logout --}}
                            <form method="POST"
                                action="{{ isset($journal) ? route('journal.logout', $journal->slug) : route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3">
                                    <i class="fa-solid fa-sign-out-alt text-red-400 w-4 text-center"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Guest Links --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ route('journal.login', $journal->slug) }}"
                            class="text-sm font-medium text-white/90 hover:text-white transition">
                            Login
                        </a>
                        <a href="{{ route('journal.register', $journal->slug) }}"
                            class="text-sm font-medium bg-white text-slate-900 px-4 py-2 rounded-full hover:bg-slate-100 transition shadow-sm">
                            Register
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden flex items-center space-x-3">
                <a href="{{ route('journal.submissions.create', $journal->slug) }}"
                    class="px-3 py-1.5 text-xs font-medium bg-white text-slate-800 rounded-lg shadow-sm">
                    Submit
                </a>
                <button @click="mobileOpen = !mobileOpen"
                    class="p-2 text-white hover:bg-white/10 rounded-lg transition-colors">
                    <i class="fa-solid" :class="mobileOpen ? 'fa-times' : 'fa-bars'" class="text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu Drawer --}}
    <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="md:hidden border-t border-white/10 bg-white shadow-xl">
        <div class="px-4 py-4 space-y-1">
            {{-- Search Box (Mobile) --}}
            <form action="{{ route('journal.public.search', $journal->slug) }}" method="GET" class="mb-4">
                <div class="relative">
                    <input type="text" name="q" placeholder="Search articles..."
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-lg">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-slate-400"></i>
                    </div>
                </div>
            </form>

            {{-- Mobile Menu Items --}}
            {{-- Primary Menu Items (Always shown for all users) --}}
            @if ($hasPrimaryMenu)
                {{-- Dynamic Mobile Primary Menu --}}
                @foreach ($primaryMenuItems as $item)
                    @if (!($item->is_divider ?? false))
                        <a href="{{ $item->resolved_url }}" target="{{ $item->target ?? '_self' }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                            @if ($item->icon ?? false)
                                <i class="{{ $item->icon }} text-slate-400 w-5 text-center"></i>
                            @endif
                            {{ $item->label }}
                        </a>

                        {{-- Nested Children (Mobile) --}}
                        @if (isset($item->children) && $item->children->isNotEmpty())
                            @foreach ($item->children as $child)
                                @if (!($child->is_divider ?? false))
                                    <a href="{{ $child->resolved_url }}" target="{{ $child->target ?? '_self' }}"
                                        class="flex items-center gap-3 px-8 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-lg">
                                        @if ($child->icon ?? false)
                                            <i class="{{ $child->icon }} text-slate-400 w-4 text-center"></i>
                                        @endif
                                        {{ $child->label }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endif
                @endforeach
            @else
                {{-- Default Mobile Primary Links --}}
                <a href="{{ route('journal.public.home', $journal->slug) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-house text-slate-400 w-5 text-center"></i> Home
                </a>
                <a href="{{ route('journal.public.about', $journal->slug) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-info-circle text-slate-400 w-5 text-center"></i> About
                </a>
                <a href="{{ route('journal.public.current', $journal->slug) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-book-open text-slate-400 w-5 text-center"></i> Current Issue
                </a>
                <a href="{{ route('journal.public.archives', $journal->slug) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-archive text-slate-400 w-5 text-center"></i> Archives
                </a>
                <a href="{{ route('journal.public.editorial-team', $journal->slug) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-users text-slate-400 w-5 text-center"></i> Editorial Team
                </a>
                <a href="{{ route('journal.public.author-guidelines', $journal->slug) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-file-alt text-slate-400 w-5 text-center"></i> Author Guidelines
                </a>
            @endif

            {{-- User Menu Items for Authenticated Users (Mobile) --}}
            @auth
                @if ($hasUserMenu)
                    {{-- Dynamic Mobile User Menu --}}
                    @foreach ($userMenuItems as $item)
                        @if (!($item->is_divider ?? false))
                            <a href="{{ $item->resolved_url }}" target="{{ $item->target ?? '_self' }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                                @if ($item->icon ?? false)
                                    <i class="{{ $item->icon }} text-slate-400 w-5 text-center"></i>
                                @endif
                                {{ $item->label }}
                            </a>

                            {{-- Nested Children (Mobile) --}}
                            @if (isset($item->children) && $item->children->isNotEmpty())
                                @foreach ($item->children as $child)
                                    @if (!($child->is_divider ?? false))
                                        <a href="{{ $child->resolved_url }}" target="{{ $child->target ?? '_self' }}"
                                            class="flex items-center gap-3 px-8 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-lg">
                                            @if ($child->icon ?? false)
                                                <i class="{{ $child->icon }} text-slate-400 w-4 text-center"></i>
                                            @endif
                                            {{ $child->label }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @else
                    {{-- Default User Navigation Links (Mobile) --}}
                    <a href="{{ route('journal.submissions.index', $journal->slug) }}"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                        <i class="fa-solid fa-gauge-high text-slate-400 w-5 text-center"></i> Dashboard
                    </a>
                    <a href="{{ route('journal.submissions.index', $journal->slug) }}"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">
                        <i class="fa-solid fa-paper-plane text-slate-400 w-5 text-center"></i> Submissions
                    </a>
                @endif
            @endauth

            {{-- Mobile Submit Button --}}
            <div class="pt-4 border-t border-slate-100 mt-4">
                <a href="{{ route('journal.submissions.create', $journal->slug) }}"
                    class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white rounded-lg"
                    style="background: {{ $primaryColor }};">
                    <i class="fa-solid fa-paper-plane"></i>
                    Submit Manuscript
                </a>
            </div>

            {{-- Mobile Auth Links --}}
            @guest
                <div class="pt-4 border-t border-slate-100 mt-4 flex gap-3">
                    <a href="{{ route('journal.login', $journal->slug) }}"
                        class="flex-1 text-center px-4 py-2.5 text-sm font-medium border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                        Login
                    </a>
                    <a href="{{ route('journal.register', $journal->slug) }}"
                        class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-white rounded-lg"
                        style="background: {{ $primaryColor }};">
                        Register
                    </a>
                </div>
            @endguest
        </div>
    </div>
</nav>
