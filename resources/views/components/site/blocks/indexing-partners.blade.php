{{-- 
    Indexing Partners Block - Modern Marquee with Duplicate Fix
    
    Features:
    - Removes duplicate logos
    - Static centered layout for few logos (≤6)
    - Infinite scroll marquee for many logos (>6)
    - Grayscale to color on hover
    - Gradient fade masks on edges
    - Pause animation on hover
--}}
@props(['block', 'data' => []])

@php
$config = $block->config ?? [];
$title = $config['title'] ?? 'Indexed by Major Databases';
$layout = $config['layout'] ?? 'auto'; // auto, static-grid, marquee
$logos = $config['logos'] ?? [];

// Fix duplicates: Remove any duplicate logo paths
$logos = collect($logos)->unique()->values()->toArray();

// Determine if we should use marquee animation
$logoCount = count($logos);
$marqueeThreshold = 6;
$enableMarquee = ($layout === 'marquee') || ($layout === 'auto' && $logoCount > $marqueeThreshold);
@endphp

@if($logoCount > 0)
<section class="py-12 md:py-16 bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Title --}}
        <h3 class="text-center text-sm font-bold text-slate-400 uppercase tracking-wider mb-8">
            {{ $title }}
        </h3>

        @if($enableMarquee && $logoCount > 1)
            {{-- ============================================ --}}
            {{-- OPTION 1: INFINITE MARQUEE SCROLL --}}
            {{-- For many logos (smooth horizontal scrolling) --}}
            {{-- ============================================ --}}
            <div class="w-full inline-flex flex-nowrap overflow-hidden relative group"
                 style="mask-image: linear-gradient(to right, transparent 0, black 128px, black calc(100% - 128px), transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0, black 128px, black calc(100% - 128px), transparent 100%);">
                
                {{-- First set of logos --}}
                <ul class="flex items-center justify-start animate-marquee-scroll shrink-0">
                    @foreach($logos as $logo)
                        <li class="mx-6 md:mx-10 flex items-center justify-center">
                            <img src="{{ Storage::url($logo) }}" 
                                 alt="Indexing Partner"
                                 class="h-10 md:h-12 w-auto max-w-[120px] object-contain grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                        </li>
                    @endforeach
                </ul>

                {{-- Duplicate set for seamless loop (CRITICAL for CSS animation) --}}
                <ul class="flex items-center justify-start animate-marquee-scroll shrink-0" aria-hidden="true">
                    @foreach($logos as $logo)
                        <li class="mx-6 md:mx-10 flex items-center justify-center">
                            <img src="{{ Storage::url($logo) }}" 
                                 alt=""
                                 class="h-10 md:h-12 w-auto max-w-[120px] object-contain grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                        </li>
                    @endforeach
                </ul>
            </div>

        @else
            {{-- ============================================ --}}
            {{-- OPTION 2: STATIC CENTERED GRID --}}
            {{-- For few logos (perfectly centered layout) --}}
            {{-- ============================================ --}}
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12 lg:gap-16">
                @foreach($logos as $logo)
                    <div class="flex items-center justify-center">
                        <img src="{{ Storage::url($logo) }}" 
                             alt="Indexing Partner"
                             class="h-10 md:h-12 w-auto max-w-[140px] object-contain grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300 cursor-pointer">
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@elseif(config('app.debug'))
    {{-- Placeholder in Debug Mode --}}
    <section class="py-12 bg-gray-50 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="inline-flex items-center gap-3 text-gray-400">
                <i class="fa-solid fa-images text-2xl"></i>
                <div class="text-left">
                    <p class="font-medium">Indexing Partners Block</p>
                    <p class="text-sm">No logos uploaded. <a href="{{ route('admin.site.appearance.edit', $block) }}" class="text-blue-500 hover:underline">Configure</a></p>
                </div>
            </div>
        </div>
    </section>
@endif

{{-- Inline CSS for Marquee - Ensures it works immediately without build steps --}}
<style>
    @keyframes marquee-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-100%); }
    }
    .animate-marquee-scroll {
        animation: marquee-scroll 40s linear infinite; /* Slowed down slightly for better readability */
        will-change: transform;
    }
    .animate-marquee-scroll:hover {
        animation-play-state: paused;
    }
</style>
