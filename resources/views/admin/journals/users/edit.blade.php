@extends('layouts.app')

@section('title', 'Edit User Roles')

@section('content')
    <div class="mb-8">
        <nav class="flex mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <span class="text-gray-400 text-sm font-medium">Journal Manager</span>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                        <a href="{{ route($routePrefix . '.index', ['journal' => $journal->slug]) }}"
                            class="text-sm font-medium text-gray-500 hover:text-indigo-600">Users</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                        <span class="text-sm font-medium text-indigo-600">Edit User</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Edit User Roles</h1>
        <p class="text-sm text-gray-500 mt-1">Update roles for <strong>{{ $user->name }}</strong> in
            <strong>{{ $journal->name }}</strong>.</p>
    </div>

    <form action="{{ route($routePrefix . '.update', ['journal' => $journal->slug, 'user' => $user->id]) }}" method="POST"
        class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden max-w-4xl">
        @csrf
        @method('PUT')

        {{-- Section 1: Identity --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-id-card text-indigo-500 mr-2"></i> Identity
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Given Name --}}
                <div>
                    <label for="given_name" class="block text-sm font-medium text-gray-700">Given Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="given_name" id="given_name"
                        value="{{ old('given_name', $user->given_name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    @error('given_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Family Name --}}
                <div>
                    <label for="family_name" class="block text-sm font-medium text-gray-700">Family Name</label>
                    <input type="text" name="family_name" id="family_name"
                        value="{{ old('family_name', $user->family_name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    @error('family_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Preferred Public Name --}}
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Preferred Public Name <span
                            class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500 mb-1">How the user prefers to be addressed (e.g. "Dr. Jane Doe").</p>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Contact --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-address-book text-indigo-500 mr-2"></i> Contact
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Affiliation --}}
                <div class="md:col-span-2">
                    <label for="affiliation" class="block text-sm font-medium text-gray-700">Affiliation</label>
                    <input type="text" name="affiliation" id="affiliation"
                        value="{{ old('affiliation', $user->affiliation) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    @error('affiliation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mailing Address --}}
                <div class="md:col-span-2">
                    <label for="mailing_address" class="block text-sm font-medium text-gray-700">Mailing Address</label>
                    <textarea name="mailing_address" id="mailing_address" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">{{ old('mailing_address', $user->mailing_address) }}</textarea>
                    @error('mailing_address')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Country --}}
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                    @php
                        $countries = [
                            'ID' => 'Indonesia',
                            'US' => 'United States',
                            'GB' => 'United Kingdom',
                            'AU' => 'Australia',
                            'CA' => 'Canada',
                            'MY' => 'Malaysia',
                            'SG' => 'Singapore',
                            'JP' => 'Japan',
                            'CN' => 'China',
                            'KR' => 'South Korea',
                            'IN' => 'India',
                            'TH' => 'Thailand',
                            'VN' => 'Vietnam',
                            'PH' => 'Philippines',
                            // Add more as needed or use a helper
                        ];
                    @endphp
                    <select name="country" id="country"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                        <option value="">Select Country...</option>
                        @foreach ($countries as $code => $name)
                            <option value="{{ $code }}"
                                {{ old('country', $user->country) == $code ? 'selected' : '' }}>{{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">If your country is not listed, please contact support.</p>
                </div>
            </div>
        </div>

        {{-- Section 3: Roles --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-user-tag text-indigo-500 mr-2"></i> Roles
            </h2>
            <p class="text-sm text-gray-500 mb-4">Select roles for <strong>{{ $journal->name }}</strong>.</p>

            @error('roles')
                <p class="mb-3 text-xs text-red-500">{{ $message }}</p>
            @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @php
                    $roleDescriptions = [
                        'Journal Manager' => 'Full administrative access to the journal',
                        'Editor' => 'Can manage submissions and make editorial decisions',
                        'Section Editor' => 'Manages submissions within assigned sections',
                        'Reviewer' => 'Can review submissions and provide recommendations',
                        'Author' => 'Can submit manuscripts to the journal',
                        'Reader' => 'Basic access to published content',
                        'Admin' => 'Administrative access across all journals',
                    ];
                    $roleColors = [
                        'Journal Manager' => 'border-red-200 bg-red-50 hover:bg-red-100',
                        'Editor' => 'border-blue-200 bg-blue-50 hover:bg-blue-100',
                        'Section Editor' => 'border-blue-200 bg-blue-50 hover:bg-blue-100',
                        'Reviewer' => 'border-amber-200 bg-amber-50 hover:bg-amber-100',
                        'Author' => 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100',
                        'Reader' => 'border-gray-200 bg-gray-50 hover:bg-gray-100',
                        'Admin' => 'border-purple-200 bg-purple-50 hover:bg-purple-100',
                    ];
                    $selectedRoles = old('roles', $userRoleNames ?? []);
                @endphp

                @foreach ($roles as $role)
                    <label
                        class="relative flex items-start p-4 rounded-lg border cursor-pointer transition-all {{ $roleColors[$role->name] ?? 'border-gray-200 bg-gray-50 hover:bg-gray-100' }} {{ in_array($role->id, $selectedRoles) ? 'ring-2 ring-indigo-500' : '' }}">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }}
                                class="h-4 w-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">{{ $role->name }}</span>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $roleDescriptions[$role->name] ?? 'Standard access role' }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Section 4: Public Profile --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-globe text-indigo-500 mr-2"></i> Public Profile
            </h2>
            <div class="grid grid-cols-1 gap-6">
                {{-- ORCID --}}
                <div>
                    <label for="orcid_id" class="block text-sm font-medium text-gray-700">ORCID iD</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span
                            class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                            https://orcid.org/
                        </span>
                        <input type="text" name="orcid_id" id="orcid_id"
                            value="{{ old('orcid_id', str_replace('https://orcid.org/', '', $user->orcid_id)) }}"
                            placeholder="0000-0000-0000-0000"
                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border">
                    </div>
                </div>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio Statement</label>
                    <textarea name="bio" id="bio" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 5: Account --}}
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-lock text-indigo-500 mr-2"></i> Account Access
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Username --}}
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    @error('username')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Fields --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-yellow-800 flex items-center">
                            <i class="fa-solid fa-key mr-2"></i> Update Password
                        </h3>
                        <p class="text-xs text-yellow-600 mt-1">Leave blank to keep the current password.</p>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Repeat New
                            Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 md:col-span-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
            <a href="{{ route($routePrefix . '.index', ['journal' => $journal->slug]) }}"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">Cancel</a>
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium shadow-sm transition-colors">
                Save Changes
            </button>
        </div>
    </form>
@endsection
