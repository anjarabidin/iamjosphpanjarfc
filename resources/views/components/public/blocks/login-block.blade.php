@props(['journal', 'block'])
@php
    $primaryColor = $journal->getWebsiteSettings()['primary_color'] ?? '#0369a1';
@endphp

<div class="text-sm text-slate-600">
    @auth
        <p class="mb-3">You are logged in as <strong>{{ auth()->user()->name }}</strong>.</p>
        <a href="{{ route('journal.submissions.create', $journal->slug) }}"
             class="block w-full text-center text-white font-medium py-2 px-4 rounded transition shadow-sm hover:brightness-110" style="background: {{ $primaryColor }};">
            Submission
        </a>
        <form method="POST" action="{{ route('journal.logout', $journal->slug) }}" class="mt-2">
            @csrf
            <button type="submit" class="block w-full text-center text-slate-600 bg-slate-100 font-medium py-2 px-4 rounded transition shadow-sm hover:bg-slate-200">
                Logout
            </button>
        </form>
    @else
        <form action="{{ route('journal.login', $journal->slug) }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label for="block-login-email" class="sr-only">Email</label>
                <input type="email" name="email" id="block-login-email" required placeholder="Email" class="w-full border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="block-login-password" class="sr-only">Password</label>
                <input type="password" name="password" id="block-login-password" required placeholder="Password" class="w-full border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="block-login-remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="block-login-remember" class="ml-2 block text-xs text-gray-900">
                        Remember me
                    </label>
                </div>
            </div>
            <button type="submit" class="block w-full text-center text-white font-medium py-2 px-4 rounded transition shadow-sm hover:brightness-110" style="background: {{ $primaryColor }};">
                Login
            </button>
        </form>
        <div class="mt-3 text-xs text-center flex items-center justify-center">
            <a href="{{ route('journal.register', $journal->slug) }}" class="text-indigo-600 hover:underline">Register</a>
            <span class="mx-2 text-slate-300">|</span>
            <a href="{{ route('forgot-password') }}" class="text-indigo-600 hover:underline">Forgot Password</a>
        </div>
    @endauth
</div>
