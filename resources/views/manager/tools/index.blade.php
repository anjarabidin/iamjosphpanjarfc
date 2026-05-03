@extends('layouts.app')

@section('title', 'Tools - ' . $journal->name)

@section('content')
    <div x-data="toolsPage()" class="space-y-6">

        {{-- HEADER --}}
        <div
            class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200/60 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text">
                    Tools
                </h1>
                <p class="text-sm text-slate-500 mt-1">Utilities for import, export, and system maintenance.</p>
            </div>

            {{-- Search Bar (Visible only on Import Tab) --}}
            <div x-show="activeTab === 'import'" x-transition class="w-full md:w-auto">
                <div class="relative">
                    <input type="text" x-model="search" placeholder="Search tools..."
                        class="w-full md:w-72 pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-400 focus:ring focus:ring-indigo-100 transition-all text-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
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

        {{-- TABS NAVIGATION --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="border-b border-slate-200">
                <nav class="flex">
                    <button @click="activeTab = 'import'"
                        :class="activeTab === 'import' ?
                            'border-indigo-500 text-indigo-600 bg-indigo-50/50' :
                            'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                        class="flex-1 md:flex-none whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Import / Export
                    </button>
                    <button @click="activeTab = 'permissions'"
                        :class="activeTab === 'permissions' ?
                            'border-red-500 text-red-600 bg-red-50/50' :
                            'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                        class="flex-1 md:flex-none whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        Permissions
                    </button>
                </nav>
            </div>

            {{-- CONTENT: IMPORT / EXPORT --}}
            <div x-show="activeTab === 'import'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($tools as $tool)
                        <div class="bg-white rounded-xl border border-slate-200 hover:border-{{ $tool['color'] }}-400 hover:shadow-lg transition-all group flex flex-col h-full"
                            x-show="'{{ strtolower($tool['title'] . ' ' . $tool['description']) }}'.includes(search.toLowerCase())"
                            x-transition>

                            <div class="p-5 flex-grow">
                                <div class="flex items-start gap-4 mb-4">
                                    <div
                                        class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-{{ $tool['color'] }}-50 text-{{ $tool['color'] }}-600 group-hover:bg-{{ $tool['color'] }}-100 transition-colors">
                                        @switch($tool['icon'])
                                            @case('scholar')
                                                <i class="fa-brands fa-google-scholar text-2xl"></i>
                                            @break

                                            @case('code-bracket')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                                </svg>
                                            @break

                                            @case('users')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                    </path>
                                                </svg>
                                            @break

                                            @case('link')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                                    </path>
                                                </svg>
                                            @break

                                            @case('beaker')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                                    </path>
                                                </svg>
                                            @break

                                            @case('globe-alt')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                            @break

                                            @case('document-text')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            @break

                                            @case('bolt')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            @break

                                            @default
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                                                    </path>
                                                </svg>
                                        @endswitch
                                    </div>
                                    <div class="flex-grow">
                                        <h3
                                            class="font-bold text-slate-800 group-hover:text-{{ $tool['color'] }}-600 transition-colors">
                                            {{ $tool['title'] }}
                                        </h3>
                                        <span
                                            class="inline-flex items-center text-xs font-semibold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full mt-1">
                                            Plugin
                                        </span>
                                    </div>
                                </div>

                                <p class="text-sm text-slate-500 leading-relaxed">
                                    {{ $tool['description'] }}
                                </p>
                            </div>

                            <div class="px-5 pb-5">
                                <a href="{{ $tool['route'] }}"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border border-slate-200 text-slate-600 hover:bg-{{ $tool['color'] }}-600 hover:text-white hover:border-{{ $tool['color'] }}-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                    Open Tool
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty State --}}
                <div x-show="!hasVisibleTools()" x-cloak class="text-center py-16 text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="font-medium">No tools found matching your search.</p>
                    <p class="text-sm mt-1">Try a different keyword.</p>
                </div>
            </div>

            {{-- CONTENT: PERMISSIONS --}}
            <div x-show="activeTab === 'permissions'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">

                <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
                    {{-- Header --}}
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-red-800">Reset Article Permissions</h3>
                            <p class="text-xs text-red-600">Danger Zone - This action cannot be undone</p>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-6">
                        <p class="text-slate-700 mb-4 leading-relaxed">
                            Remove the copyright statement and license information for every published article, reverting
                            them to the journal's current default settings.
                            This will <strong class="text-red-600">permanently remove all prior copyright and licensing
                                information</strong> attached to articles.
                        </p>

                        {{-- Legal Warning --}}
                        <div class="bg-amber-50 border-l-4 border-amber-400 rounded-r-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800 mb-1">Legal Warning</p>
                                    <p class="text-sm text-amber-700">
                                        In some cases, you may not be legally permitted to re-license work that has been
                                        published under a different license.
                                        Please take caution when using this tool and consult legal expertise if you are
                                        unsure what rights you hold over the articles published in your journal.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div x-data="{ confirmReset: false }">
                            <button @click="confirmReset = true"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/25 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Reset Article Permissions
                            </button>

                            {{-- CONFIRMATION MODAL --}}
                            <div x-show="confirmReset" x-cloak
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100" @click.outside="confirmReset = false">

                                    <div class="p-6">
                                        <div
                                            class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                        </div>

                                        <h3 class="text-lg font-bold text-slate-900 text-center mb-2">Are you absolutely
                                            sure?</h3>
                                        <p class="text-sm text-slate-500 text-center mb-6">
                                            This action cannot be undone. All custom licenses on existing published articles
                                            will be overwritten by the current journal default settings.
                                        </p>

                                        <div class="flex gap-3">
                                            <button @click="confirmReset = false"
                                                class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                                                Cancel
                                            </button>
                                            <form
                                                action="{{ route('journal.settings.tools.permissions.reset', ['journal' => $journal->slug]) }}"
                                                method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full px-4 py-2.5 rounded-xl text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                                                    Yes, Reset Everything
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function toolsPage() {
            return {
                activeTab: 'import',
                search: '',
                tools: @json($tools),

                hasVisibleTools() {
                    if (!this.search) return true;
                    const searchLower = this.search.toLowerCase();
                    return this.tools.some(tool =>
                        (tool.title + ' ' + tool.description).toLowerCase().includes(searchLower)
                    );
                }
            }
        }
    </script>
@endsection
