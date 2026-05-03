@php $title = 'Archives'; @endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">

    <div class="container mx-auto px-4 lg:px-0">

        {{-- BREADCRUMB --}}
        <nav class="text-sm text-slate-500 mb-6">
            <a href="{{ route('journal.public.home', $journal->slug) }}"
               class="hover:text-primary-600">
                Home
            </a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-medium">Archives</span>
        </nav>

        {{-- PAGE TITLE --}}
        <h1 class="text-3xl font-bold text-slate-800 mb-12">
            Archives
        </h1>

        {{-- ARCHIVE LIST --}}
        @if($issues->count())
            <div class="space-y-16">

                @foreach($issues as $issue)
                    {{-- ISSUE ROW --}}
                    <article class="grid grid-cols-1 lg:grid-cols-[180px_minmax(0,1fr)] gap-8 items-start">

                        {{-- COVER (STRICT SIZE) --}}
                        <div class="w-[180px] max-w-full">
                            <a href="{{ route('journal.public.issue', ['journal' => $journal->slug, 'issue' => $issue->seq_id]) }}"
                               class="block">
                                @if($issue->cover_path)
                                    <img
                                        src="{{ Storage::url($issue->cover_path) }}"
                                        alt="Cover Vol {{ $issue->volume }}"
                                        class="w-full h-auto aspect-[3/4] object-cover border border-slate-200 shadow-sm"
                                    >
                                @else
                                    <div class="w-full aspect-[3/4] bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-xs font-semibold uppercase">
                                        No Cover
                                    </div>
                                @endif
                            </a>
                        </div>

                        {{-- ISSUE INFO --}}
                        <div class="min-w-0">

                            {{-- TITLE --}}
                            <h2 class="text-2xl font-bold text-slate-900 leading-snug mb-2">
                                <a href="{{ route('journal.public.issue', ['journal' => $journal->slug, 'issue' => $issue->seq_id]) }}"
                                   class="hover:text-primary-700 hover:underline">
                                    {{ $issue->title ?: "Vol. {$issue->volume} No. {$issue->number} ({$issue->year})" }}
                                </a>
                            </h2>

                            {{-- META --}}
                            <div class="text-base text-slate-500 mb-4">
                                Vol. {{ $issue->volume }} No. {{ $issue->number }} ({{ $issue->year }})
                            </div>

                            {{-- JOURNAL + ISSN --}}
                            <div class="text-slate-700 mb-5 leading-relaxed">
                                <strong>{{ $journal->name }}</strong>
                                @if($journal->print_issn || $journal->online_issn)
                                    :
                                @endif

                                @if($journal->print_issn)
                                    ISSN
                                    <span class="text-primary-600">{{ $journal->print_issn }}</span>
                                    (CETAK)
                                @endif

                                @if($journal->online_issn)
                                    ,
                                    ISSN
                                    <span class="text-primary-600">{{ $journal->online_issn }}</span>
                                    (ONLINE)
                                @endif
                            </div>

                            {{-- DESCRIPTION --}}
                            @if($issue->description)
                                <div class="prose max-w-none text-slate-700">
                                    {!! clean($issue->description) !!}
                                </div>
                            @endif

                        </div>
                    </article>
                @endforeach

            </div>

            {{-- PAGINATION --}}
            <div class="mt-20">
                {{ $issues->links() }}
            </div>
        @else
            <div class="p-10 bg-slate-50 border border-slate-200 rounded text-center">
                <p class="text-slate-500">No archives available.</p>
            </div>
        @endif

    </div>
</x-layouts.public>
