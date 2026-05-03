@extends('layouts.admin')

@section('title', 'Create Journal')

@section('content')
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('admin.journals.index') }}" class="hover:text-indigo-600 transition-colors">Hosted Journals</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-900">Create New Journal</span>
    </div>

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create New Journal</h1>
        <p class="mt-1 text-gray-500">Set up a new academic journal on your platform.</p>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.journals.store') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-5xl">
        @csrf

        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
            <!-- Basic Information -->
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Basic Information</h2>

                <div class="space-y-6">
                    <!-- Journal Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Journal Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            placeholder="e.g., Journal of Computer Science"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Abbreviation -->
                    <div>
                        <label for="abbreviation" class="block text-sm font-medium text-gray-700 mb-2">
                            Abbreviation
                        </label>
                        <input type="text" id="abbreviation" name="abbreviation" value="{{ old('abbreviation') }}"
                            placeholder="e.g., JCS"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('abbreviation') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">This will be used to generate the URL path if provided.</p>
                        @error('abbreviation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" name="description" rows="4"
                            placeholder="Brief description of the journal's scope and focus..."
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Publisher & Identifiers -->
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Publisher & Identifiers</h2>

                <div class="space-y-6">
                    <!-- Publisher -->
                    <div>
                        <label for="publisher" class="block text-sm font-medium text-gray-700 mb-2">
                            Publisher
                        </label>
                        <input type="text" id="publisher" name="publisher" value="{{ old('publisher') }}"
                            placeholder="e.g., Indonesian Academic Publishers"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('publisher') border-red-500 @enderror">
                        @error('publisher')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ISSN Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Print ISSN -->
                        <div>
                            <label for="issn_print" class="block text-sm font-medium text-gray-700 mb-2">
                                Print ISSN
                            </label>
                            <input type="text" id="issn_print" name="issn_print" value="{{ old('issn_print') }}"
                                placeholder="e.g., 1234-5678"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('issn_print') border-red-500 @enderror">
                            @error('issn_print')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Online ISSN -->
                        <div>
                            <label for="issn_online" class="block text-sm font-medium text-gray-700 mb-2">
                                Online ISSN
                            </label>
                            <input type="text" id="issn_online" name="issn_online" value="{{ old('issn_online') }}"
                                placeholder="e.g., 8765-4321"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('issn_online') border-red-500 @enderror">
                            @error('issn_online')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Journal Logo -->
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Journal Logo</h2>

                <div x-data="{ previewUrl: null }">
                    <label class="block">
                        <div class="flex items-center gap-6">
                            <!-- Preview -->
                            <div class="w-24 h-24 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center border-2 border-dashed border-gray-300"
                                :class="{ 'border-solid border-indigo-300': previewUrl }">
                                <template x-if="!previewUrl">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </template>
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" class="w-full h-full object-cover">
                                </template>
                            </div>

                            <!-- Upload Input -->
                            <div class="flex-1">
                                <input type="file" id="logo" name="logo" accept="image/*" class="hidden"
                                    @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                                <label for="logo"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 cursor-pointer transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Choose Logo
                                </label>
                                <p class="mt-2 text-xs text-gray-500">PNG, JPG up to 2MB. Recommended size: 200x200px</p>
                            </div>
                        </div>
                    </label>
                    @error('logo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-gray-50 flex items-center justify-end gap-4">
                <a href="{{ route('admin.journals.index') }}"
                    class="px-5 py-2.5 text-gray-700 font-medium hover:text-gray-900 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 hover:shadow-indigo-500/40 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Journal
                </button>
            </div>
        </div>
    </form>
   
@endsection
