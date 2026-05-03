{{-- Announcements Block - Recent announcements --}}
@props(['journal', 'settings' => [], 'block' => null])

@php
$announcements = $journal->announcements()
->where('is_active', true)
->latest()
->take(3)
->get();
@endphp

@if($announcements->isNotEmpty())
<div class="space-y-3">
    @foreach($announcements as $announcement)
    <div class="border-l-2 border-indigo-500 pl-3">
        <p class="text-xs text-gray-400">
            {{ $announcement->created_at->format('M d, Y') }}
        </p>
        <h4 class="text-sm font-medium text-gray-900 line-clamp-2">
            {{ $announcement->title }}
        </h4>
    </div>
    @endforeach
</div>
@else
<p class="text-sm text-gray-500 italic">No announcements</p>
@endif