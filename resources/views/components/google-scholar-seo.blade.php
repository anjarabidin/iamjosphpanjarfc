<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden h-full">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fa-brands fa-google-scholar text-blue-500 text-lg"></i>
            Google Scholar Forecaster
        </h3>

        @php
            $statusColor = match ($analysis['status']) {
                'good' => 'text-green-600 bg-green-50 border-green-200',
                'warning' => 'text-amber-600 bg-amber-50 border-amber-200',
                'bad' => 'text-red-600 bg-red-50 border-red-200',
                default => 'text-gray-600 bg-gray-50 border-gray-200',
            };

            $scoreColor = match ($analysis['status']) {
                'good' => 'text-green-500',
                'warning' => 'text-amber-500',
                'bad' => 'text-red-500',
                default => 'text-gray-500',
            };

            $progressColor = match ($analysis['status']) {
                'good' => 'text-green-500',
                'warning' => 'text-amber-500',
                'bad' => 'text-red-500',
                default => 'text-gray-500',
            };
        @endphp

        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusColor }}">
            {{ ucfirst($analysis['status']) }}
        </span>
    </div>

    <div class="p-6">
        {{-- Score Section --}}
        <div class="flex flex-col items-center justify-center mb-10">
            {{-- Circular Progress --}}
            <div class="relative w-32 h-32">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle class="text-gray-100" stroke-width="8" stroke="currentColor" fill="transparent" r="40"
                        cx="50" cy="50" />
                    <circle class="{{ $progressColor }} transition-all duration-1000 ease-out" stroke-width="8"
                        stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50"
                        cy="50" stroke-dasharray="251.2"
                        stroke-dashoffset="{{ 251.2 - (251.2 * $analysis['score']) / 100 }}" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-4xl font-extrabold {{ $scoreColor }}">{{ $analysis['score'] }}</span>
                </div>
            </div>
            <div class="mt-2 text-center">
                <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Indexing Requirement Score</span>
            </div>
        </div>

        {{-- Checklist --}}
        <div class="space-y-4">
            @foreach ($analysis['checks'] as $check)
                <div
                    class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                    <div class="flex-shrink-0 mt-0.5">
                        @if ($check['status'] === true)
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fa-solid fa-check text-green-600 text-xs"></i>
                            </div>
                        @elseif($check['status'] === 'warning')
                            <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center">
                                <i class="fa-solid fa-exclamation text-amber-600 text-xs"></i>
                            </div>
                        @else
                            <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fa-solid fa-xmark text-red-600 text-xs"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900">{{ $check['label'] }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ $check['message'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100">
            <p class="text-xs text-center text-gray-400">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Based on Google Scholar Indexing Guidelines
            </p>
        </div>
    </div>
</div>
