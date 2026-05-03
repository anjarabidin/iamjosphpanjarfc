@props(['search', 'sort', 'alpha', 'alphabet', 'brandColor' => '#00629B'])

<div class="space-y-8">
    {{-- SEARCH SECTION --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
            <i class="fa-solid fa-magnifying-glass text-indigo-600"></i>
            Search Journals
        </h3>
        <form action="{{ route('portal.journals') }}" method="GET">
            <div class="relative group">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Find by title or key..."
                       class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-4 focus:ring-indigo-50 border-transparent transition-all"
                       style="--tw-ring-color: rgba(79, 70, 229, 0.1)">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors text-sm"></i>
            </div>
            
            {{-- Preserve existing query params --}}
            @if($sort) <input type="hidden" name="sort" value="{{ $sort }}"> @endif
            @if($alpha && $alpha !== 'all') <input type="hidden" name="alpha" value="{{ $alpha }}"> @endif
            
            <button type="submit" 
                    class="w-full mt-3 py-3 text-xs font-black text-white rounded-2xl bg-indigo-600 shadow-lg shadow-indigo-50 hover:bg-indigo-700 transition-all uppercase tracking-widest">
                Search
            </button>
        </form>
    </div>

    {{-- ALPHABETICAL FILTER --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                <i class="fa-solid fa-font text-indigo-600"></i>
                Index
            </h3>
            @if($alpha && $alpha !== 'all')
                <a href="{{ route('portal.journals', array_merge(request()->except('alpha'))) }}" 
                   class="text-[10px] font-black text-red-400 hover:text-red-600 uppercase tracking-widest transition-colors">
                    Clear
                </a>
            @endif
        </div>
        
        <div class="grid grid-cols-6 gap-1.5">
            {{-- 'All' Button --}}
            <a href="{{ route('portal.journals', array_merge(request()->except('alpha'))) }}"
               class="col-span-2 h-8 flex items-center justify-center rounded-lg text-xs font-black border transition-all duration-200 uppercase tracking-tight"
               style="{{ (empty($alpha) || $alpha === 'all') 
                        ? "background-color: #4f46e5; color: white; border-color: #4f46e5;" 
                        : "background-color: #f8fafc; color: #94a3b8; border-color: #f1f5f9;" }}">
                All
            </a>
            
            {{-- Letter Buttons --}}
            @foreach($alphabet as $letter)
                @if($letter !== 'all')
                    <a href="{{ route('portal.journals', array_merge(request()->query(), ['alpha' => $letter])) }}"
                       class="h-8 flex items-center justify-center rounded-lg text-xs font-black border transition-all duration-200 hover:border-indigo-200 uppercase"
                       style="{{ $alpha === $letter 
                                ? "background-color: #4f46e5; color: white; border-color: #4f46e5;" 
                                : "background-color: #f8fafc; color: #94a3b8; border-color: #f1f5f9;" }}">
                        {{ $letter }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    {{-- SUBJECT FIELDS --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
            <i class="fa-solid fa-layer-group text-indigo-600"></i>
            Subject Fields
        </h3>
        <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1 custom-sidebar-scroll">
            @foreach([
                'Engineering & Tech', 
                'Health & Medicine', 
                'Social Sciences', 
                'Humanities', 
                'Education', 
                'Economy & Business', 
                'Agriculture', 
                'Computer & IT'
            ] as $subject)
                <label class="flex items-center gap-3 py-2 px-3 rounded-xl cursor-pointer hover:bg-slate-50 transition-all group">
                    <input type="checkbox" 
                           name="subject[]" 
                           value="{{ $subject }}" 
                           class="w-4 h-4 rounded-lg border-slate-200 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 transition-all">
                    <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">{{ $subject }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- ACCREDITATION --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
            <i class="fa-solid fa-award text-indigo-600"></i>
            Accreditation
        </h3>
        <div class="space-y-1.5">
            @foreach([
                ['value' => 'sinta-1', 'label' => 'SINTA 1', 'level' => 'S1', 'color' => 'bg-amber-100 text-amber-700'],
                ['value' => 'sinta-2', 'label' => 'SINTA 2', 'level' => 'S2', 'color' => 'bg-slate-100 text-slate-700'],
                ['value' => 'scopus', 'label' => 'Scopus Indexed', 'level' => 'SC', 'color' => 'bg-blue-100 text-blue-700'],
                ['value' => 'doaj', 'label' => 'DOAJ', 'level' => 'DJ', 'color' => 'bg-purple-100 text-purple-700'],
            ] as $accreditation)
                <label class="flex items-center gap-3 py-2 px-3 rounded-xl cursor-pointer hover:bg-slate-50 transition-all group">
                    <input type="checkbox" 
                           name="accreditation[]" 
                           value="{{ $accreditation['value'] }}" 
                           class="w-4 h-4 rounded-lg border-slate-200 text-indigo-600 focus:ring-indigo-500 transition-all">
                    <span class="flex items-center gap-3 text-sm font-medium text-slate-600 group-hover:text-slate-900">
                        <span class="w-6 h-6 flex items-center justify-center rounded text-[10px] font-black {{ $accreditation['color'] }}">
                            {{ $accreditation['level'] }}
                        </span>
                        {{ $accreditation['label'] }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>
</div>

<style>
    .custom-sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .custom-sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-sidebar-scroll::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
</style>
