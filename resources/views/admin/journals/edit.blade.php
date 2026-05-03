@extends('layouts.admin')

@section('title', 'Edit Journal - ' . $journal->name)

@section('content')
<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="{{ route('admin.journals.index') }}" class="hover:text-indigo-600 transition-colors">Hosted Journals</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
    <span class="text-gray-900">{{ $journal->name }}</span>
</div>

<!-- Page Header -->
<div class="flex items-start justify-between mb-8">
    <div class="flex items-center gap-4">
        @if ($journal->logo_path)
        <img src="{{ Storage::url($journal->logo_path) }}" alt="{{ $journal->name }}"
            class="w-16 h-16 rounded-xl object-cover border border-gray-200">
        @else
        <div
            class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center">
            <span
                class="text-indigo-600 font-bold text-xl">{{ strtoupper(substr($journal->abbreviation ?? $journal->name, 0, 2)) }}</span>
        </div>
        @endif
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $journal->name }}</h1>
            <p class="text-gray-500">
                <code class="font-mono text-sm text-indigo-600">/{{ $journal->slug }}</code>
                <span class="mx-2">•</span>
                @if ($journal->enabled)
                <span class="text-emerald-600">Active</span>
                @else
                <span class="text-gray-500">Disabled</span>
                @endif
            </p>
        </div>
    </div>
    <a href="{{ route('journal.public.home', ['journal' => $journal->slug]) }}" target="_blank"
        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
        View Public Site
    </a>
</div>

<!-- Form -->
<form action="{{ route('admin.journals.update', $journal) }}" method="POST" enctype="multipart/form-data"
    class="w-full max-w-5xl">
    @csrf
    @method('PUT')

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
                    <input type="text" id="name" name="name" value="{{ old('name', $journal->name) }}"
                        required
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
                    <input type="text" id="abbreviation" name="abbreviation"
                        value="{{ old('abbreviation', $journal->abbreviation) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('abbreviation') border-red-500 @enderror">
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
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $journal->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Toggles -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                        <input type="checkbox" id="enabled" name="enabled" value="1"
                            {{ old('enabled', $journal->enabled) ? 'checked' : '' }}
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="enabled" class="cursor-pointer">
                            <span class="block text-sm font-medium text-gray-900">Enable Journal</span>
                            <span class="block text-xs text-gray-500">Journal is accessible to public</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                        <input type="checkbox" id="visible" name="visible" value="1"
                            {{ old('visible', $journal->visible) ? 'checked' : '' }}
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="visible" class="cursor-pointer">
                            <span class="block text-sm font-medium text-gray-900">Show in Portal</span>
                            <span class="block text-xs text-gray-500">Visible in the journal directory</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Journal Content (Rich Text) -->
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-2">Journal Content</h2>
            <p class="text-sm text-gray-500 mb-6">Rich text content for your journal's public pages. Supports formatting, links, and images.</p>

            <div class="space-y-6">
                <!-- Journal Summary -->
                <div>
                    <label for="summary" class="block text-sm font-medium text-gray-700 mb-2">
                        Journal Summary
                    </label>
                    <textarea id="summary" name="summary" rows="6"
                        class="tinymce-editor w-full">{{ old('summary', $journal->summary ?? '') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">A brief summary displayed on the journal homepage and listings.</p>
                    @error('summary')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- About the Journal -->
                <div>
                    <label for="about" class="block text-sm font-medium text-gray-700 mb-2">
                        About the Journal
                    </label>
                    <textarea id="about" name="about" rows="8"
                        class="tinymce-editor w-full">{{ old('about', $journal->about ?? '') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Detailed information about the journal's scope, aims, and policies.</p>
                    @error('about')
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
                    <input type="text" id="publisher" name="publisher"
                        value="{{ old('publisher', $journal->publisher) }}"
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
                        <input type="text" id="issn_print" name="issn_print"
                            value="{{ old('issn_print', $journal->issn_print) }}" placeholder="e.g., 1234-5678"
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
                        <input type="text" id="issn_online" name="issn_online"
                            value="{{ old('issn_online', $journal->issn_online) }}" placeholder="e.g., 8765-4321"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('issn_online') border-red-500 @enderror">
                        @error('issn_online')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Branding -->
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Branding</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <!-- Logo -->
                <div x-data="{ previewUrl: '{{ $journal->logo_path ? Storage::url($journal->logo_path) : '' }}' }">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Journal Logo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center border-2 border-dashed border-gray-300"
                            :class="{ 'border-solid border-indigo-300': previewUrl }">
                            <template x-if="!previewUrl">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </template>
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div>
                            <input type="file" id="logo" name="logo" accept="image/*" class="hidden"
                                @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                            <label for="logo"
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 cursor-pointer transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Max 2MB</p>
                        </div>
                    </div>
                    @error('logo')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Thumbnail -->
                <div x-data="{ previewUrl: '{{ $journal->thumbnail_path ? Storage::url($journal->thumbnail_path) : '' }}' }">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Thumbnail</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center border-2 border-dashed border-gray-300"
                            :class="{ 'border-solid border-indigo-300': previewUrl }">
                            <template x-if="!previewUrl">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </template>
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div>
                            <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="hidden"
                                @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                            <label for="thumbnail"
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 cursor-pointer transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Max 1MB</p>
                        </div>
                    </div>
                    @error('thumbnail')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="p-6 bg-gray-50 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Last updated: <span
                    class="font-medium text-gray-900">{{ $journal->updated_at->format('M d, Y H:i') }}</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.journals.index') }}"
                    class="px-5 py-2.5 text-gray-700 font-medium hover:text-gray-900 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 hover:shadow-indigo-500/40 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Quick Actions -->
<div class="mt-8 w-full max-w-5xl">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('journal.admin.users.index', ['journal' => $journal->slug]) }}"
            class="p-4 bg-white border border-gray-200 rounded-xl hover:border-emerald-300 hover:shadow-md transition-all group">
            <div
                class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition-colors">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <p class="font-semibold text-gray-900">Manage Users</p>
            <p class="text-sm text-gray-500">Add editors, reviewers, and authors</p>
        </a>

        <a href="{{ route('journal.admin.sections.index', ['journal' => $journal->slug]) }}"
            class="p-4 bg-white border border-gray-200 rounded-xl hover:border-blue-300 hover:shadow-md transition-all group">
            <div
                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-colors">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
            </div>
            <p class="font-semibold text-gray-900">Sections</p>
            <p class="text-sm text-gray-500">Configure article sections</p>
        </a>

        <a href="{{ route('journal.issues.index', ['journal' => $journal->slug]) }}"
            class="p-4 bg-white border border-gray-200 rounded-xl hover:border-purple-300 hover:shadow-md transition-all group">
            <div
                class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-200 transition-colors">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <p class="font-semibold text-gray-900">Issues</p>
            <p class="text-sm text-gray-500">Manage journal issues</p>
        </a>
    </div>
</div>
     @push('scripts')
        <script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
        <script>
            tinymce.init({
                selector: '#summary,#about',
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
@endsection
