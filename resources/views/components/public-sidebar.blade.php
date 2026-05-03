{{-- Public Sidebar Component with Dynamic Blocks --}}
@props(['journal', 'settings' => [], 'sidebarBlocks' => collect()])

@php
$primaryColor = $settings['primary_color'] ?? '#4F46E5';
@endphp

<aside {{ $attributes->merge(['class' => 'space-y-6']) }}>
    @forelse($sidebarBlocks as $block)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Block Header --}}
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                @if($block->icon)
                <i class="{{ $block->icon }}" style="color: {{ $primaryColor }};"></i>
                @endif
                {{ $block->title }}
            </h3>
        </div>

        {{-- Block Content --}}
        <div class="p-4">
            @if($block->is_system && $block->component_name)
            {{-- Render System Component --}}
            @try
            <x-dynamic-component :component="$block->component_name" :journal="$journal" :settings="$settings" :block="$block" />
            @catch (\Exception $e)
            <p class="text-sm text-gray-500 italic">Component not available</p>
            @endtry
            @else
            {{-- Render Custom HTML Content --}}
            <div class="prose prose-sm max-w-none">
                {!! $block->content !!}
            </div>
            @endif
        </div>
    </div>
    @empty
    {{-- Default Sidebar Content when no blocks configured --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">
                <i class="fa-solid fa-info-circle mr-2" style="color: {{ $primaryColor }};"></i>
                Information
            </h3>
        </div>
        <div class="p-4">
            <ul class="space-y-2 text-sm">
                <li class="flex items-center gap-2 text-gray-600">
                    <i class="fa-solid fa-book-open w-4 text-gray-400"></i>
                    <a href="{{ route('journal.public.about', $journal->slug) }}" class="hover:text-gray-900">About the Journal</a>
                </li>
                <li class="flex items-center gap-2 text-gray-600">
                    <i class="fa-solid fa-users w-4 text-gray-400"></i>
                    <a href="{{ route('journal.public.editorial-team', $journal->slug) }}" class="hover:text-gray-900">Editorial Team</a>
                </li>
                <li class="flex items-center gap-2 text-gray-600">
                    <i class="fa-solid fa-file-alt w-4 text-gray-400"></i>
                    <a href="#" class="hover:text-gray-900">Submission Guidelines</a>
                </li>
            </ul>
        </div>
    </div>
    @endforelse
</aside>