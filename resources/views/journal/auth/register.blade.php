<x-layouts.public :journal="$journal" title="Register | {{ $journal->name }}">
    <div class="max-w-3xl mx-auto" x-data="registerForm()">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 lg:p-8">

                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-slate-900">Create Account</h1>
                    <p class="text-slate-600 mt-2">Join <span class="font-semibold">{{ $journal->name }}</span> to submit
                        articles and manage your publications.</p>
                </div>

                <!-- Alert Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-lg">
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

                <form action="{{ route('journal.register.store', $journal->slug) }}" method="POST">
                    @csrf

                    <!-- Smart Enroll Notice -->
                    <div class="mb-8 p-4 bg-blue-50 border border-blue-100 rounded-lg flex gap-3 text-sm text-blue-800">
                        <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                        <p>
                            Already have an IAMJOS account with another journal?
                            <span class="font-semibold">Simply enter your existing email and password below</span> to
                            instantly link your account to {{ $journal->abbreviation ?? 'this journal' }}.
                        </p>
                    </div>

                    <!-- Section 1: Profile Information -->
                    <div class="mb-8">
                        <h2
                            class="text-lg font-semibold text-slate-900 border-b border-slate-100 pb-2 mb-5 flex items-center gap-2">
                            <i class="fas fa-user-circle text-slate-400"></i>
                            Profile Information
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- First Name -->
                            <div>
                                <label for="given_name" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="given_name" name="given_name" value="{{ old('given_name') }}"
                                    placeholder="John"
                                    class="block w-full px-4 py-2.5 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="family_name" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="family_name" name="family_name"
                                    value="{{ old('family_name') }}" placeholder="Doe"
                                    class="block w-full px-4 py-2.5 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                            </div>

                            <!-- Affiliation (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="affiliation" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Affiliation / Institution <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-university text-slate-400 text-sm"></i>
                                    </div>
                                    <input type="text" id="affiliation" name="affiliation"
                                        value="{{ old('affiliation') }}" placeholder="University of Indonesia"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        required>
                                </div>
                            </div>

                            <!-- Country (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="country" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-globe text-slate-400 text-sm"></i>
                                    </div>
                                    <select id="country" name="country"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none bg-white"
                                        required>
                                        <option value="">Select your country</option>
                                        @foreach (config('countries', []) as $code => $name)
                                            <option value="{{ $code }}"
                                                {{ old('country') == $code ? 'selected' : '' }}>{{ $name }}
                                            </option>
                                        @endforeach
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
                                            <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>
                                                United States</option>
                                            <option value="OTHER" {{ old('country') == 'OTHER' ? 'selected' : '' }}>
                                                Other</option>
                                        @endif
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone Number (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Phone Number (WhatsApp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-slate-400 text-sm"></i>
                                    </div>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                        placeholder="e.g. 6281234567890"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        required>
                                </div>
                                <p class="mt-1 text-xs text-slate-500 flex items-center gap-1">
                                    <i class="fab fa-whatsapp text-green-500"></i>
                                    Connected to WhatsApp for notifications.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Account Credentials -->
                    <div class="mb-8">
                        <h2
                            class="text-lg font-semibold text-slate-900 border-b border-slate-100 pb-2 mb-5 flex items-center gap-2">
                            <i class="fas fa-lock text-slate-400"></i>
                            Account Credentials
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-slate-400 text-sm"></i>
                                    </div>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        placeholder="john@example.com"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        required>
                                </div>
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Username <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-at text-slate-400 text-sm"></i>
                                    </div>
                                    <input type="text" id="username" name="username"
                                        value="{{ old('username') }}" placeholder="johndoe"
                                        class="block w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        required>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">Alphanumeric only (letters, numbers,
                                    underscores).</p>
                            </div>

                            <!-- Password -->
                            <div x-data="{ show: false }">
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-key text-slate-400 text-sm"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" id="password" name="password"
                                        placeholder="••••••••"
                                        class="block w-full pl-10 pr-12 py-2.5 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        required minlength="8">
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">Min. 8 characters.</p>
                            </div>

                            <!-- Confirm Password -->
                            <div x-data="{ show: false }">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-key text-slate-400 text-sm"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" id="password_confirmation"
                                        name="password_confirmation" placeholder="••••••••"
                                        class="block w-full pl-10 pr-12 py-2.5 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        required>
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Consent & Roles -->
                    <div class="mb-8">
                        <h2
                            class="text-lg font-semibold text-slate-900 border-b border-slate-100 pb-2 mb-5 flex items-center gap-2">
                            <i class="fas fa-check-square text-slate-400"></i>
                            Consent & Roles
                        </h2>

                        <div class="space-y-4">
                            <!-- Privacy Policy Consent -->
                            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                                <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1"
                                    class="mt-1 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    required {{ old('privacy_consent') ? 'checked' : '' }}>
                                <label for="privacy_consent" class="text-sm text-slate-700 cursor-pointer">
                                    I agree to the <a href="#" class="text-blue-600 hover:underline">privacy
                                        statement</a>.
                                </label>
                            </div>

                            <!-- Notification Preferences -->
                            <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                                <input type="checkbox" id="email_notifications" name="email_notifications"
                                    value="1"
                                    class="mt-1 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    {{ old('email_notifications', true) ? 'checked' : '' }}>
                                <label for="email_notifications" class="text-sm text-slate-700 cursor-pointer">
                                    Notify me about new publications and announcements.
                                </label>
                            </div>

                            <!-- Reviewer Role Request -->
                            <div class="flex items-start gap-3 p-4 bg-purple-50 rounded-lg border border-purple-100">
                                <input type="checkbox" id="reviewer_interest" name="reviewer_interest"
                                    value="1"
                                    class="mt-1 w-4 h-4 rounded border-purple-300 text-purple-600 focus:ring-purple-500"
                                    {{ old('reviewer_interest') ? 'checked' : '' }}>
                                <div>
                                    <label for="reviewer_interest"
                                        class="text-sm font-medium text-purple-900 cursor-pointer">
                                        Yes, I would like to safeguard the quality of this journal.
                                    </label>
                                    <p class="text-xs text-purple-700 mt-1">Register me as a Reviewer for
                                        {{ $journal->name }}.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-slate-200">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            Register
                        </button>
                    </div>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-slate-600">
                            Already have an account?
                            <a href="{{ route('journal.login', $journal->slug) }}"
                                class="font-semibold text-blue-600 hover:text-blue-500">Sign in</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function registerForm() {
            return {
                // Form interactions if needed
            }
        }
    </script>
</x-layouts.public>
