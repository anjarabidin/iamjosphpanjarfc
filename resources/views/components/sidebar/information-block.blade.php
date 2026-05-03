{{-- Information Block - Displays journal information --}}
@props(['journal', 'settings' => [], 'block' => null])

<ul class="space-y-2.5 text-sm">
    <li>
        <a href="{{ route('journal.public.about', $journal->slug) }}"
            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fa-solid fa-book-open w-4 text-gray-400"></i>
            About the Journal
        </a>
    </li>
    <li>
        <a href="{{ route('journal.public.editorial-team', $journal->slug) }}"
            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fa-solid fa-users w-4 text-gray-400"></i>
            Editorial Team
        </a>
    </li>
    @if($journal->issn_online)
    <li class="flex items-center gap-2 text-gray-600">
        <i class="fa-solid fa-barcode w-4 text-gray-400"></i>
        <span>ISSN: {{ $journal->issn_online }}</span>
    </li>
    @endif
    @if($journal->publisher)
    <li class="flex items-center gap-2 text-gray-600">
        <i class="fa-solid fa-building w-4 text-gray-400"></i>
        <span>{{ $journal->publisher }}</span>
    </li>
    @endif
</ul>