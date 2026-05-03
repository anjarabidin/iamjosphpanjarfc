{{-- Login Block - User login/profile widget --}}
@props(['journal', 'settings' => [], 'block' => null])

@php
$primaryColor = $settings['primary_color'] ?? '#4F46E5';
@endphp

@auth
<div class="flex items-center gap-3">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-600 to-gray-800 flex items-center justify-center text-white font-semibold">
        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
    </div>
</div>
<div class="mt-4 space-y-2">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
        <i class="fa-solid fa-gauge w-4 text-gray-400"></i>
        Dashboard
    </a>
    <a href="{{ route('journal.submissions.index', $journal->slug) }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
        <i class="fa-solid fa-file-alt w-4 text-gray-400"></i>
        My Submissions
    </a>
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="flex items-center gap-2 text-sm text-red-600 hover:text-red-800">
            <i class="fa-solid fa-sign-out-alt w-4"></i>
            Logout
        </button>
    </form>
</div>
@else
<div class="space-y-3">
    <a href="{{ route('login') }}"
        class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white rounded-lg"
        style="background: {{ $primaryColor }};">
        <i class="fa-solid fa-sign-in-alt mr-2"></i>
        Login
    </a>
    <a href="{{ route('register') }}"
        class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
        <i class="fa-solid fa-user-plus mr-2"></i>
        Register
    </a>
</div>
@endauth