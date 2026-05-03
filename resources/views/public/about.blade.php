@php $title = 'About the Journal'; @endphp

<x-layouts.public :journal="$journal" :settings="$settings" :title="$title">

    <section class="bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">About the Journal</h1>

            {{-- About Content (HTML from Settings) --}}
            @php
                $aboutContent = $about ?? $journal->settings['masthead']['about'] ?? '';
            @endphp
            @if(!empty($aboutContent))
            <div class="prose prose-lg max-w-none mb-12">
                {!! clean($aboutContent) !!}
            </div>
            @else
            {{-- No custom about content set --}}
            <div class="text-center py-12">
                <div class="text-gray-500">
                    <i class="fa-solid fa-info-circle text-4xl mb-4"></i>
                    <p>About content has not been configured yet.</p>
                </div>
            </div>
            @endif
        </div>
    </section>
</x-layouts.public>