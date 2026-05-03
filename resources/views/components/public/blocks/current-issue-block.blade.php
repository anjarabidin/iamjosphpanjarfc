@props(['journal', 'block'])
@php
    $primaryColor = $journal->getWebsiteSettings()['primary_color'] ?? '#0369a1';
    $currentIssue = $journal->issues()->where('is_published', true)->orderBy('published_at', 'desc')->first();
@endphp

@if($currentIssue)
<div class="px-4 pb-1">
    @if($currentIssue->cover_path)
        <a href="{{ route('journal.public.current', $journal->slug) }}" class="block mb-4">
            <img src="{{ Storage::url($currentIssue->cover_path) }}" 
                    alt="{{ $currentIssue->display_title }}"
                    class="w-full rounded-lg shadow-sm hover:shadow-md transition-shadow">
        </a>
    @endif
    <div class="text-center">
        <a href="{{ route('journal.public.current', $journal->slug) }}" 
            class="text-sm font-semibold hover:underline" style="color: {{ $primaryColor }};">
            {{ $currentIssue->display_title }}
        </a>
        @if($currentIssue->published_at)
            <p class="text-xs text-slate-500 mt-1">
                Published: {{ $currentIssue->published_at->format('M d, Y') }}
            </p>
        @endif
    </div>
</div>
@else
<div class="px-4 pb-1">
    <p class="text-sm text-slate-500 italic text-center">No issues published yet.</p>
</div>
@endif
