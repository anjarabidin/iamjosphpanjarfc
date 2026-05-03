{{--
Dynamic Portal Navigation Component (OJS 3.3 Style)
--}}
@props(['primaryMenu' => null, 'userMenu' => null, 'settings' => []])

@php
    // Use passed menu data or fallback to direct queries if not provided
    $primaryMenuItems = $primaryMenu ?? collect();
    $userMenuItems = $userMenu ?? collect();
@endphp

<div x-data="{ mobileMenuOpen: false }">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
 
            {{-- LOGO --}}
            <a href="{{ route('portal.home') }}" class="flex items-center gap-2">
                @if (!empty($settings['site_logo']))
                    <img src="{{ Storage::url($settings['site_logo']) }}" class="h-8">
                @else
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        IJ
                    </div>
                @endif
                <span class="hidden sm:block font-bold text-slate-800">
                    {{ $settings['site_name'] ?? 'IAMJOS' }}
                </span>
            </a>
 
            {{-- PRIMARY MENU --}}
            <nav class="hidden md:flex items-center gap-1">
                @foreach ($primaryMenuItems as $item)
                    {{-- DROPDOWN --}}
                    @if (isset($item->children) && $item->children->isNotEmpty())
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open"
                                class="px-3 py-2 text-sm font-medium rounded-md flex items-center gap-1
                                text-slate-600 hover:bg-slate-50">
                                @if ($item->icon)
                                    <i class="{{ $item->icon }}"></i>
                                @endif
                                <span>{{ $item->label }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
 
                            <div x-show="open" x-transition
                                class="absolute mt-2 w-48 bg-white border rounded-md shadow z-50">
                                @foreach ($item->children as $child)
                                    <a href="{{ $child->resolved_url }}" target="{{ $child->target }}"
                                        class="block px-4 py-2 text-sm hover:bg-slate-50">
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- SINGLE LINK --}}
                        <a href="{{ $item->resolved_url }}" target="{{ $item->target }}"
                            class="px-3 py-2 text-sm font-medium rounded-md
                           text-slate-600 hover:bg-slate-50">
                            @if ($item->icon)
                                <i class="{{ $item->icon }}"></i>
                            @endif
                            <span>{{ $item->label }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
 
            {{-- USER ACTION --}}
            <div class="flex items-center gap-3">
                @auth
                    {{-- Authenticated User Menu --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open"
                            class="flex items-center gap-2 text-sm text-slate-700 hover:text-slate-900 transition-colors">
                            {{-- User Avatar --}}
                            <div
                                class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                @if (auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                                        class="w-8 h-8 rounded-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            {{-- User Name --}}
                            <span class="hidden lg:block font-medium">{{ auth()->user()->name }}</span>
                            {{-- Dropdown Arrow --}}
                            <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
 
                        {{-- Dropdown Menu --}}
                        <div x-show="open" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-lg shadow-lg z-50">
                            <div class="py-1">
                                <a href="{{ auth()->user()->hasRole('Super Admin') ? route('admin.site.index') : route('journal.select') }}"
                                    class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors">
                                    <i class="fa-solid fa-tachometer-alt mr-2"></i>
                                    Dashboard
                                </a>
                                <a href="{{ ($j = request()->route('journal') ?? \App\Models\Journal::first()) ? route('journal.profile.edit', $j) : '#' }}"
                                    class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors">
                                    <i class="fa-solid fa-user-edit mr-2"></i>
                                    Edit Profile
                                </a>
                                <hr class="border-slate-200 my-1">
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors">
                                    <i class="fa-solid fa-sign-out-alt mr-2"></i>
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Guest User Actions --}}
                    @if ($userMenuItems->isNotEmpty())
                        {{-- Custom User Menu Items --}}
                        @foreach ($userMenuItems as $item)
                            <a href="{{ $item->resolved_url }}"
                                class="hidden md:block text-sm text-slate-600 hover:text-blue-600">
                                {{ $item->label }}
                            </a>
                        @endforeach
                    @else
                        {{-- Default Login/Register --}}
                        <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-blue-600">Login</a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
                            Register
                        </a>
                    @endif
                @endauth
 
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>
 
    {{-- MOBILE MENU --}}
    <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-white border-t">
        <div class="px-4 py-3 space-y-2">
            {{-- Primary Menu Items --}}
            @foreach ($primaryMenuItems as $item)
                @if (isset($item->children) && $item->children->isNotEmpty())
                    <details>
                        <summary class="py-2 font-medium cursor-pointer">
                            {{ $item->label }}
                        </summary>
                        <div class="ml-4">
                            @foreach ($item->children as $child)
                                <a href="{{ $child->resolved_url }}" class="block py-1 text-slate-600">
                                    {{ $child->label }}
                                </a>
                            @endforeach
                        </div>
                    </details>
                @else
                    <a href="{{ $item->resolved_url }}" class="block py-2 font-medium">
                        {{ $item->label }}
                    </a>
                @endif
            @endforeach
 
            {{-- Guest Actions (Only show if not logged in) --}}
            @guest
                @if ($userMenuItems->isEmpty())
                    <hr class="border-slate-200 my-2">
                    <a href="{{ route('login') }}" class="block py-2 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="block py-2 font-medium text-blue-600">Register</a>
                @endif
            @endguest
        </div>
    </div>
</div>
