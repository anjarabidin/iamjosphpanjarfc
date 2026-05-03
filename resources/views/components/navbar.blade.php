{{-- Navbar Component --}}
<header class="sticky top-0 z-10 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

        <!-- Left: Mobile Menu Button + Breadcrumb -->
        <div class="flex items-center">
            <!-- Mobile menu button -->
            <button @click="sidebarOpen = true"
                class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo (visible on mobile when sidebar is hidden) -->
            <a href="{{ route('dashboard') }}" class="lg:hidden ml-2 flex items-center space-x-2">
                <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="font-bold text-lg text-gray-900">IAMJOS</span>
            </a>

            <!-- Breadcrumb (desktop) -->
            <nav class="hidden lg:flex items-center space-x-2 text-sm">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                @isset($breadcrumb)
                    {{ $breadcrumb }}
                @endisset
            </nav>
        </div>

        <!-- Right: User Dropdown -->
        <div class="flex items-center space-x-4">

            <!-- Notifications (placeholder) -->
            <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                {{-- Notification badge --}}
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <!-- Avatar -->
                    <div
                        class="w-9 h-9 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <!-- Name & Role -->
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ Auth::user()->primary_role_label }}
                        </p>
                    </div>
                    <!-- Chevron -->
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="border-t border-gray-100 py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
