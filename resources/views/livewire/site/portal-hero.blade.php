<div class="relative w-full min-h-[38vh] flex flex-col items-center justify-start overflow-hidden bg-[#020617] isolate pt-6 pb-10">

    {{-- 1. NOISE TEXTURE --}}
    <div
        class="absolute inset-0 z-20 opacity-[0.03] pointer-events-none mix-blend-overlay"
        style="background-image: url(&quot;data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E&quot;);">
    </div>

    {{-- 2. AMBIENT GLOW --}}
    <div class="absolute top-[-12%] left-[-12%] w-[32rem] h-[32rem] bg-blue-600/20 rounded-full blur-[128px] pointer-events-none z-10"></div>
    <div class="absolute bottom-[-12%] right-[-12%] w-[32rem] h-[32rem] bg-indigo-600/10 rounded-full blur-[128px] pointer-events-none z-10"></div>

    {{-- 3. MAIN CONTENT --}}
    <div class="relative z-30 max-w-5xl mx-auto px-4 text-center">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-blue-300 text-xs font-medium mb-3 backdrop-blur-md">
            <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75 animate-ping"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
            </span>
            Research & Innovation
        </div>

        {{-- Headline --}}
        <h1 class="text-3xl md:text-5xl font-bold tracking-tight text-white mb-3 leading-tight">
            Discover
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">
                Academic Excellence
            </span>
        </h1>

        {{-- Subheadline --}}
        <p class="text-sm md:text-base text-slate-400 mb-6 max-w-2xl mx-auto leading-relaxed">
            Access over
            <span class="text-white font-bold">{{ number_format($stats['articles'] ?? 0) }}</span>
            peer-reviewed articles and join a community of
            <span class="text-white font-bold">{{ number_format($stats['authors'] ?? 0) }}</span>
            scholars pushing the boundaries of knowledge.
        </p>

        {{-- Search Bar --}}
        <div class="max-w-3xl mx-auto relative group mb-5">

            {{-- Glow --}}
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 rounded-full blur opacity-40 group-hover:opacity-60 transition duration-700"></div>

            <form action="{{ route('portal.search') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="q"
                    wire:model.live.debounce.200ms="query"
                    placeholder="Search titles, keywords, authors..."
                    class="block w-full h-14 pl-8 pr-32 rounded-full bg-white border border-transparent text-slate-900 placeholder-slate-500 text-base shadow-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all font-light"
                    autocomplete="off"
                >

                <button
                    type="submit"
                    class="absolute right-2 top-2 h-10 px-6 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-medium text-sm shadow-lg shadow-blue-900/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
                >
                    Search
                </button>
            </form>

            {{-- Live Suggestions --}}
            @if(!empty($suggestions) && strlen($query) >= 2)
                <div class="absolute top-full left-0 right-0 mt-2 p-2 bg-white/95 backdrop-blur-3xl border border-slate-200 rounded-2xl shadow-2xl text-left overflow-hidden ring-1 ring-slate-900/5 z-50">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 py-2">
                        Suggested Results
                    </div>

                    @foreach($suggestions as $result)
                        <a href="{{ $result['url'] }}" class="flex items-start gap-3 px-3 py-3 rounded-xl hover:bg-slate-100 transition-colors">
                            <div class="mt-0.5 w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="{{ $result['icon'] }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-slate-900 truncate">
                                    {{ $result['title'] }}
                                </h4>
                                <p class="text-xs text-slate-500 capitalize">
                                    {{ $result['type'] }}
                                </p>
                            </div>
                        </a>
                    @endforeach

                    <a
                        href="{{ route('portal.search', ['q' => $query]) }}"
                        class="block px-3 py-3 text-center text-sm text-blue-600 font-medium border-t border-slate-100"
                    >
                        View all results for "{{ $query }}" →
                    </a>
                </div>
            @endif
        </div>

        {{-- Stats --}}
        <!-- <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">

            <div class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm min-h-[90px]">
                <i class="fa-solid fa-book-bookmark text-lg text-blue-400 mb-1"></i>
                <div class="text-xl font-bold text-white">{{ number_format($stats['journals'] ?? 0) }}</div>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Journals</div>
            </div>

            <div class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm min-h-[90px]">
                <i class="fa-solid fa-file-lines text-lg text-emerald-400 mb-1"></i>
                <div class="text-xl font-bold text-white">{{ number_format($stats['articles'] ?? 0) }}</div>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Articles</div>
            </div>

            <div class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm min-h-[90px]">
                <i class="fa-solid fa-users text-lg text-purple-400 mb-1"></i>
                <div class="text-xl font-bold text-white">{{ number_format($stats['authors'] ?? 0) }}</div>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Authors</div>
            </div>

            <div class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm min-h-[90px]">
                <i class="fa-solid fa-globe text-lg text-cyan-400 mb-1"></i>
                <div class="text-xl font-bold text-white">Global</div>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Reach</div>
            </div>

        </div> -->

    </div>
</div>
