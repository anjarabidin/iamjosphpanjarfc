<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">
    <x-slot name="title">{{ $title }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            {{-- Sidebar --}}
            <aside class="hidden lg:block lg:col-span-3">
                <nav class="space-y-1">
                    <a href="{{ route('journal.info.readers', ['journal' => $journal->slug]) }}"
                       class="{{ Route::is('journal.info.readers') ? 'bg-gray-100 text-gray-900 border-l-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent' }} group flex items-center px-3 py-2 text-sm font-medium">
                        <span class="truncate">For Readers</span>
                    </a>

                    <a href="{{ route('journal.info.authors', ['journal' => $journal->slug]) }}"
                       class="{{ Route::is('journal.info.authors') ? 'bg-gray-100 text-gray-900 border-l-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent' }} group flex items-center px-3 py-2 text-sm font-medium">
                        <span class="truncate">For Authors</span>
                    </a>

                    <a href="{{ route('journal.info.librarians', ['journal' => $journal->slug]) }}"
                       class="{{ Route::is('journal.info.librarians') ? 'bg-gray-100 text-gray-900 border-l-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent' }} group flex items-center px-3 py-2 text-sm font-medium">
                        <span class="truncate">For Librarians</span>
                    </a>
                </nav>
            </aside>

            {{-- Main Content --}}
            <div class="mt-8 lg:mt-0 lg:col-span-9">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @if($content)
                            <div class="prose max-w-none text-gray-700">
                                {!! clean($content) !!}
                            </div>
                        @else
                            <div class="rounded-md bg-blue-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <!-- Heroicon name: solid/information-circle -->
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            No information available for this section.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
