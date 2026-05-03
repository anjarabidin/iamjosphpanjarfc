<x-app-layout>
    @push('styles')
        <style>
            /* CKEditor 4 Custom Styling */
            .cke_chrome {
                border: 1px solid #d1d5db !important;
                border-radius: 0.5rem !important;
            }

            .cke_top {
                background: #f9fafb !important;
                border-bottom: 1px solid #e5e7eb !important;
                border-radius: 0.5rem 0.5rem 0 0 !important;
            }

            .cke_bottom {
                background: #f9fafb !important;
                border-top: 1px solid #e5e7eb !important;
            }
        </style>
    @endpush
    <x-slot name="title">Profile Settings</x-slot>

    <div class="min-h-screen bg-gray-50 -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
                <p class="mt-2 text-gray-600">Manage your account information and preferences</p>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-medium text-red-800">Please fix the following errors:</p>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Avatar Section (Standalone) -->
            <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6" x-data="{ previewUrl: null }">
                        <!-- Avatar Display -->
                        <div class="relative">
                            @if ($user->avatar_url)
                                <img x-show="!previewUrl" src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-primary-100 shadow-lg">
                            @else
                                <div x-show="!previewUrl"
                                    class="w-32 h-32 rounded-full bg-primary-600 flex items-center justify-center border-4 border-primary-100 shadow-lg">
                                    <span class="text-white text-4xl font-bold">{{ $user->initials }}</span>
                                </div>
                            @endif
                            <!-- Preview -->
                            <img x-show="previewUrl" x-cloak :src="previewUrl"
                                class="w-32 h-32 rounded-full object-cover border-4 border-primary-100 shadow-lg">
                        </div>

                        <!-- Avatar Actions -->
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 mb-4">{{ $user->email }}</p>
                            <p class="text-xs text-gray-600 mb-4">
                                Upload a new avatar. Max file size: 2MB. Allowed formats: JPG, PNG, WebP.
                            </p>
                            <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                                <form action="{{ route('journal.profile.avatar', $journal->slug) }}" method="POST"
                                    enctype="multipart/form-data" class="flex items-center gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <input type="file" name="avatar" id="avatar-input"
                                        accept="image/jpeg,image/png,image/jpg,image/webp"
                                        @change="if ($event.target.files[0]) { 
                                            if ($event.target.files[0].size > 2 * 1024 * 1024) { 
                                                alert('File size must be less than 2MB'); 
                                                $event.target.value = ''; 
                                            } else { 
                                                previewUrl = URL.createObjectURL($event.target.files[0]); 
                                            }
                                        }"
                                        class="hidden">
                                    <label for="avatar-input"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-colors">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Choose Image
                                    </label>
                                    <button x-show="previewUrl" x-cloak type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        Upload
                                    </button>
                                </form>

                                @if ($user->avatar)
                                    <form action="{{ route('journal.profile.avatar.delete', $journal->slug) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium transition-colors"
                                            onclick="return confirm('Are you sure you want to remove your avatar?')">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Remove
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Interface -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ activeTab: 'identity' }">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex overflow-x-auto -mb-px" aria-label="Tabs">
                        <button @click="activeTab = 'identity'" type="button"
                            :class="activeTab === 'identity' ? 'border-primary-600 text-primary-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            <i class="fa-solid fa-user mr-2"></i>
                            Identity
                        </button>
                        <button @click="activeTab = 'contact'" type="button"
                            :class="activeTab === 'contact' ? 'border-primary-600 text-primary-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            <i class="fa-solid fa-address-book mr-2"></i>
                            Contact
                        </button>
                        <button @click="activeTab = 'public'" type="button"
                            :class="activeTab === 'public' ? 'border-primary-600 text-primary-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            <i class="fa-solid fa-globe mr-2"></i>
                            Public
                        </button>
                        <button @click="activeTab = 'password'" type="button"
                            :class="activeTab === 'password' ? 'border-primary-600 text-primary-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            <i class="fa-solid fa-lock mr-2"></i>
                            Password
                        </button>
                        {{-- @if ($journal && $availableRoles->isNotEmpty()) --}}
                        <button @click="activeTab = 'roles'" type="button"
                            :class="activeTab === 'roles' ? 'border-primary-600 text-primary-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            <i class="fa-solid fa-user-tag mr-2"></i>
                            Roles
                        </button>
                        {{-- @endif --}}
                    </nav>
                </div>

                <!-- Tab Content -->
                <form action="{{ route('journal.profile.update', $journal->slug) }}" method="POST" class="p-6">
                    @csrf
                    @method('PATCH')

                    <!-- TAB 1: Identity -->
                    <div x-show="activeTab === 'identity'" x-cloak>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Identity Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Public Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Public Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $user->name) }}" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Your public display name (how you want to be
                                    known)</p>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Given Name -->
                            <div>
                                <label for="given_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Given Name
                                </label>
                                <input type="text" name="given_name" id="given_name"
                                    value="{{ old('given_name', $user->given_name) }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-xs text-gray-500">Your first/given name (optional)</p>
                            </div>

                            <!-- Family Name -->
                            <div>
                                <label for="family_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Family Name
                                </label>
                                <input type="text" name="family_name" id="family_name"
                                    value="{{ old('family_name', $user->family_name) }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-xs text-gray-500">Your last/family name (optional)</p>
                            </div>

                            <!-- Affiliation -->
                            <div class="md:col-span-2">
                                <label for="affiliation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Affiliation / Institution
                                </label>
                                <textarea name="affiliation" id="affiliation" rows="3"
                                    placeholder="e.g., Department of Computer Science, University of Indonesia"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('affiliation', $user->affiliation) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Your current institution or organization</p>
                            </div>

                            <!-- Country -->
                            <div class="md:col-span-2">
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                    Country
                                </label>
                                <select name="country" id="country"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Select a country</option>
                                    @php
                                        $countries = [
                                            'Indonesia',
                                            'Malaysia',
                                            'Singapore',
                                            'Thailand',
                                            'Philippines',
                                            'United States',
                                            'United Kingdom',
                                            'Australia',
                                            'Canada',
                                            'Germany',
                                            'France',
                                            'Japan',
                                            'South Korea',
                                            'China',
                                            'India',
                                            'Netherlands',
                                            'Switzerland',
                                            'Sweden',
                                            'Norway',
                                            'Denmark',
                                            'Finland',
                                            'Belgium',
                                            'Austria',
                                            'New Zealand',
                                            'Brazil',
                                            'Mexico',
                                            'Argentina',
                                            'Chile',
                                        ];
                                    @endphp
                                    @foreach ($countries as $country)
                                        <option value="{{ $country }}"
                                            {{ old('country', $user->country) === $country ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Contact -->
                    <div x-show="activeTab === 'contact'" x-cloak>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Contact Information</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Email (Read-only) -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email', $user->email) }}" required disabled
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                <p class="mt-1 text-xs text-gray-500">Email cannot be changed directly. Contact admin
                                    if needed.</p>
                            </div>

                            <!-- Phone Number with WhatsApp -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone / WhatsApp Number
                                </label>
                                <div class="relative">
                                    <input type="tel" name="phone" id="phone"
                                        value="{{ old('phone', $user->phone) }}" placeholder="628123456789"
                                        class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div class="mt-2 flex items-start">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fa-brands fa-whatsapp mr-1"></i>
                                        WhatsApp Active
                                    </span>
                                    <p class="ml-2 text-xs text-gray-500">Ensure this number is active on WhatsApp for
                                        notifications. Format: 628...</p>
                                </div>
                            </div>

                            <!-- Mailing Address -->
                            <div>
                                <label for="mailing_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mailing Address
                                </label>
                                <textarea name="mailing_address" id="mailing_address" rows="4"
                                    placeholder="Enter your complete mailing address..."
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('mailing_address', $user->mailing_address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Public Profile -->
                    <div x-show="activeTab === 'public'" x-cloak>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Public Profile</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Bio Statement with CKEditor 4 -->
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Biography / Bio Statement
                                </label>
                                <textarea name="bio" id="bio" rows="6"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('bio', $user->bio) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Maximum 5000 characters. This will be displayed
                                    on your public profile.</p>
                            </div>

                            <!-- Homepage URL -->
                            <div>
                                <label for="homepage" class="block text-sm font-medium text-gray-700 mb-2">
                                    Homepage URL
                                </label>
                                <div class="relative">
                                    <input type="url" name="homepage" id="homepage"
                                        value="{{ old('homepage', $user->homepage) }}"
                                        placeholder="https://yourwebsite.com"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Your personal or institutional website</p>
                            </div>

                            <!-- ORCID iD -->
                            <div>
                                <label for="orcid_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="inline-block w-4 h-4 mr-1" viewBox="0 0 256 256" fill="#A6CE39">
                                        <path
                                            d="M256,128c0,70.7-57.3,128-128,128C57.3,256,0,198.7,0,128C0,57.3,57.3,0,128,0C198.7,0,256,57.3,256,128z" />
                                        <g>
                                            <path fill="#FFFFFF" d="M86.3,186.2H70.9V79.1h15.4v48.4V186.2z" />
                                            <path fill="#FFFFFF"
                                                d="M108.9,79.1h41.6c39.6,0,57,28.3,57,53.6c0,27.5-21.5,53.6-56.8,53.6h-41.8V79.1z M124.3,172.4h24.5c34.9,0,42.9-26.5,42.9-39.7c0-21.5-13.7-39.7-43.7-39.7h-23.7V172.4z" />
                                            <path fill="#FFFFFF"
                                                d="M88.7,56.8c0,5.5-4.5,10.1-10.1,10.1c-5.6,0-10.1-4.6-10.1-10.1c0-5.6,4.5-10.1,10.1-10.1C84.2,46.7,88.7,51.3,88.7,56.8z" />
                                        </g>
                                    </svg>
                                    ORCID iD
                                </label>
                                <div class="relative">
                                    <input type="text" name="orcid_id" id="orcid_id"
                                        value="{{ old('orcid_id', $user->orcid_id) }}"
                                        placeholder="https://orcid.org/0000-0001-2345-6789"
                                        class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Your unique researcher identifier. Format:
                                    https://orcid.org/0000-0001-2345-6789</p>
                                @error('orcid_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: Password -->
                    <div x-show="activeTab === 'password'" x-cloak>
                        <!-- Content moved to separate form below -->
                    </div>

                    <!-- Save Button (for tabs 1-3) -->
                    <div x-show="['identity', 'contact', 'public'].includes(activeTab)"
                        class="mt-8 flex justify-end border-t border-gray-200 pt-6">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>

                <!-- Password Tab Content (Separate Form) -->
                <div x-show="activeTab === 'password'" x-cloak class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h3>
                    <form action="{{ route('journal.profile.password', $journal->slug) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Password -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Current Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="current_password" id="current_password" required
                                    autocomplete="current-password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('current_password') border-red-500 @enderror">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" id="password" required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Min 8 characters, mixed case, numbers</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    required autocomplete="new-password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end border-t border-gray-200 pt-6">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Roles Tab Content (Separate Form) -->
                @if ($journal && $availableRoles->isNotEmpty())
                    <div x-show="activeTab === 'roles'" x-cloak class="p-6">
                        @include('profile.partials.tab-roles')
                    </div>
                @endif
            </div>

            <!-- Account Stats -->
            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Account Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-1">Member Since</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-1">Email Verified</p>
                            <p
                                class="text-lg font-semibold {{ $user->email_verified_at ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                            </p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-1">Total Submissions</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $user->submissions()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>



    @push('scripts')
        <script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
        <script>
            tinymce.init({
                selector: '#bio',
                height: 350,
                menubar: false,
                plugins: 'lists link image table code autoresize',
                toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | table link image | code',
                branding: false,
                license_key: 'gpl',
                images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '{{ route('journal.profile.upload.image', $journal->slug) }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                    xhr.upload.onprogress = (e) => {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = () => {
                        if (xhr.status === 403) {
                            reject({
                                message: 'HTTP Error: ' + xhr.status,
                                remove: true
                            });
                            return;
                        }

                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }

                        const json = JSON.parse(xhr.responseText);

                        if (!json || typeof json.location != 'string') {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }

                        resolve(json.location);
                    };

                    xhr.onerror = () => {
                        reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    xhr.send(formData);
                })
            });
        </script>
    @endpush
</x-app-layout>
