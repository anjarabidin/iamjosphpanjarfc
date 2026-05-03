@extends('layouts.app')

@section('title', 'ICI XML Exporter - ' . $journal->name)

@section('content')
    <div x-data="{ tab: 'articles' }" class="space-y-6">

        {{-- HEADER --}}
        <div
            class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200/60 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('journal.settings.tools.index', ['journal' => $journal->slug]) }}"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text">
                        ICI XML Exporter
                    </h1>
                </div>
                <p class="text-sm text-slate-500 mt-1 ml-8">Export articles and issues metadata in the format required by Index Copernicus.</p>
            </div>
        </div>

        {{-- GLOBAL ALERTS --}}
        @if (empty($journal->issn_online))
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-amber-800">Missing Configuration</h4>
                    <p class="text-sm text-amber-700 mt-1">Journal E-ISSN is missing. Please update Journal Settings before exporting, otherwise the file will be rejected by Index Copernicus.</p>
                </div>
            </div>
        @endif

        {{-- ALERTS --}}
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="text-sm text-emerald-700">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        {{-- MAIN CONTENT --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">

            {{-- TABS NAVIGATION --}}
            <div class="border-b border-slate-200">
                <nav class="flex">
                    <button @click="tab = 'articles'"
                        :class="tab === 'articles' ?
                            'border-indigo-500 text-indigo-600 bg-indigo-50/50' :
                            'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                        class="flex-1 md:flex-none whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export Articles
                    </button>
                    <button @click="tab = 'issues'"
                        :class="tab === 'issues' ?
                            'border-indigo-500 text-indigo-600 bg-indigo-50/50' :
                            'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                        class="flex-1 md:flex-none whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Export Issues
                    </button>
                </nav>
            </div>

            {{-- TAB 1: EXPORT ARTICLES --}}
            <div x-show="tab === 'articles'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">

                <form action="{{ route('journal.settings.tools.copernicus.export.articles', ['journal' => $journal->slug]) }}"
                    method="POST">
                    @csrf

                    <div class="mb-6 p-4 bg-indigo-50/50 rounded-xl border border-indigo-100/50">
                        <p class="text-sm text-indigo-700 flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Hanya artikel dengan metadata lengkap yang dapat dipilih untuk diekspor. Silakan lengkapi data melalui menu Submissions jika tombol centang tidak aktif.
                        </p>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-slate-800">Select Published Articles</h3>
                            <p class="text-sm text-slate-500">Choose specific articles to export in Index Copernicus (ICI) format.</p>
                        </div>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-medium text-white shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2"
                            style="background: linear-gradient(to right, #10b981, #14b8a6); color: white;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export Selected
                        </button>
                    </div>

                    @if ($submissions->count() > 0)
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <div class="max-h-[500px] overflow-y-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-50 text-slate-600 sticky top-0 z-10">
                                        <tr>
                                            <th class="p-4 w-12">
                                                <input type="checkbox" onChange="document.querySelectorAll('.article-checkbox').forEach(cb => cb.checked = this.checked)"
                                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            </th>
                                            <th class="p-4 font-semibold">Title</th>
                                            <th class="p-4 font-semibold">Authors</th>
                                            <th class="p-4 font-semibold">Published</th>
                                            <th class="p-4 font-semibold">Issue</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($submissions as $submission)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="p-4">
                                                    <input type="checkbox" name="submission_ids[]"
                                                        value="{{ $submission->id }}"
                                                        @if(!empty($submission->ici_missing_fields)) disabled @endif
                                                        class="article-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 disabled:bg-slate-100 disabled:cursor-not-allowed">
                                                </td>
                                                <td class="p-4">
                                                    <span
                                                        class="font-medium text-slate-800">{{ Str::limit($submission->title, 60) }}</span>
                                                    @if(!empty($submission->ici_missing_fields))
                                                        <div class="mt-1">
                                                            <span class="inline-block text-[11px] font-semibold text-red-600 bg-red-50 border border-red-200 px-2.5 py-0.5 rounded-full">
                                                                Missing: {{ implode(', ', $submission->ici_missing_fields) }}
                                                            </span>
                                                        </div>
                                                    @elseif ($submission->submission_code)
                                                        <span
                                                            class="block text-xs text-slate-400 mt-1">{{ $submission->submission_code }}</span>
                                                    @endif
                                                </td>
                                                <td class="p-4 text-slate-600">
                                                    {{ $submission->authors->pluck('name')->take(2)->join(', ') }}
                                                    @if ($submission->authors->count() > 2)
                                                        <span
                                                            class="text-slate-400">+{{ $submission->authors->count() - 2 }}
                                                            more</span>
                                                    @endif
                                                </td>
                                                <td class="p-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                        {{ $submission->published_at?->format('M d, Y') ?? 'Published' }}
                                                    </span>
                                                </td>
                                                <td class="p-4 text-slate-500">
                                                    {{ $submission->issue?->identifier ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16 text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="font-medium">No published articles found</p>
                        </div>
                    @endif
                </form>
            </div>

            {{-- TAB 2: EXPORT ISSUES --}}
            <div x-show="tab === 'issues'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">

                <form action="{{ route('journal.settings.tools.copernicus.export.issues', ['journal' => $journal->slug]) }}"
                    method="POST">
                    @csrf

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-slate-800">Select Published Issues</h3>
                            <p class="text-sm text-slate-500">Export whole issues with all their contained articles in ICI format.</p>
                        </div>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-medium text-white shadow-lg shadow-purple-500/25 transition-all flex items-center gap-2"
                            style="background: linear-gradient(to right, #a855f7, #6366f1); color: white;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export Selected
                        </button>
                    </div>

                    @if ($issues->count() > 0)
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <div class="max-h-[500px] overflow-y-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-50 text-slate-600 sticky top-0 z-10">
                                        <tr>
                                            <th class="p-4 w-12">
                                                <input type="checkbox" onChange="document.querySelectorAll('.issue-checkbox').forEach(cb => cb.checked = this.checked)"
                                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            </th>
                                            <th class="p-4 font-semibold">Issue Identification</th>
                                            <th class="p-4 font-semibold">Published</th>
                                            <th class="p-4 font-semibold">Articles</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($issues as $issue)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="p-4">
                                                    <input type="checkbox" name="issue_ids[]"
                                                        value="{{ $issue->id }}"
                                                        class="issue-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                </td>
                                                <td class="p-4">
                                                    <span class="font-medium text-slate-800">
                                                        Vol {{ $issue->volume }}, No {{ $issue->number }}
                                                        ({{ $issue->year }})
                                                    </span>
                                                    @if ($issue->title)
                                                        <span
                                                            class="block text-sm text-slate-500">{{ $issue->title }}</span>
                                                    @endif
                                                </td>
                                                <td class="p-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                        {{ $issue->published_at?->format('M d, Y') ?? 'Published' }}
                                                    </span>
                                                </td>
                                                <td class="p-4">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-600">
                                                        {{ $issue->submissions_count }}
                                                        {{ Str::plural('article', $issue->submissions_count) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16 text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            <p class="font-medium">No published issues found</p>
                        </div>
                    @endif
                </form>
            </div>

        </div>
    </div>
@endsection
