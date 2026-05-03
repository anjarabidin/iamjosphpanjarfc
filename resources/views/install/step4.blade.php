@extends('install.layout', ['step' => 4])

@section('content')

<div class="space-y-8">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Final System Setup</h2>
        <p class="text-gray-500 text-sm mt-1">Configure your site URL and create the initial Super Admin account.</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('install.execute') }}" class="space-y-8" onsubmit="document.getElementById('installBtn').disabled = true; document.getElementById('installBtn').innerHTML = 'Installing... Please wait...';">
        @csrf

        <!-- Environment & Site Settings -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Environment & Site Settings</h3>
            
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                        <input type="text" name="app_name" value="{{ old('app_name', 'IAMJOS PHP') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="My Journal Website" required>
                        @error('app_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Application URL</label>
                        <input type="url" name="app_url" value="{{ old('app_url', url('/')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="https://journal.example.com" required>
                        @error('app_url') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Environment</label>
                        <select name="app_env" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" required>
                            <option value="production" {{ old('app_env', 'production') == 'production' ? 'selected' : '' }}>Production (Live)</option>
                            <option value="local" {{ old('app_env') == 'local' ? 'selected' : '' }}>Local (Development)</option>
                        </select>
                        @error('app_env') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Debug Mode</label>
                        <select name="app_debug" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" required>
                            <option value="false" {{ old('app_debug', 'false') == 'false' ? 'selected' : '' }}>False (Recommended)</option>
                            <option value="true" {{ old('app_debug') == 'true' ? 'selected' : '' }}>True (Show Errors)</option>
                        </select>
                        @error('app_debug') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                        <select name="app_timezone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" required>
                            <option value="UTC" {{ old('app_timezone', 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="Asia/Jakarta" {{ old('app_timezone') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" {{ old('app_timezone') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" {{ old('app_timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                        </select>
                        @error('app_timezone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Super Admin Setup -->
        <div>
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Super Admin Account</h3>
            
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="John Doe" required>
                        @error('admin_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="admin@example.com" required>
                        @error('admin_email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="admin_password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" required minlength="8">
                        @error('admin_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="admin_password_confirmation" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" required minlength="8">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-sm p-4 rounded-md flex items-start gap-3">
            <svg class="h-5 w-5 text-blue-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong class="block mb-1">What happens next?</strong>
                Clicking "Install IAMJOS" will update your .env file, run database migrations and seeders, and create your Super Admin account. This process may take a minute.
            </div>
        </div>

        <!-- Actions -->
        <div class="pt-6 border-t border-gray-200 flex justify-between items-center">
            <a href="{{ route('install.step3') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition text-sm font-medium">
                Back
            </a>
            
            <button type="submit" id="installBtn" class="px-6 py-2.5 rounded-md text-sm font-bold transition bg-indigo-600 text-white hover:bg-indigo-700 shadow-md flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Install IAMJOS
            </button>
        </div>
    </form>
</div>

@endsection
