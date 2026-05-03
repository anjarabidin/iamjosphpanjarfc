@php $title = 'Announcements'; @endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8 border-b border-gray-200 pb-4">
            <h1 class="text-3xl font-bold text-gray-900">Announcements</h1>
            
            {{-- Journal Introduction --}}
            @if($journal->announcements_introduction)
                <div class="mt-4 prose max-w-none text-gray-700">
                    {!! clean($journal->announcements_introduction) !!}
                </div>
            @endif
        </div>

        @if($announcements->isEmpty())
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            No announcements have been published.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="grid gap-6">
                @foreach($announcements as $announcement)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <span class="flex items-center">
                                    <i class="fa-regular fa-calendar mr-1"></i>
                                    {{ $announcement->published_at ? $announcement->published_at->format('F d, Y') : $announcement->created_at->format('F d, Y') }}
                                </span>
                            </div>

                            <a href="{{ route('journal.announcement.show', ['journal' => $journal->slug, 'id' => $announcement->id]) }}" class="block mt-2">
                                <h2 class="text-xl font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                                    {{ $announcement->title }}
                                </h2>
                            </a>
                            
                            <div class="mt-3 text-base text-gray-600 line-clamp-3">
                                {!! clean(strip_tags($announcement->excerpt ?? $announcement->content)) !!}
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('journal.announcement.show', ['journal' => $journal->slug, 'id' => $announcement->id]) }}" class="text-indigo-600 hover:text-indigo-500 font-medium text-sm flex items-center">
                                    Read more <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</x-public-layout>
