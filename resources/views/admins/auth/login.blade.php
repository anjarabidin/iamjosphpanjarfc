<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ $journal->name ?? config('app.name', 'IAMJOS') }}</title>
    <meta name="description"
        content="{{ $journal ? $journal->name . ' - Login' : 'Indonesian Academic Journal System - Login' }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Tailwind CSS via CDN (for standalone auth pages) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (($journal ?? false) && ($journal->is_recaptcha_enabled ?? false) && ($siteSetting->recaptcha_site_key ?? false))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex" x-data="{ showPassword: false }">
        <!-- Left Side - Brand Panel (Dynamic based on Journal Context) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden"
            @if ($journal && ($branding['cover_url'] ?? null)) style="background-image: url('{{ $branding['cover_url'] }}'); background-size: cover; background-position: center;" @endif>
            <!-- Background Gradient Overlay -->
            <div
                class="absolute inset-0 {{ $journal ? 'bg-gradient-to-br from-gray-900/90 via-gray-800/85 to-gray-900/90' : 'bg-gradient-to-br from-indigo-900 via-indigo-800 to-indigo-900' }}">
            </div>

            <!-- Abstract Pattern Overlay (only for portal context) -->
            @unless ($journal)
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" />
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#grid)" />
                    </svg>
                </div>
            @endunless

            <!-- Floating Circles (animated background elements) -->
            <div
                class="absolute top-20 left-20 w-72 h-72 {{ $journal ? 'bg-white' : 'bg-indigo-500' }} rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse">
            </div>
            <div class="absolute bottom-20 right-20 w-96 h-96 {{ $journal ? 'bg-white' : 'bg-purple-500' }} rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"
                style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/3 w-64 h-64 {{ $journal ? 'bg-white' : 'bg-blue-500' }} rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"
                style="animation-delay: 2s;"></div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-20 w-full">
                <!-- Logo -->
                <div class="mb-12">
                    <div class="flex items-center gap-3">
                        @if ($journal && ($branding['logo_url'] ?? null))
                            <img src="{{ $branding['logo_url'] }}" alt="{{ $journal->name }}"
                                class="h-12 w-auto object-contain">
                        @else
                            <div
                                class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-book-open text-2xl text-white"></i>
                            </div>
                        @endif
                        <span class="text-2xl font-bold text-white tracking-tight">
                            {{ $branding['acronym'] ?? config('app.name', 'IAMJOS') }}
                        </span>
                    </div>
                </div>

                <!-- Heading -->
                <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight mb-6">
                    @if ($journal)
                        {{ $branding['headline'] }}
                    @else
                        Advance Your<br>
                        <span class="text-indigo-300">Academic Research</span>
                    @endif
                </h1>

                <!-- Tagline -->
                <p class="text-lg {{ $journal ? 'text-gray-200' : 'text-indigo-200' }} leading-relaxed max-w-md mb-12">
                    {{ Str::limit($branding['tagline'] ?? $branding['description'], 160) }}
                </p>

                <!-- Features (show different content based on context) -->
                @if ($journal)
                    <!-- Journal-specific info -->
                    <div class="space-y-4">
                        @if ($journal->issn_print || $journal->issn_online)
                            <div class="flex items-center gap-4 text-gray-100">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-barcode text-sm"></i>
                                </div>
                                <span class="text-sm">
                                    @if ($journal->issn_print)
                                        ISSN Print: {{ $journal->issn_print }}
                                    @endif
                                    @if ($journal->issn_print && $journal->issn_online)
                                        |
                                    @endif
                                    @if ($journal->issn_online)
                                        ISSN Online: {{ $journal->issn_online }}
                                    @endif
                                </span>
                            </div>
                        @endif
                        <div class="flex items-center gap-4 text-gray-100">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-paper-plane text-sm"></i>
                            </div>
                            <span class="text-sm">Submit Your Manuscript</span>
                        </div>
                        <div class="flex items-center gap-4 text-gray-100">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-sm"></i>
                            </div>
                            <span class="text-sm">Track Submission Progress</span>
                        </div>
                    </div>
                @else
                    <!-- Portal features -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 text-indigo-100">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-paper-plane text-sm"></i>
                            </div>
                            <span class="text-sm">Streamlined Submission Process</span>
                        </div>
                        <div class="flex items-center gap-4 text-indigo-100">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <span class="text-sm">Collaborative Peer Review</span>
                        </div>
                        <div class="flex items-center gap-4 text-indigo-100">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                            <span class="text-sm">Editorial Workflow Management</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-10 text-center">
                    <div class="inline-flex items-center gap-3">
                        @if ($journal && ($branding['logo_url'] ?? null))
                            <img src="{{ $branding['logo_url'] }}" alt="{{ $journal->name }}"
                                class="h-10 w-auto object-contain">
                        @else
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book-open text-xl text-white"></i>
                            </div>
                        @endif
                        <span class="text-xl font-bold text-gray-900">
                            {{ $branding['acronym'] ?? config('app.name', 'IAMJOS') }}
                        </span>
                    </div>
                </div>

                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
                    <p class="text-gray-500">
                        Sign in to {{ $branding['acronym'] ?? config('app.name', 'IAMJOS') }}
                    </p>
                </div>

                <!-- Alert Messages -->
                @if (session('warning'))
                    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                        <p class="text-sm text-amber-700">{{ session('warning') }}</p>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-start gap-3">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                        <i class="fas fa-times-circle text-red-500 mt-0.5"></i>
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Login Form (Dynamic Action based on Context) -->
                <form action="{{ $journal ? route('journal.authenticate', $journal->slug) : route('authenticate') }}"
                    method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email or Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" id="email" name="email" value="{{ old('email') }}"
                                placeholder="Enter your email or username"
                                class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                required autofocus>
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            {{-- <a href="{{ route('forgot-password') }}"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                                Forgot password?
                            </a> --}}
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                placeholder="Enter your password"
                                class="block w-full pl-10 pr-12 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                required>
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600">
                            Remember for 30 days
                        </label>
                    </div>

                    <!-- reCAPTCHA Widget -->
                    @if (($journal ?? false) && ($journal->is_recaptcha_enabled ?? false) && ($siteSetting->recaptcha_site_key ?? false))
                        <div class="g-recaptcha" data-sitekey="{{ $siteSetting->recaptcha_site_key }}"></div>
                        @error('g-recaptcha-response')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @endif

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign in
                    </button>
                </form>

                <!-- Register Link -->
                <p class="mt-8 text-center text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ $journal ? route('journal.register', $journal->slug) : route('register') }}"
                        class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                        Create account
                    </a>
                </p>

                <!-- Back to Journal/Portal Link -->
                @if ($journal)
                    <p class="mt-4 text-center text-sm text-gray-500">
                        <a href="{{ route('journal.public.home', $journal->slug) }}"
                            class="text-gray-600 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Back to
                            {{ $journal->abbreviation ?? $journal->name }}
                        </a>
                    </p>
                @else
                    <p class="mt-4 text-center text-sm text-gray-500">
                        <a href="{{ route('portal.home') }}"
                            class="text-gray-600 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Home
                        </a>
                    </p>
                @endif

                <!-- Footer -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <p class="text-center text-xs text-gray-400">
                        © {{ date('Y') }} {{ $journal ? $journal->name : config('app.name', 'IAMJOS') }}.
                        @unless ($journal)
                            Indonesian Academic Journal System.
                        @endunless
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
