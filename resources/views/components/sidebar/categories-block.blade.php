{{-- Categories Block - Article categories list --}}
@props(['journal', 'settings' => [], 'block' => null])

@php
$categories = $journal->categories()->withCount('submissions')->orderBy('name')->get();
@endphp

@if($categories->isNotEmpty())
<ul class="space-y-2">
    @foreach($categories as $category)
    <li>
        <a href="{{ route('journal.public.category', [$journal->slug, $category->path]) }}"
            class="flex items-center justify-between text-sm text-gray-600 hover:text-gray-900 transition-colors">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-tag text-gray-400 text-xs"></i>
                {{ $category->name }}
            </span>
            @if($category->submissions_count > 0)
            <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-500 rounded-full">
                {{ $category->submissions_count }}
            </span>
            @endif
        </a>
    </li>
    @endforeach
</ul>
@else
<p class="text-sm text-gray-500 italic">No categories defined</p>
@endif