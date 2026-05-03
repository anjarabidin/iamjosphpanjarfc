@props(['journal', 'block'])
@php
    $announcements = \App\Models\Announcement::where('journal_id', $journal->id)
        ->where('is_active', true)
        ->latest()
        ->take(3)
        ->get();
@endphp

<div class="text-sm text-slate-600">
    @if($announcements->count() > 0)
        <div class="space-y-4">
            @foreach($announcements as $announcement)
                <div class="border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                    <a href="{{ route('journal.announcement.show', ['journal' => $journal->slug, 'id' => $announcement->id]) }}" class="font-medium text-slate-800 hover:text-indigo-600 hover:underline leading-tight block mb-1">
                        {{ $announcement->title }}
                    </a>
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <i class="fa-regular fa-calendar text-[10px]"></i>
                        {{ $announcement->created_at->format('M d, Y') }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('journal.announcement.index', $journal->slug) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline inline-flex items-center gap-1">
                More Announcements <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
            </a>
        </div>
    @else
        <p class="text-slate-500 italic">No recent announcements.</p>
    @endif
</div>
