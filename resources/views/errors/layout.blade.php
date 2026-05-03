<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'IAMJOS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: { 'blob': 'blob 15s infinite' },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -40px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { margin: 0; padding: 0; }
    </style>
</head>

<body class="bg-[#f8faff] h-screen w-screen overflow-hidden relative selection:bg-indigo-500 selection:text-white font-sans antialiased flex items-center justify-center">

    @php
        $journal = current_journal();
        $brandingName = $journal ? $journal->name : 'IAMJOS';
        $brandingLogo = $journal && $journal->logo ? asset('storage/' . $journal->logo) : null;
        $redirectUrl = $journal ? route('journal.public.home', $journal->slug) : url('/dashboard');
        $contextName = $journal ? 'Home' : 'Dashboard';
    @endphp

    <!-- Mesh Gradient Background & Grid -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9InJnYmEoOTksIDEwMiwgMjQxLCAwLjAzKSIvPjwvc3ZnPg==')] opacity-60"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_50%_0%,rgba(99,102,241,0.05)_0%,transparent_50%)]"></div>
        <div class="absolute top-[20%] left-[20%] w-72 h-72 bg-purple-200/40 rounded-full filter blur-[80px] animate-blob mix-blend-multiply"></div>
        <div class="absolute bottom-[20%] right-[20%] w-80 h-80 bg-indigo-200/40 rounded-full filter blur-[80px] animate-blob animation-delay-2000 mix-blend-multiply"></div>
    </div>

    <!-- Main Card -->
    <main x-data="{ 
             countdown: 15,
             redirectUrl: '{{ $redirectUrl }}',
             init() {
                 this.timer = setInterval(() => {
                     if (this.countdown > 0) this.countdown--;
                     else { clearInterval(this.timer); window.location.href = this.redirectUrl; }
                 }, 1000);
             },
             cancelRedirect() {
                 clearInterval(this.timer);
                 this.countdown = null;
             }
         }"
         class="relative z-10 w-full max-w-[380px] p-4">
    
        <div class="w-full bg-white/80 backdrop-blur-2xl rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] border border-white/50 flex flex-col items-center p-8 text-center transition-all duration-700"
             x-show="true"
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            <!-- Branding -->
            <div class="mb-6">
                @if($brandingLogo)
                    <img src="{{ $brandingLogo }}" alt="{{ $brandingName }}" class="h-10 w-auto object-contain mx-auto">
                @else
                    <div class="mx-auto w-12 h-12 bg-indigo-600 rounded-[14px] flex items-center justify-center text-white text-lg font-bold shadow-lg shadow-indigo-200/50">
                        {{ substr($brandingName, 0, 2) }}
                    </div>
                @endif
                <p class="mt-3 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">{{ $brandingName }}</p>
            </div>

            <!-- Content -->
            <div class="mb-2">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-50/80 rounded-2xl text-indigo-500 mb-5 shadow-inner shadow-indigo-100/50 ring-1 ring-indigo-50">
                    @yield('icon')
                </div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight leading-tight">@yield('title')</h1>
                <p class="mt-3 text-slate-500 text-[13px] leading-relaxed px-1">@yield('message')</p>
            </div>

            <!-- Auto Redirect / Actions -->
            <div class="w-full mt-6" x-show="countdown !== null">
                <!-- Progress -->
                <div class="flex justify-between text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                    <span>Redirecting...</span>
                    <span x-text="countdown + 's'"></span>
                </div>
                <div class="h-1 w-full bg-slate-100 rounded-full overflow-hidden mb-6">
                    <div class="h-full bg-indigo-600 transition-all duration-1000 ease-linear shadow-[0_0_10px_rgba(79,70,229,0.3)]"
                         :style="'width: ' + ((countdown / 15) * 100) + '%'"></div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-3">
                    <button @click="window.location.href = redirectUrl" 
                            class="w-full py-3 bg-indigo-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-indigo-200/50 hover:bg-indigo-700 hover:shadow-indigo-200 active:scale-[0.98] transition-all duration-200">
                        Continue to {{ $contextName }}
                    </button>
                    <button @click="cancelRedirect()" 
                            class="w-full py-3 bg-white text-slate-500 border border-slate-200 rounded-xl font-medium text-sm hover:bg-slate-50 hover:text-slate-700 transition-all duration-200">
                        Stay on this page
                    </button>
                </div>
            </div>

            <!-- Manual Action (shown when cancelled) -->
            <div class="w-full mt-8" x-show="countdown === null" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <a href="{{ $redirectUrl }}" 
                   class="inline-flex items-center justify-center w-full py-3 bg-indigo-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-indigo-200/50 hover:bg-indigo-700 hover:shadow-indigo-200 active:scale-[0.98] transition-all duration-200 group">
                   <span>Continue to {{ $contextName }}</span>
                   <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <p class="mt-3 text-[10px] text-slate-400 font-medium">Auto-redirect cancelled.</p>
            </div>

            <footer class="mt-8 pt-6 border-t border-slate-50 w-full">
                <p class="text-[9px] text-slate-300 font-bold uppercase tracking-[0.2em] hover:text-slate-400 transition-colors cursor-default">
                    &copy; {{ date('Y') }} {{ config('app.name', 'IAMJOS') }}
                </p>
            </footer>
        </div>
    </main>

</body>
</html>