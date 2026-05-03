@props(['journal', 'block'])
@php
    $categories = \App\Models\Category::where('journal_id', $journal->id)->ordered()->get();
@endphp

<div class="text-sm text-slate-600">
    @if($categories->count() > 0)
        <ul class="space-y-2">
            @foreach($categories as $category)
                <li>
                    <a href="{{ route('journal.public.search', ['journal' => $journal->slug, 'category' => $category->id]) }}" class="flex items-start gap-2 text-indigo-600 hover:text-indigo-800 hover:underline transition-colors group">
                        <i class="fa-solid fa-tag text-slate-400 group-hover:text-indigo-500 text-[10px] mt-1 text-center w-4 shrink-0 transition-colors"></i>
                        <span>{{ $category->name }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-slate-500 italic">No categories available.</p>
    @endif
</div>
