{{-- Hero Search Block - Modern Light SaaS Style (PrebuiltUI) --}}
@props(['block', 'data' => []])

@php
    $config = $block->config ?? [];

    // Data (Optional binding, keeping it available if needed in future)
    $journalsCount = $data['total_journals'] ?? 50;

    // Headline configuration if dynamically passed, otherwise default to the requested text
    $headline = $config['headline'] ?? 'Discover Academic Excellence with IAMJOS.';
    $subheadline =
        $config['subheadline'] ??
        'A secure, open-access platform for managing academic journal submissions, peer reviews, and publications.';

    // Fetch top 3 popular keywords from database with caching
    $popularKeywords = Cache::remember('portal_popular_keywords_hero', 3600, function () {
        return \App\Models\Keyword::withCount('submissions')
            ->orderBy('submissions_count', 'desc')
            ->limit(3)
            ->get();
    });
@endphp

<section class="w-full bg-white pt-10 pb-16 relative overflow-hidden">
    {{-- Optional: Very subtle background decoration (mesh/blob) --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div
            class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-purple-100/50 rounded-full blur-[100px] opacity-60">
        </div>
        <div
            class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-indigo-50/50 rounded-full blur-[120px] opacity-60">
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">

        {{-- 1. SOCIAL PROOF (Avatars & Stars) --}}
        @if ($config['show_social_proof'] ?? false)
            <div class="flex flex-col sm:flex-row items-center gap-4 mb-4">
                {{-- Avatars --}}
                @if (!empty($config['logos']))
                    <div class="flex -space-x-3 overflow-hidden">
                        @foreach (array_slice($config['logos'], 0, 4) as $logo)
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-contain bg-white shadow-sm"
                                src="{{ Storage::url($logo) }}" alt="Institution Logo" />
                        @endforeach

                        @if ($journalsCount > 4)
                            <div
                                class="h-8 w-8 rounded-full ring-2 ring-white bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600 border border-slate-200 shadow-sm">
                                +{{ $journalsCount - 4 }}</div>
                        @endif
                    </div>
                @endif

                {{-- Stars & Text --}}
                <div class="flex flex-col items-start">
                    <div class="flex text-indigo-500 mb-0.5">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="text-xs text-slate-600 font-medium">Trusted by {{ $journalsCount }}+
                        Institutions</span>
                </div>
            </div>
        @endif

        {{-- 2. HEADLINE (Big & Bold with Gradient) --}}
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mb-4 max-w-4xl leading-[1.1]">
            Discover
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                Academic Excellence
            </span>
            <br class="hidden md:block" />
            with IAMJOS.
        </h1>

        {{-- 3. SUBTITLE --}}
        <p class="text-base md:text-lg text-slate-500 mb-6 max-w-2xl mx-auto leading-relaxed font-normal">
            {{ $subheadline }}
        </p>

        {{-- 4. SEARCH BAR (Pixel Perfect) --}}
        <form action="{{ route('portal.search') }}" method="GET" class="w-full max-w-2xl relative mx-auto mt-2">
            <div class="relative flex items-center group">

                {{-- Glow Effect --}}
                <div
                    class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-75 transition duration-500">
                </div>

                {{-- INPUT FIELD --}}
                <input type="text" name="q"
                    class="relative w-full h-14 pl-6 pr-32 rounded-2xl border-2 border-transparent bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 text-base shadow-2xl transition-all"
                    placeholder="Search titles, abstracts, or keywords...">

                {{-- BUTTON (Floating Inside) --}}
                <div class="absolute right-1.5 top-1.5 bottom-1.5">
                    <button type="submit"
                        class="h-full px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-95 flex items-center justify-center text-sm">
                        Search
                    </button>
                </div>

            </div>
        </form>

        {{-- 5. QUICK TAGS --}}
        @if($popularKeywords->isNotEmpty())
            <div class="mt-6 flex flex-wrap justify-center items-center gap-2 text-xs md:text-sm text-slate-500">
                <span class="font-medium text-slate-400">Popular Queries:</span>
                @foreach($popularKeywords as $keyword)
                    <a href="{{ route('portal.search', ['q' => $keyword->content]) }}"
                        class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 transition-colors border border-slate-200/50">
                        {{ $keyword->content }}
                    </a>
                @endforeach
            </div>
        @endif

    </div>
</section>
