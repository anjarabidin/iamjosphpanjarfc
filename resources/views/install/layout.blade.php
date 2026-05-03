<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IAMJOS - Installation Wizard</title>
    <!-- Tailwind CSS (via CDN for installer to not rely on Vite build initially) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    
    <div class="flex-grow flex items-center justify-center py-10 px-4">
        <div class="max-w-3xl w-full bg-white shadow-xl rounded-lg overflow-hidden border border-gray-100">
            
            <!-- Header -->
            <div class="bg-indigo-600 text-white p-6">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    IAMJOS Installation Wizard
                </h1>
                <p class="text-indigo-100 text-sm mt-1">Configure your system in 4 easy steps</p>
            </div>

            <!-- Progress Bar -->
            <div class="bg-gray-50 border-b border-gray-200">
                <div class="flex items-center">
                    @php
                        $steps = [
                            1 => ['name' => 'Requirements', 'route' => route('install.index')],
                            2 => ['name' => 'Database', 'route' => route('install.step2')],
                            3 => ['name' => 'SMTP Mail', 'route' => route('install.step3')],
                            4 => ['name' => 'Final Setup', 'route' => route('install.step4')],
                        ];
                    @endphp

                    @foreach($steps as $number => $stepData)
                        <div class="flex-1 relative @if(!$loop->last) border-r border-gray-200 @endif">
                            <div class="py-3 px-4 text-center {{ isset($step) && $step == $number ? 'bg-indigo-50 border-b-2 border-indigo-600 text-indigo-700 font-medium' : (isset($step) && $step > $number ? 'text-gray-900 bg-white' : 'text-gray-400') }}">
                                <span class="hidden sm:inline">Step {{ $number }}:</span> {{ $stepData['name'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 border-b border-red-200 text-sm font-medium">
                {{ session('error') }}
            </div>
            @endif
            @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 border-b border-green-200 text-sm font-medium">
                {{ session('success') }}
            </div>
            @endif

            <!-- Main Content -->
            <div class="p-6 sm:p-10">
                @yield('content')
            </div>
            
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-6 text-center text-gray-400 text-sm">
        &copy; {{ date('Y') }} IAMJOS. All rights reserved.
    </footer>

</body>
</html>
