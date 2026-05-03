{{-- Current Issue Block - Displays current issue info --}}
@props(['journal', 'settings' => [], 'block' => null])

@php
$primaryColor = $settings['primary_color'] ?? '#4F46E5';
$currentIssue = $journal->issues()->where('is_published', true)->latest('published_at')->first();
@endphp

@if($currentIssue)
<div>
    @if($currentIssue->cover_image_path)
    <img src="{{ Storage::url($currentIssue->cover_image_path) }}"
        alt="{{ $currentIssue->title }}"
        class="w-full rounded-lg mb-3 shadow-sm">
    @endif

    <div class="text-center">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium mb-2"
            style="background: {{ $primaryColor }}20; color: {{ $primaryColor }};">
            Vol. {{ $currentIssue->volume }} No. {{ $currentIssue->number }}
        </span>
        <h4 class="font-medium text-gray-900 text-sm">{{ $currentIssue->title ?? 'Current Issue' }}</h4>
        <p class="text-xs text-gray-500 mt-1">
            {{ $currentIssue->published_at?->format('F Y') }}
        </p>
        <a href="{{ route('journal.public.current', $journal->slug) }}"
            class="inline-flex items-center text-sm font-medium mt-3"
            style="color: {{ $primaryColor }};">
            View Issue
            <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
</div>
@else
<p class="text-sm text-gray-500 text-center italic">
    No published issues yet
</p>
@endif