@props(['active' => false, 'href' => '#'])

@php
    $classes = $active
        ? 'flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-primary-500 text-white'
        : 'flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors duration-150';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <span class="mr-3 flex-shrink-0">{{ $icon }}</span>
    @endif
    <span>{{ $slot }}</span>
</a>
