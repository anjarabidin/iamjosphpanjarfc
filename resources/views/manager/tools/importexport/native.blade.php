@extends('layouts.app')

@section('title', 'Native XML Plugin - ' . $journal->name)

@section('content')
    <div x-data="nativeXmlPlugin()" class="space-y-6">

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
                        Native XML Plugin
                    </h1>
                </div>
                <p class="text-sm text-slate-500 mt-1 ml-8">Import and export data in IAMJOS's native XML format.</p>
            </div>
        </div>

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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
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
                    <button @click="tab = 'import'"
                        :class="tab === 'import' ?
                            'border-indigo-500 text-indigo-600 bg-indigo-50/50' :
                            'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                        class="flex-1 md:flex-none whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import
                    </button>
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

            {{-- TAB 1: IMPORT --}}
            <div x-show="tab === 'import'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">

                <div class="w-full">
                    <h3 class="font-bold text-lg text-slate-800 mb-2">Upload XML File</h3>
                    <p class="text-sm text-slate-500 mb-6">
                        Upload articles or issues in IAMJOS Native XML format. The system will automatically process
                        and import all data including authors and metadata.
                    </p>

                    <form action="{{ route('journal.settings.tools.native.import', ['journal' => $journal->slug]) }}"
                        method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- File Upload Zone --}}
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-indigo-400 transition-colors"
                            x-data="{ dragging: false }" @dragover.prevent="dragging = true"
                            @dragleave.prevent="dragging = false" @drop.prevent="dragging = false; handleDrop($event)"
                            :class="{ 'border-indigo-400 bg-indigo-50/50': dragging }">

                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>

                            <p class="text-slate-600 mb-2">
                                <span class="font-medium">Drop your XML file here</span> or click to browse
                            </p>
                            <p class="text-xs text-slate-400 mb-4">Maximum file size: 10MB</p>

                            <input type="file" name="xml_file" id="xml_file" accept=".xml,application/xml"
                                class="hidden" @change="fileName = $event.target.files[0]?.name || ''">

                            <label for="xml_file"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 cursor-pointer transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                    </path>
                                </svg>
                                Choose File
                            </label>

                            <p x-show="fileName" x-text="fileName" class="text-sm text-indigo-600 font-medium mt-3"></p>
                        </div>

                        <button type="submit"
                            class="w-full px-5 py-3 rounded-xl text-sm font-medium text-white shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2"
                            style="background: linear-gradient(to right, #4f46e5, #9333ea); color: white;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Import XML
                        </button>
                    </form>
                </div>
            </div>

            {{-- TAB 2: EXPORT ARTICLES --}}
            <div x-show="tab === 'articles'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">

                <form action="{{ route('journal.settings.tools.native.export.articles', ['journal' => $journal->slug]) }}"
                    method="POST">
                    @csrf

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-slate-800">Select Articles to Export</h3>
                            <p class="text-sm text-slate-500">Choose specific articles to include in the export.</p>
                        </div>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-medium text-white shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2"
                            style="background: linear-gradient(to right, #10b981, #14b8a6); color: white;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export Selected Articles
                        </button>
                    </div>

                    @if ($submissions->count() > 0)
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <div class="max-h-[500px] overflow-y-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-50 text-slate-600 sticky top-0 z-10">
                                        <tr>
                                            <th class="p-4 w-12">
                                                <input type="checkbox" @change="toggleAllArticles($event)"
                                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            </th>
                                            <th class="p-4 font-semibold">Title</th>
                                            <th class="p-4 font-semibold">Authors</th>
                                            <th class="p-4 font-semibold">Status</th>
                                            <th class="p-4 font-semibold">Issue</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($submissions as $submission)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="p-4">
                                                    <input type="checkbox" name="submission_ids[]"
                                                        value="{{ $submission->id }}"
                                                        class="article-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                </td>
                                                <td class="p-4">
                                                    <span
                                                        class="font-medium text-slate-800">{{ Str::limit($submission->title, 60) }}</span>
                                                    @if ($submission->submission_code)
                                                        <span
                                                            class="block text-xs text-slate-400">{{ $submission->submission_code }}</span>
                                                    @endif
                                                </td>
                                                <td class="p-4 text-slate-600">
                                                    {{ $submission->authors->pluck('family_name')->take(2)->join(', ') }}
                                                    @if ($submission->authors->count() > 2)
                                                        <span
                                                            class="text-slate-400">+{{ $submission->authors->count() - 2 }}
                                                            more</span>
                                                    @endif
                                                </td>
                                                <td class="p-4">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if ($submission->status === 'published') bg-emerald-100 text-emerald-700
                                                        @elseif($submission->status === 'accepted') bg-blue-100 text-blue-700
                                                        @else bg-slate-100 text-slate-600 @endif">
                                                        {{ ucwords(str_replace('_', ' ', $submission->status)) }}
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
                            <p class="font-medium">No articles found</p>
                            <p class="text-sm mt-1">Submit articles to export them.</p>
                        </div>
                    @endif
                </form>
            </div>

            {{-- TAB 3: EXPORT ISSUES --}}
            <div x-show="tab === 'issues'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">

                <form action="{{ route('journal.settings.tools.native.export.issues', ['journal' => $journal->slug]) }}"
                    method="POST">
                    @csrf

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-slate-800">Select Issues to Export</h3>
                            <p class="text-sm text-slate-500">Issues will be exported with all their contained articles.
                            </p>
                        </div>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-medium text-white shadow-lg shadow-purple-500/25 transition-all flex items-center gap-2"
                            style="background: linear-gradient(to right, #a855f7, #6366f1); color: white;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export Selected Issues
                        </button>
                    </div>

                    @if ($issues->count() > 0)
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <div class="max-h-[500px] overflow-y-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-50 text-slate-600 sticky top-0 z-10">
                                        <tr>
                                            <th class="p-4 w-12">
                                                <input type="checkbox" @change="toggleAllIssues($event)"
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
                                                    @if ($issue->is_published)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                            {{ $issue->published_at?->format('M d, Y') ?? 'Published' }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                            Unpublished
                                                        </span>
                                                    @endif
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
                            <p class="font-medium">No issues found</p>
                            <p class="text-sm mt-1">Create issues to export them.</p>
                        </div>
                    @endif
                </form>
            </div>

        </div>

    </div>

    <script>
        function nativeXmlPlugin() {
            return {
                tab: 'import',
                fileName: '',

                handleDrop(event) {
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        const input = document.getElementById('xml_file');
                        input.files = files;
                        this.fileName = files[0].name;
                    }
                },

                toggleAllArticles(event) {
                    const checked = event.target.checked;
                    document.querySelectorAll('.article-checkbox').forEach(cb => cb.checked = checked);
                },

                toggleAllIssues(event) {
                    const checked = event.target.checked;
                    document.querySelectorAll('.issue-checkbox').forEach(cb => cb.checked = checked);
                }
            }
        }
    </script>
@endsection
