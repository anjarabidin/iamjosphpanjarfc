<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | {{ config('app.name', 'IAMJOS') }}</title>
    <meta name="description" content="Indonesian Academic Journal System - Create Account">
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

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 min-h-screen">
    <!-- Background Pattern -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div
            class="absolute -top-40 -right-40 w-80 h-80 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70">
        </div>
        <div
            class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70">
        </div>
    </div>

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex flex-col justify-center" x-data="registerForm()">
        <div class="w-full max-w-3xl mx-auto">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-3 mb-6">
                    <div
                        class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i class="fas fa-book-open text-xl text-white"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">IAMJOS</span>
                </a>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Create your account</h1>
                <p class="text-gray-500">Join the Indonesian Academic Journal System community</p>
            </div>

            <!-- Registration Card -->
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <!-- Alert Messages -->
                @if ($errors->any())
                    <div class="p-4 bg-red-50 border-b border-red-100">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-red-800">Please correct the following errors:</p>
                                <ul class="mt-1 list-disc list-inside text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="p-6 lg:p-8">
                    @csrf

                    <!-- Section 1: Profile Information -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900">Profile Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- First Name -->
                            <div>
                                <label for="given_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="given_name" name="given_name" value="{{ old('given_name') }}"
                                    placeholder="John"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    required>
                                @error('given_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="family_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="family_name" name="family_name"
                                    value="{{ old('family_name') }}" placeholder="Doe"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    required>
                                @error('family_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Affiliation (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="affiliation" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Affiliation / Institution <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-university text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="text" id="affiliation" name="affiliation"
                                        value="{{ old('affiliation') }}" placeholder="University of Indonesia"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        required>
                                </div>
                                @error('affiliation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Country (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-globe text-gray-400 text-sm"></i>
                                    </div>
                                    <select id="country" name="country"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors appearance-none bg-white"
                                        required>
                                        <option value="">Select your country</option>
                                        @foreach (config('countries', []) as $code => $name)
                                            <option value="{{ $code }}"
                                                {{ old('country') == $code ? 'selected' : '' }}>{{ $name }}
                                            </option>
                                        @endforeach
                                        <!-- Fallback countries if config not available -->
                                        @if (empty(config('countries')))
                                            <option value="ID" {{ old('country') == 'ID' ? 'selected' : '' }}>
                                                Indonesia</option>
                                            <option value="MY" {{ old('country') == 'MY' ? 'selected' : '' }}>
                                                Malaysia</option>
                                            <option value="SG" {{ old('country') == 'SG' ? 'selected' : '' }}>
                                                Singapore</option>
                                            <option value="TH" {{ old('country') == 'TH' ? 'selected' : '' }}>
                                                Thailand</option>
                                            <option value="VN" {{ old('country') == 'VN' ? 'selected' : '' }}>
                                                Vietnam</option>
                                            <option value="PH" {{ old('country') == 'PH' ? 'selected' : '' }}>
                                                Philippines</option>
                                            <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>
                                                Australia</option>
                                            <option value="JP" {{ old('country') == 'JP' ? 'selected' : '' }}>Japan
                                            </option>
                                            <option value="KR" {{ old('country') == 'KR' ? 'selected' : '' }}>South
                                                Korea</option>
                                            <option value="CN" {{ old('country') == 'CN' ? 'selected' : '' }}>China
                                            </option>
                                            <option value="IN" {{ old('country') == 'IN' ? 'selected' : '' }}>
                                                India</option>
                                            <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>
                                                United States</option>
                                            <option value="GB" {{ old('country') == 'GB' ? 'selected' : '' }}>
                                                United Kingdom</option>
                                            <option value="DE" {{ old('country') == 'DE' ? 'selected' : '' }}>
                                                Germany</option>
                                            <option value="FR" {{ old('country') == 'FR' ? 'selected' : '' }}>
                                                France</option>
                                            <option value="NL" {{ old('country') == 'NL' ? 'selected' : '' }}>
                                                Netherlands</option>
                                            <option value="CA" {{ old('country') == 'CA' ? 'selected' : '' }}>
                                                Canada</option>
                                            <option value="BR" {{ old('country') == 'BR' ? 'selected' : '' }}>
                                                Brazil</option>
                                            <option value="OTHER" {{ old('country') == 'OTHER' ? 'selected' : '' }}>
                                                Other</option>
                                        @endif
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                @error('country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone Number (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Phone Number (WhatsApp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                        placeholder="e.g. 6281234567890"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        required>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fab fa-whatsapp text-green-500"></i>
                                    Please ensure this number is connected to WhatsApp.
                                </p>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Account Credentials -->
                    <div class="mb-8 pt-6 border-t border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-key text-emerald-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900">Account Credentials</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        placeholder="john@university.edu"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        required>
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Username <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-at text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="text" id="username" name="username"
                                        value="{{ old('username') }}" placeholder="johndoe"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        required>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Alphanumeric characters only, no spaces</p>
                                @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div x-data="{ show: false }">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400 text-sm"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" id="password" name="password"
                                        placeholder="••••••••"
                                        class="block w-full pl-10 pr-12 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        required minlength="8">
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div x-data="{ show: false }">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400 text-sm"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" id="password_confirmation"
                                        name="password_confirmation" placeholder="••••••••"
                                        class="block w-full pl-10 pr-12 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        required>
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Consent & Roles -->
                    <div class="mb-8 pt-6 border-t border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-amber-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900">Consent & Preferences</h2>
                        </div>

                        <div class="space-y-4">
                            <!-- Privacy Policy Consent -->
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1"
                                    class="mt-1 w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    required {{ old('privacy_consent') ? 'checked' : '' }}>
                                <div>
                                    <label for="privacy_consent"
                                        class="text-sm font-medium text-gray-900 cursor-pointer">
                                        Privacy Policy Agreement <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Yes, I agree to have my data collected and stored according to the
                                        <a href="#"
                                            class="text-indigo-600 hover:text-indigo-500 underline">privacy
                                            statement</a>.
                                    </p>
                                </div>
                            </div>
                            @error('privacy_consent')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Notification Preferences -->
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <input type="checkbox" id="email_notifications" name="email_notifications"
                                    value="1"
                                    class="mt-1 w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    {{ old('email_notifications', true) ? 'checked' : '' }}>
                                <div>
                                    <label for="email_notifications"
                                        class="text-sm font-medium text-gray-900 cursor-pointer">
                                        Email Notifications
                                    </label>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Yes, I would like to be notified of new publications and announcements.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Register with Journals (Multi-Journal Selection) -->
                    <div class="mb-8 pt-6 border-t border-gray-100" x-data="journalSelection()">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-journal-whills text-indigo-600 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Register with Journals</h2>
                                <p class="text-sm text-gray-500">Select the journals you want to participate in <span
                                        class="text-red-500">*</span></p>
                            </div>
                        </div>

                        @error('journals')
                            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-600 flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            </div>
                        @enderror

                        @if (isset($journals) && $journals->count() > 0)
                            <!-- Journal Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($journals as $journal)
                                    <div class="relative rounded-xl border-2 transition-all duration-200 overflow-hidden"
                                        :class="selectedJournals.includes('{{ $journal->id }}') ?
                                            'border-indigo-300 bg-indigo-50/50' :
                                            'border-gray-200 bg-white hover:border-gray-300'">
                                        <!-- Journal Card Content -->
                                        <div class="p-4">
                                            <!-- Header -->
                                            <div class="flex items-start gap-3 mb-4">
                                                <!-- Journal Icon/Logo -->
                                                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors"
                                                    :class="selectedJournals.includes('{{ $journal->id }}') ?
                                                        'bg-indigo-200 text-indigo-700' : 'bg-gray-100 text-gray-500'">
                                                    @if ($journal->logo_path)
                                                        <img src="{{ Storage::url($journal->logo_path) }}"
                                                            alt="{{ $journal->name }}"
                                                            class="w-10 h-10 object-contain rounded">
                                                    @else
                                                        <i class="fas fa-book text-lg"></i>
                                                    @endif
                                                </div>

                                                <!-- Journal Info -->
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="font-semibold text-gray-900 text-sm leading-tight">
                                                        {{ $journal->name }}
                                                    </h3>
                                                    @if ($journal->abbreviation)
                                                        <span
                                                            class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                                                            {{ $journal->abbreviation }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Main Checkbox: Register as Author/Reader -->
                                            <label
                                                class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-colors"
                                                :class="selectedJournals.includes('{{ $journal->id }}') ? 'bg-indigo-100' :
                                                    'bg-gray-50 hover:bg-gray-100'">
                                                <input type="checkbox" name="journals[]" value="{{ $journal->id }}"
                                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    @change="toggleJournal('{{ $journal->id }}')"
                                                    :checked="selectedJournals.includes('{{ $journal->id }}')"
                                                    {{ in_array($journal->id, old('journals', [])) ? 'checked' : '' }}>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-900">Register as
                                                        Author/Reader</span>
                                                    <p class="text-xs text-gray-500 mt-0.5">Submit articles and access
                                                        publications</p>
                                                </div>
                                            </label>

                                            <!-- Reviewer Checkbox (Conditional) -->
                                            <div class="mt-2 transition-all duration-200"
                                                :class="selectedJournals.includes('{{ $journal->id }}') ? 'opacity-100' :
                                                    'opacity-40 pointer-events-none'">
                                                <label
                                                    class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-colors"
                                                    :class="reviewerForJournal['{{ $journal->id }}'] ? 'bg-purple-100' :
                                                        'bg-gray-50 hover:bg-gray-100'">
                                                    <input type="checkbox"
                                                        name="reviewer_for_journal[{{ $journal->id }}]"
                                                        value="1"
                                                        class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                                        @change="toggleReviewer('{{ $journal->id }}')"
                                                        :checked="reviewerForJournal['{{ $journal->id }}']"
                                                        :disabled="!selectedJournals.includes('{{ $journal->id }}')"
                                                        {{ old('reviewer_for_journal.' . $journal->id) ? 'checked' : '' }}>
                                                    <div>
                                                        <span
                                                            class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                                            <i class="fas fa-user-check text-purple-600 text-xs"></i>
                                                            Become a Reviewer
                                                        </span>
                                                        <p class="text-xs text-gray-500 mt-0.5">Evaluate manuscript
                                                            submissions</p>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- Reviewer Badge (when selected) -->
                                            <div x-show="reviewerForJournal['{{ $journal->id }}'] && selectedJournals.includes('{{ $journal->id }}')"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                class="mt-3 inline-flex items-center gap-2 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                                                <i class="fas fa-check-circle"></i>
                                                Reviewer role requested
                                            </div>
                                        </div>

                                        <!-- Selected Indicator -->
                                        <div x-show="selectedJournals.includes('{{ $journal->id }}')"
                                            class="absolute top-3 right-3">
                                            <span
                                                class="flex items-center justify-center w-6 h-6 bg-indigo-600 rounded-full">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Selection Summary -->
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200"
                                x-show="selectedJournals.length > 0">
                                <p class="text-sm text-gray-700">
                                    <i class="fas fa-info-circle text-indigo-500 mr-1"></i>
                                    You will be registered with <span class="font-semibold"
                                        x-text="selectedJournals.length"></span> journal(s).
                                </p>
                            </div>
                        @else
                            <!-- No Journals Available -->
                            <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <i class="fas fa-journal-whills text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">No journals are currently available for registration.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-gray-100">
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            Create Account
                        </button>
                    </div>
                </form>

                <!-- Footer Links -->
                <div class="px-6 lg:px-8 py-5 bg-gray-50 border-t border-gray-100">
                    <p class="text-center text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} {{ config('app.name', 'IAMJOS') }}. Indonesian Academic Journal System.
                </p>
            </div>
        </div>
    </div>

    <script>
        function registerForm() {
            return {
                // Add any form-level state here if needed
            }
        }

        function journalSelection() {
            // Initialize from old input if available (for validation errors)
            const oldJournals = @json(old('journals', []));
            const oldReviewerFor = @json(old('reviewer_for_journal', []));

            return {
                selectedJournals: oldJournals,
                reviewerForJournal: oldReviewerFor,

                toggleJournal(journalId) {
                    const index = this.selectedJournals.indexOf(journalId);
                    if (index === -1) {
                        this.selectedJournals.push(journalId);
                    } else {
                        this.selectedJournals.splice(index, 1);
                        // Also remove reviewer interest when deselecting journal
                        delete this.reviewerForJournal[journalId];
                    }
                },

                toggleReviewer(journalId) {
                    if (this.reviewerForJournal[journalId]) {
                        delete this.reviewerForJournal[journalId];
                    } else {
                        this.reviewerForJournal[journalId] = true;
                    }
                },

                isJournalSelected(journalId) {
                    return this.selectedJournals.includes(journalId);
                },

                isReviewerFor(journalId) {
                    return !!this.reviewerForJournal[journalId];
                }
            }
        }
    </script>
</body>

</html>
