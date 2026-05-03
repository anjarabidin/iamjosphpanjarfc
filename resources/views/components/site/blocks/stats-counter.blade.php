{{-- Stats Counter Block - Animated Platform Statistics --}}
@props(['block', 'data' => []])

@php
$config = $block->config ?? [];
$layout = $config['layout'] ?? 'horizontal';
$showAnimation = $config['show_animation'] ?? true;

$stats = [
    [
        'label' => 'Journals',
        'value' => $data['total_journals'] ?? 0,
        'icon' => 'fa-book',
        'color' => 'blue',
    ],
    [
        'label' => 'Articles',
        'value' => $data['total_articles'] ?? 0,
        'icon' => 'fa-file-lines',
        'color' => 'green',
    ],
    [
        'label' => 'Authors',
        'value' => $data['total_authors'] ?? 0,
        'icon' => 'fa-users',
        'color' => 'purple',
    ],
    [
        'label' => 'Downloads',
        'value' => $data['total_downloads'] ?? 50000,
        'icon' => 'fa-download',
        'color' => 'amber',
    ],
];

$colorMap = [
    'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
    'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'border' => 'border-green-200'],
    'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'border' => 'border-purple-200'],
    'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'border' => 'border-amber-200'],
];
@endphp

<section class="py-12 bg-white border-y border-gray-100" x-data="statsCounter()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            @foreach($stats as $stat)
                @php $colors = $colorMap[$stat['color']] ?? $colorMap['blue']; @endphp
                
                <div class="text-center group" 
                     x-intersect.once="animateCounter($el, {{ $stat['value'] }})">
                    {{-- Icon --}}
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl {{ $colors['bg'] }} {{ $colors['border'] }} border mb-4 group-hover:scale-110 transition-transform">
                        <i class="fa-solid {{ $stat['icon'] }} text-2xl {{ $colors['text'] }}"></i>
                    </div>

                    {{-- Counter --}}
                    <div class="text-3xl md:text-4xl font-bold text-gray-900 mb-1" data-target="{{ $stat['value'] }}">
                        @if($showAnimation)
                            0
                        @else
                            {{ number_format($stat['value']) }}
                        @endif
                    </div>

                    {{-- Label --}}
                    <div class="text-sm text-gray-500 font-medium">
                        {{ $stat['label'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@pushOnce('scripts')
<script>
function statsCounter() {
    return {
        animateCounter(el, target) {
            const counterEl = el.querySelector('[data-target]');
            if (!counterEl) return;

            const duration = 2000;
            const start = 0;
            const startTime = performance.now();

            const step = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function
                const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                const current = Math.floor(start + (target - start) * easeOutQuart);
                
                counterEl.textContent = current.toLocaleString();
                
                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            };

            requestAnimationFrame(step);
        }
    }
}
</script>
@endPushOnce
