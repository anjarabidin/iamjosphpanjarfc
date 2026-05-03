@props(['journal', 'block'])
@php
    $primaryColor = $journal->getWebsiteSettings()['primary_color'] ?? '#0369a1';
@endphp

<div class="flex flex-col space-y-2 text-sm">
    <a href="{{ route('journal.public.about', $journal->slug) }}" class="flex items-center text-slate-600 hover:text-primary-600 transition">
        <i class="fa-solid fa-book-open w-4 mr-2 text-slate-400"></i>
        For Readers
    </a>
    <a href="{{ route('journal.public.author-guidelines', $journal->slug) }}" class="flex items-center text-slate-600 hover:text-primary-600 transition">
        <i class="fa-solid fa-pen w-4 mr-2 text-slate-400"></i>
        For Authors
    </a>
    <a href="{{ route('journal.public.editorial-team', $journal->slug) }}" class="flex items-center text-slate-600 hover:text-primary-600 transition">
        <i class="fa-solid fa-user-tie w-4 mr-2 text-slate-400"></i>
        For Librarians
    </a>
</div>
