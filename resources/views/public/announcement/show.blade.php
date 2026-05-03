<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">
    <x-slot name="title">{{ $announcement->title }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ route('journal.public.home', $journal->slug) }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-home"></i>
                    </a>
                </li>
                <li>
                    <span class="text-gray-300">/</span>
                </li>
                <li>
                    <a href="{{ route('journal.announcement.index', $journal->slug) }}" class="text-gray-500 hover:text-gray-700 font-medium">
                        Announcements
                    </a>
                </li>
            </ol>
        </nav>
        
        <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $announcement->title }}</h1>
                    <div class="flex items-center text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fa-regular fa-calendar mr-2"></i>
                            {{ $announcement->published_at ? $announcement->published_at->format('F d, Y') : $announcement->created_at->format('F d, Y') }}
                        </span>
                    </div>
                </div>

                <div class="prose prose-lg prose-indigo max-w-none text-gray-700">
                    {!! clean($announcement->content) !!}
                </div>
            </div>
        </article>
        
        <div class="mt-8 text-center">
             <a href="{{ route('journal.announcement.index', $journal->slug) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Announcements
            </a>
        </div>
    </div>
</x-layouts.public>
