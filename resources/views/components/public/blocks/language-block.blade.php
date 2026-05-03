@props(['journal', 'block'])
@php
    $primaryColor = $journal->getWebsiteSettings()['primary_color'] ?? '#0369a1';
@endphp
<div class="text-sm text-slate-600">
    <div class="flex items-center gap-2">
        <i class="fa-solid fa-globe text-slate-400"></i>
        <span>English</span>
    </div>
    <p class="text-xs text-slate-500 mt-2 italic">More languages coming soon.</p>
</div>
