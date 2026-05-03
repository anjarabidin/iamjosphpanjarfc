{{-- Public Navbar Component with Dynamic Navigation --}}
@props(['journal', 'settings' => [], 'primaryNavItems' => collect()])

@php
$primaryColor = $settings['primary_color'] ?? '#4F46E5';
$secondaryColor = $settings['secondary_color'] ?? '#7C3AED';
@endphp

<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <div class="flex items-center">
                <a href="{{ route('journal.public.home', $journal->slug) }}" class="flex items-center gap-3">
                    @if($journal->logo_path)
                    <img src="{{ Storage::url($journal->logo_path) }}" alt="{{ $journal->name }}" class="h-10">
                    @else
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold"
                        style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                        {{ strtoupper(substr($journal->abbreviation ?? $journal->name, 0, 2)) }}
                    </div>
                    @endif
                    <span class="font-bold text-gray-900 hidden sm:block">{{ $journal->abbreviation ?? $journal->name }}</span>
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-1">
                @if($primaryNavItems->isNotEmpty())
                @foreach($primaryNavItems as $item)
                @if($item->is_divider)
                <div class="w-px h-6 bg-gray-200 mx-2"></div>
                @elseif($item->has_children)
                {{-- Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                        @if($item->icon)
                        <i class="{{ $item->icon }} text-gray-400"></i>
                        @endif
                        {{ $item->label }}
                        <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                        @foreach($item->children as $child)
                        @if($child->is_divider)
                        <hr class="my-2 border-gray-100">
                        @else
                        <a href="{{ $child->resolved_url }}" target="{{ $child->target }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            @if($child->icon)
                            <i class="{{ $child->icon }} text-gray-400 w-4"></i>
                            @endif
                            {{ $child->label }}
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>
                @else
                {{-- Regular Link --}}
                <a href="{{ $item->resolved_url }}" target="{{ $item->target }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                    @if($item->icon)
                    <i class="{{ $item->icon }} text-gray-400"></i>
                    @endif
                    {{ $item->label }}
                </a>
                @endif
                @endforeach
                @else
                {{-- Default Navigation Links --}}
                <a href="{{ route('journal.public.home', $journal->slug) }}"
                    class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                    Home
                </a>
                <a href="{{ route('journal.public.current', $journal->slug) }}"
                    class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                    Current
                </a>
                <a href="{{ route('journal.public.archives', $journal->slug) }}"
                    class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                    Archives
                </a>
                <a href="{{ route('journal.public.about', $journal->slug) }}"
                    class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                    About
                </a>
                @endif

                {{-- CTA Button --}}
                <a href="{{ route('journal.submissions.create', $journal->slug) }}"
                    class="ml-2 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all hover:shadow-md"
                    style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                    <i class="fa-solid fa-paper-plane mr-1"></i>
                    Submit
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden">
                <button x-data @click="$dispatch('toggle-mobile-menu')"
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open"
        x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="md:hidden border-t border-gray-100">
        <div class="px-4 py-4 space-y-1">
            @if($primaryNavItems->isNotEmpty())
            @foreach($primaryNavItems as $item)
            @if(!$item->is_divider)
            <a href="{{ $item->resolved_url }}" target="{{ $item->target }}"
                class="flex items-center gap-3 px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                @if($item->icon)
                <i class="{{ $item->icon }} text-gray-400 w-5"></i>
                @endif
                {{ $item->label }}
            </a>
            @endif
            @endforeach
            @else
            <a href="{{ route('journal.public.home', $journal->slug) }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Home</a>
            <a href="{{ route('journal.public.current', $journal->slug) }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Current</a>
            <a href="{{ route('journal.public.archives', $journal->slug) }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Archives</a>
            <a href="{{ route('journal.public.about', $journal->slug) }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">About</a>
            @endif
            <a href="{{ route('journal.submissions.create', $journal->slug) }}"
                class="block px-3 py-2 text-base font-semibold text-white rounded-lg text-center mt-3"
                style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                <i class="fa-solid fa-paper-plane mr-2"></i>
                Submit Article
            </a>
        </div>
    </div>
</nav>