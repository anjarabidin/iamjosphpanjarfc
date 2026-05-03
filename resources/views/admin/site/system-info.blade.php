@extends('layouts.admin')

@section('title', 'System Information')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">System & Maintenance</h1>
        <p class="mt-1 text-gray-500">View server status and perform maintenance tasks.</p>
    </div>

    <div class="mb-8">
        <!-- Administrative Tools -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Administrative Tools</h2>
                    <p class="text-sm text-gray-500">Maintenance and system actions</p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Malware Guard -->
                <div class="bg-gray-50 rounded-xl p-5 border border-transparent hover:border-indigo-100 transition-all">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate">Malware Guard</h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">Check system integrity and scan for
                                suspicious files.</p>
                            <a href="{{ route('admin.malware.index') }}"
                                class="inline-flex items-center gap-2 mt-4 px-3 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition-all">
                                Open Scanner
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Expire Sessions -->
                <div class="bg-gray-50 rounded-xl p-5 border border-transparent hover:border-orange-100 transition-all">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 text-orange-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate">Expire Sessions</h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">Log out all users immediately. They will need
                                to sign in again.</p>
                            <form action="{{ route('admin.site.expire-sessions') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('This will log out ALL users immediately. Are you sure?')"
                                    class="w-full sm:w-auto px-3 py-2 bg-orange-600 text-white text-xs font-semibold rounded-lg hover:bg-orange-700 transition-all">
                                    Expire Sessions
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Clear Data Cache -->
                <div class="bg-gray-50 rounded-xl p-5 border border-transparent hover:border-red-100 transition-all">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate">Clear Data Cache</h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">Clear database query cache and application
                                cache.</p>
                            <form action="{{ route('admin.site.clear-cache') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit"
                                    class="w-full sm:w-auto px-3 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition-all">
                                    Clear Cache
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Clear Template Cache -->
                <div class="bg-gray-50 rounded-xl p-5 border border-transparent hover:border-amber-100 transition-all">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate">Clear Templates</h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">Force re-compilation of all Blade templates.
                            </p>
                            <form action="{{ route('admin.site.clear-templates') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit"
                                    class="w-full sm:w-auto px-3 py-2 bg-amber-600 text-white text-xs font-semibold rounded-lg hover:bg-amber-700 transition-all">
                                    Clear Templates
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Clear Scheduled Task Logs -->
                <div class="bg-gray-50 rounded-xl p-5 border border-transparent hover:border-slate-300 transition-all">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center flex-shrink-0 text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate">Clear Task Logs</h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">Remove old execution logs from scheduled
                                tasks.</p>
                            <form action="{{ route('admin.site.clear-logs') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit"
                                    class="w-full sm:w-auto px-3 py-2 bg-slate-600 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition-all">
                                    Clear Logs
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
