@php
    $currentPage = $currentPage ?? '';
@endphp

<div class="flex h-full flex-col bg-gradient-to-b from-slate-800 to-slate-900">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center px-6 border-b border-slate-700/50">
        <a href="{{ route('admin.site.index') }}" class="flex items-center">
            <div
                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
            <span class="ml-3 text-lg font-bold text-white">{{ config('app.name') }}</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-1 flex-col overflow-y-auto px-4 py-6">
        <ul role="list" class="flex flex-1 flex-col gap-y-2">
            <!-- Site Administration -->
            <li>
                <a href="{{ route('admin.site.index') }}"
                    class="group flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150
                          {{ $currentPage === 'site-admin' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ $currentPage === 'site-admin' ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                    </svg>
                    Site Administration
                </a>
            </li>

            <!-- Hosted Journals -->
            <li>
                <a href="{{ route('admin.journals.index') }}"
                    class="group flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150
                          {{ $currentPage === 'journals' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ $currentPage === 'journals' ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Hosted Journals
                </a>
            </li>

            <!-- Separator -->
            <li class="my-4">
                <div class="border-t border-slate-700/50"></div>
            </li>

            <!-- System Section Title -->
            <li class="px-3 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">System</span>
            </li>

            <!-- Users -->
            <li>
                <a href="#"
                    class="group flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150
                          {{ $currentPage === 'users' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ $currentPage === 'users' ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    All Users
                </a>
            </li>

            <!-- Roles & Permissions -->
            <li>
                <a href="#"
                    class="group flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150
                          {{ $currentPage === 'roles' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ $currentPage === 'roles' ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    Roles & Permissions
                </a>
            </li>

            <!-- Spacer -->
            <li class="mt-auto"></li>

            <!-- Separator -->
            <li class="my-4">
                <div class="border-t border-slate-700/50"></div>
            </li>

            <!-- Back to Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}"
                    class="group flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-150">
                    <svg class="h-5 w-5 shrink-0 text-slate-400 group-hover:text-white" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Dashboard
                </a>
            </li>

            <!-- Support -->
            <li>
                <a href="#"
                    class="group flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-150">
                    <svg class="h-5 w-5 shrink-0 text-slate-400 group-hover:text-white" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                    Support
                </a>
            </li>
        </ul>
    </nav>

    <!-- User info -->
    <div class="shrink-0 border-t border-slate-700/50 p-4">
        <div class="flex items-center gap-x-3">
            <div
                class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                <span class="text-white font-semibold">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-slate-400 truncate">Super Admin</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
