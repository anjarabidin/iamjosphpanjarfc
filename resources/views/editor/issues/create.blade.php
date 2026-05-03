@php
    $journal = current_journal();
@endphp

@extends('layouts.app')

@section('title', 'Create New Issue')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                    <a href="{{ route('journal.issues.index', ['journal' => $journal->slug]) }}"
                        class="hover:text-indigo-600 transition-colors">Issues</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900">Create New</span>
                </div>

                <h1 class="text-3xl font-bold text-indigo-700">
                    Create New Issue
                </h1>
                <p class="mt-2 text-gray-500">
                    Set up a new issue to schedule articles for publication.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('journal.issues.store', ['journal' => $journal->slug]) }}" method="POST"
                    enctype="multipart/form-data" 
                    x-data="{
                        volume: '{{ old('volume', $suggestedVolume) }}',
                        number: '{{ old('number', $suggestedNumber) }}',
                        year: '{{ old('year', $suggestedYear) }}',
                        title: '{{ old('title', '') }}',
                        showTitle: {{ old('show_title', false) ? 'true' : 'false' }},
                        urlPath: '{{ old('url_path', '') }}',
                        manualUrlPath: {{ old('url_path') ? 'true' : 'false' }},
                        generateSlug() {
                            if (this.manualUrlPath) return;
                            let slug = '';
                            if (this.showTitle && this.title) {
                                slug = this.title;
                            } else {
                                slug = 'v' + (this.volume || '1') + '-n' + (this.number || '1') + '-' + (this.year || new Date().getFullYear());
                            }
                            this.urlPath = slug.toLowerCase().replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '');
                        }
                    }">
                    @csrf

                    <div class="p-6 space-y-6">
                        <!-- Issue Identification -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                Issue Identification
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                <!-- Volume -->
                                <div>
                                    <label for="volume" class="block text-sm font-medium text-gray-700 mb-1">
                                        Volume <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="volume" name="volume"
                                        x-model="volume" @input="generateSlug"
                                        min="1" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 transition-colors @error('volume') border-red-500 @enderror">
                                    @error('volume')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Number -->
                                <div>
                                    <label for="number" class="block text-sm font-medium text-gray-700 mb-1">
                                        Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="number" name="number"
                                        x-model="number" @input="generateSlug"
                                        min="1" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 transition-colors @error('number') border-red-500 @enderror">
                                    @error('number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Year -->
                                <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">
                                        Year <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="year" name="year"
                                        x-model="year" @input="generateSlug"
                                        min="2000" max="2100" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 transition-colors @error('year') border-red-500 @enderror">
                                    @error('year')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- OJS 3.3 Display Toggles -->
                            <div class="flex flex-wrap gap-6 mb-4 bg-gray-50 rounded-lg p-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_volume" value="1"
                                        {{ old('show_volume', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Show Volume</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_number" value="1"
                                        {{ old('show_number', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Show Number</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_year" value="1"
                                        {{ old('show_year', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Show Year</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_title" value="1"
                                        x-model="showTitle" @change="generateSlug"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Show Title</span>
                                </label>
                            </div>

                            <p class="text-sm text-gray-500 bg-blue-50 rounded-lg p-3 border border-blue-100">
                                <svg class="w-4 h-4 inline-block mr-1 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                This will create issue: <strong>Vol. <span x-text="volume || '1'"></span> No. <span x-text="number || '1'"></span>, <span x-text="year || new Date().getFullYear()"></span> <span x-show="showTitle && title" x-text="'- ' + title"></span></strong>
                            </p>
                        </div>

                        <!-- Title (Optional) -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                                Issue Title
                            </h3>

                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Title <span class="text-gray-400">(Optional - for special/themed issues)</span>
                                </label>
                                <input type="text" id="title" name="title"
                                    x-model="title" @input="generateSlug"
                                    placeholder="e.g., Special Issue on AI in Education"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 transition-colors @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Description
                            </h3>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Issue Description <span class="text-gray-400">(Optional)</span>
                                </label>
                                <textarea id="description" name="description" rows="6"
                                    placeholder="Enter detailed description for this issue..."
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 transition-colors @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                <p class="mt-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4 inline-block mr-1 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    You can use HTML formatting. A rich text editor (TinyMCE/CKEditor) can be integrated
                                    here.
                                </p>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Cover Image -->
                        <div x-data="{ previewUrl: null }">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Cover Image
                            </h3>

                            <div class="flex items-start gap-6">
                                <!-- Preview -->
                                <div
                                    class="w-32 h-44 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl overflow-hidden flex-shrink-0">
                                    <template x-if="previewUrl">
                                        <img :src="previewUrl" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!previewUrl">
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>

                                <!-- Upload -->
                                <div class="flex-1">
                                    <label for="cover" class="block text-sm font-medium text-gray-700 mb-1">
                                        Upload Cover <span class="text-gray-400">(Optional)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" id="cover" name="cover" accept="image/*"
                                            @change="previewUrl = URL.createObjectURL($event.target.files[0])"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Recommended size: 300x400 pixels. Max file size: 2MB.
                                    </p>
                                    @error('cover')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- URL Path -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                Custom URL
                            </h3>

                            <div>
                                <label for="url_path" class="block text-sm font-medium text-gray-700 mb-1">
                                    URL Path <span class="text-gray-400">(Optional)</span>
                                </label>
                                <input type="text" id="url_path" name="url_path" 
                                    x-model="urlPath" @input="manualUrlPath = true"
                                    placeholder="e.g., vol1-no2-2026"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 transition-colors @error('url_path') border-red-500 @enderror">
                                <p class="mt-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4 inline-block mr-1 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    An optional path to use in the URL instead of the issue ID. Only letters, numbers,
                                    dashes, and underscores allowed.
                                </p>
                                @error('url_path')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('journal.issues.index', ['journal' => $journal->slug]) }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:bg-indigo-700 transition-all">
                            Create Issue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
<script>
    tinymce.init({
        selector: '#description',
        height: 350,
        menubar: false,
        plugins: 'lists link image table code autoresize',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | table link image | code',
        branding: false,
        license_key: 'gpl',
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '{{ route('profile.upload.image') }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = () => {
                if (xhr.status === 403) {
                    reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
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
