<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Journal - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Select Arrow */
        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
            padding-right: 2.75rem;
        }
    </style>
</head>

<body class="font-sans antialiased text-slate-800 bg-slate-50 min-h-screen relative overflow-x-hidden" 
      x-data="{ 
          search: '', 
          filter: 'all',
          journals: @js($journals->map(fn($j) => ['name' => $j->name, 'abbr' => $j->abbreviation, 'enabled' => (bool)$j->enabled])),
          get visibleCount() {
              if (!this.journals || this.journals.length === 0) return 0;
              let count = 0;
              for (let j of this.journals) {
                  let matchesSearch = true;
                  if (this.search !== '') {
                      const q = this.search.toLowerCase();
                      matchesSearch = j.name.toLowerCase().includes(q) || (j.abbr && j.abbr.toLowerCase().includes(q));
                  }
                  let matchesFilter = true;
                  if (this.filter === 'active') matchesFilter = j.enabled;
                  if (this.filter === 'inactive') matchesFilter = !j.enabled;
                  
                  if (matchesSearch && matchesFilter) count++;
              }
              return count;
          },
          matchesSearch(name, abbr) {
              if (this.search === '') return true;
              const q = this.search.toLowerCase();
              return name.toLowerCase().includes(q) || (abbr && String(abbr).toLowerCase().includes(q));
          },
          matchesFilter(enabled) {
              if (this.filter === 'all') return true;
              if (this.filter === 'active') return enabled === true;
              if (this.filter === 'inactive') return enabled === false;
              return true;
          }
      }">

    <!-- Top Navigation Bar -->
    <div class="bg-primary-700 shadow-md relative z-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center font-bold text-white shadow-inner">
                    {{ auth()->user()->initials ?? strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-white font-semibold leading-tight text-sm sm:text-base">{{ auth()->user()->name }}</h2>
                    <p class="text-primary-100 text-xs sm:text-sm leading-tight">{{ auth()->user()->email }}</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-primary-800 hover:bg-primary-900 border border-primary-600 rounded-lg text-sm font-medium transition-all shadow-sm text-white">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">Sign Out</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Background Decor (Light Mode) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-primary-100/40 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-indigo-100/40 blur-[120px] rounded-full"></div>
    </div>

    <div class="relative z-10 flex flex-col p-6 lg:p-10 min-h-[calc(100vh-88px)]">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto w-full flex-1 mt-4">
            <!-- Header & Search/Filter -->
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-2 tracking-tight">Select Your Journal</h1>
                    <p class="text-slate-500 max-w-lg">Choose a journal to access its dashboard, manage submissions, and review articles.</p>
                </div>
                
                @if ($journals->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-72">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" x-model="search" placeholder="Search journals by name or abbr..." 
                            class="w-full pl-11 pr-4 py-2 bg-white border border-gray-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 rounded-lg text-slate-800 placeholder-gray-400 transition-all shadow-sm">
                    </div>
                    
                    <!-- Filter -->
                    <div class="relative w-full sm:w-48">
                        <select x-model="filter" class="w-full pl-4 pr-10 py-2 bg-white border border-gray-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 rounded-lg text-slate-800 transition-all appearance-none cursor-pointer shadow-sm">
                            <option value="all">All Status</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>

            <!-- Empty State No Journals At All -->
            @if ($journals->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-100">
                        <i class="fa-solid fa-book-open-reader text-3xl text-slate-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3">No Journals Available</h3>
                    <p class="text-slate-500 mb-8 max-w-md mx-auto">There are no journals currently available or assigned to your account.</p>
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-all shadow-sm">
                        <i class="fa-solid fa-user-plus"></i>
                        Join a Journal
                    </a>
                </div>
            @else
                
                @if(isset($showJoinOption) && $showJoinOption)
                    <!-- Notice for Unassigned Users -->
                    <div class="mb-8 p-5 bg-amber-50 rounded-2xl border border-amber-200 flex flex-col sm:flex-row items-center gap-5 sm:gap-6 relative overflow-hidden">
                        <div class="relative w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center shrink-0 border border-amber-200">
                            <i class="fa-solid fa-circle-exclamation text-xl text-amber-600"></i>
                        </div>
                        <div class="relative text-center sm:text-left">
                            <h3 class="text-lg font-bold text-amber-900 mb-1">You haven't joined any journal yet</h3>
                            <p class="text-amber-700 text-sm">Browse the available journals below and click to register or join them.</p>
                        </div>
                    </div>
                @endif

                <!-- Journals Grid -->
                <!-- Use CSS Grid for dynamic columns (1 on mobile, 2 on tablet, 3-4 on desktop) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" x-show="visibleCount > 0" x-cloak>
                    @foreach ($journals as $index => $journal)
                        @php
                            $isUserJournal = isset($userJournals) && $userJournals->contains('id', $journal->id);
                            // Avoid quoting issues in Alpine
                            $jName = addcslashes($journal->name, "'\\");
                            $jAbbr = addcslashes($journal->abbreviation ?? '', "'\\");
                            $jEnabled = $journal->enabled ? 'true' : 'false';
                        @endphp
                        <a href="{{ $isUserJournal ? route('journal.select.go', ['journal' => $journal->slug]) : route('register', ['journal' => $journal->slug]) }}"
                            x-show="matchesSearch('{{ $jName }}', '{{ $jAbbr }}') && matchesFilter({{ $jEnabled }})"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="group relative flex flex-col bg-white rounded-2xl border {{ $isUserJournal ? 'border-primary-200' : 'border-gray-200' }} shadow-sm p-6 transition-all duration-200 hover:border-primary-500 hover:shadow-md hover:shadow-primary-100 overflow-hidden">
                            
                            @if(!$isUserJournal && isset($showJoinOption) && $showJoinOption)
                                <!-- Join Badge -->
                                <div class="absolute -top-1 -right-1 px-4 py-1.5 bg-primary-600 text-white text-[10px] font-bold uppercase tracking-wider rounded-bl-xl rounded-tr-2xl shadow-sm">
                                    Join
                                </div>
                            @endif

                            <!-- Header: Logo & Status -->
                            <div class="flex items-start justify-between mb-5 relative z-10">
                                <div class="w-16 h-16 shrink-0 rounded-xl bg-slate-50 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:border-primary-200 transition-colors">
                                    @if ($journal->logo_path)
                                        <img src="{{ Storage::url($journal->logo_path) }}" alt="{{ $journal->name }}" class="w-full h-full object-contain p-2">
                                    @else
                                        <div class="w-full h-full bg-primary-50 flex items-center justify-center">
                                            <span class="text-2xl font-bold text-primary-600">{{ strtoupper(substr($journal->abbreviation ?? $journal->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md {{ $journal->enabled ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                    {{ $journal->enabled ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 mb-6 relative z-10">
                                <h3 class="text-lg font-bold text-slate-800 mb-1.5 leading-tight group-hover:text-primary-700 transition-colors line-clamp-2">
                                    {{ $journal->name }}
                                </h3>
                                @if ($journal->abbreviation)
                                    <p class="text-sm font-semibold text-primary-600 mb-3">{{ $journal->abbreviation }}</p>
                                @endif
                                <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $journal->description ?? 'No description available for this journal.' }}
                                </p>
                            </div>

                            <!-- Footer Stats -->
                            <div class="mt-auto pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2 text-xs font-medium text-slate-500 relative z-10">
                                <div class="flex gap-3">
                                    @if ($journal->issn_print || $journal->issn_online)
                                        <span class="flex items-center gap-1.5 bg-primary-50 text-primary-700 px-2.5 py-1 rounded border border-primary-100" title="ISSN">
                                            <i class="fa-solid fa-barcode text-primary-500"></i>
                                            {{ $journal->issn_online ?? $journal->issn_print }}
                                        </span>
                                    @endif
                                    
                                    <span class="flex items-center gap-1.5 bg-primary-50 text-primary-700 px-2.5 py-1 rounded border border-primary-100" title="Articles">
                                        <i class="fa-regular fa-file-lines text-primary-500"></i>
                                        {{ $journal->submissions_count ?? $journal->submissions()->count() }}
                                    </span>
                                </div>
                                
                                <!-- Go Icon -->
                                <div class="w-8 h-8 rounded-full bg-primary-50 flex items-center justify-center text-primary-600 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all">
                                    <i class="fa-solid fa-arrow-right text-sm"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Empty Search Result State -->
                <div x-show="visibleCount === 0 && journals.length > 0" x-cloak
                    class="text-center py-16 bg-white rounded-2xl border border-gray-200 shadow-sm mt-6">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-gray-200">
                        <i class="fa-solid fa-search text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">No matches found</h3>
                    <p class="text-slate-500">We couldn't find any journal matching your current search or filters.</p>
                    <button @click.prevent="search = ''; filter = 'all'" class="mt-6 px-4 py-2 border border-gray-300 rounded-lg text-slate-700 hover:bg-slate-50 font-medium text-sm transition-all shadow-sm">
                        Clear all filters
                    </button>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center text-sm font-medium text-slate-500 flex-none pb-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
