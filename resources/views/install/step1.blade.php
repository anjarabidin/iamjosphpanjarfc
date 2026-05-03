@extends('install.layout', ['step' => 1])

@section('content')

<div class="space-y-8">
    <div>
        <h2 class="text-xl font-bold text-gray-800">System Requirements</h2>
        <p class="text-gray-500 text-sm mt-1">Please ensure your server meets all the minimum requirements before proceeding.</p>
    </div>

    <!-- PHP Version -->
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <span class="font-medium text-gray-700">PHP Version</span>
                <span class="text-xs text-gray-500 block">Required: 8.2 or higher</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 border px-2 py-1 bg-white rounded">{{ PHP_VERSION }}</span>
                @if($requirements['php'])
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                @endif
            </div>
        </div>
    </div>

    <!-- PHP Extensions -->
    <div>
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Required Extensions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($requirements['extensions'] as $ext => $loaded)
            <div class="flex items-center justify-between border border-gray-100 bg-white p-3 rounded-md shadow-sm">
                <span class="text-gray-700 font-mono text-sm">{{ $ext }}</span>
                @if($loaded)
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Enabled
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 px-2 py-1 rounded">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Missing
                    </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Directory Permissions -->
    <div>
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Directory Permissions</h3>
        <div class="space-y-2">
            @foreach($requirements['permissions'] as $dir => $writable)
            <div class="flex items-center justify-between border border-gray-100 bg-white p-3 rounded-md shadow-sm">
                <span class="text-gray-700 font-mono text-sm">{{ $dir }}</span>
                @if($writable)
                    <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded border border-green-200">Writable</span>
                @else
                    <span class="text-xs font-medium text-red-700 bg-red-50 px-2 py-1 rounded border border-red-200">Not Writable</span>
                @endif
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 mt-2 italic">Ensure the web server user has write permissions to these paths.</p>
    </div>

    <!-- Actions -->
    <div class="pt-6 border-t border-gray-200 flex justify-end gap-3">
        <a href="{{ route('install.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition text-sm font-medium">
            Re-Check
        </a>
        
        <a href="{{ $requirements['allPass'] ? route('install.step2') : '#' }}" 
           class="px-5 py-2.5 rounded-md text-sm font-medium transition {{ $requirements['allPass'] ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm' : 'bg-indigo-300 text-white cursor-not-allowed' }}"
           @if(!$requirements['allPass']) onclick="event.preventDefault(); alert('Please resolve all requirements before proceeding.');" @endif>
            Next Step &rarr;
        </a>
    </div>
</div>

@endsection
