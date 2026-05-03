{{-- Subject Categories Block - Modern Icon Grid --}}
@props(['block', 'data' => []])

@php
$config = $block->config ?? [];
$title = $config['title'] ?? 'Browse by Subject';
$subtitle = $config['subtitle'] ?? 'Find journals in your research area';
$layout = $config['layout'] ?? 'icon-grid';
$columns = $config['columns'] ?? 6;
$showCount = $config['show_count'] ?? true;

$defaultCategories = [
    ['name' => 'Science & Technology', 'icon' => 'fa-flask', 'color' => 'blue', 'slug' => 'science-technology'],
    ['name' => 'Medicine & Health', 'icon' => 'fa-heartbeat', 'color' => 'red', 'slug' => 'medicine-health'],
    ['name' => 'Social Sciences', 'icon' => 'fa-users', 'color' => 'green', 'slug' => 'social-sciences'],
    ['name' => 'Arts & Humanities', 'icon' => 'fa-palette', 'color' => 'purple', 'slug' => 'arts-humanities'],
    ['name' => 'Business & Economics', 'icon' => 'fa-chart-bar', 'color' => 'amber', 'slug' => 'business-economics'],
    ['name' => 'Education', 'icon' => 'fa-graduation-cap', 'color' => 'indigo', 'slug' => 'education'],
    ['name' => 'Law', 'icon' => 'fa-gavel', 'color' => 'slate', 'slug' => 'law'],
    ['name' => 'Engineering', 'icon' => 'fa-cogs', 'color' => 'orange', 'slug' => 'engineering'],
    ['name' => 'Agriculture', 'icon' => 'fa-leaf', 'color' => 'emerald', 'slug' => 'agriculture'],
    ['name' => 'Islamic Studies', 'icon' => 'fa-mosque', 'color' => 'teal', 'slug' => 'islamic-studies'],
    ['name' => 'Computer Science', 'icon' => 'fa-laptop-code', 'color' => 'cyan', 'slug' => 'computer-science'],
    ['name' => 'Mathematics', 'icon' => 'fa-calculator', 'color' => 'gray', 'slug' => 'mathematics'],
];

$categories = $config['categories'] ?? $defaultCategories;
$categoryData = $data['categories'] ?? [];

// Color mappings
$colorClasses = [
    'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'hover' => 'hover:bg-blue-200'],
    'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'hover' => 'hover:bg-red-200'],
    'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'hover' => 'hover:bg-green-200'],
    'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'hover' => 'hover:bg-purple-200'],
    'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'hover' => 'hover:bg-amber-200'],
    'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'hover' => 'hover:bg-indigo-200'],
    'slate' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'hover' => 'hover:bg-slate-200'],
    'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'hover' => 'hover:bg-orange-200'],
    'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'hover' => 'hover:bg-emerald-200'],
    'teal' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'hover' => 'hover:bg-teal-200'],
    'cyan' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-600', 'hover' => 'hover:bg-cyan-200'],
    'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'hover' => 'hover:bg-gray-200'],
];
@endphp

<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ $title }}
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                {{ $subtitle }}
            </p>
        </div>

        {{-- Categories Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
            @foreach($categories as $category)
                @php
                    $color = $category['color'] ?? 'blue';
                    $classes = $colorClasses[$color] ?? $colorClasses['blue'];
                    $count = $categoryData[$category['slug'] ?? ''] ?? rand(5, 30);
                @endphp
                
                <a href="{{ route('portal.search', ['category' => $category['slug'] ?? Str::slug($category['name'])]) }}"
                   class="group flex flex-col items-center p-6 rounded-2xl {{ $classes['bg'] }} {{ $classes['hover'] }} transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-xl {{ $classes['bg'] }} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fa-solid {{ $category['icon'] ?? 'fa-folder' }} text-2xl {{ $classes['text'] }}"></i>
                    </div>

                    {{-- Name --}}
                    <h3 class="text-sm font-semibold text-gray-900 text-center mb-1">
                        {{ $category['name'] }}
                    </h3>

                    {{-- Count --}}
                    @if($showCount)
                        <span class="text-xs text-gray-500">
                            {{ $count }} Journals
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
