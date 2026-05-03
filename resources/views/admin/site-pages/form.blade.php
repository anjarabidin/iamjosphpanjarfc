@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.site-pages.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $title }}</h1>
                        <p class="text-sm text-gray-500">{{ $page ? 'Update page content' : 'Create a new static page' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ $page ? route('admin.site-pages.update', $page) : route('admin.site-pages.store') }}" 
              method="POST"
              class="space-y-6">
            @csrf
            @if($page)
                @method('PUT')
            @endif

            {{-- Page Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="font-semibold text-gray-900">Page Settings</h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Page Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title"
                               value="{{ old('title', $page?->title) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Enter page title">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                            URL Slug
                        </label>
                        <div class="flex items-center">
                            <span class="text-gray-500 text-sm mr-2">{{ url('/page/') }}/</span>
                            <input type="text" 
                                   name="slug" 
                                   id="slug"
                                   value="{{ old('slug', $page?->slug) }}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="auto-generated-from-title">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from title</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Published --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" 
                               name="is_published" 
                               id="is_published"
                               value="1"
                               {{ old('is_published', $page?->is_published) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_published" class="text-sm font-medium text-gray-700">
                            Publish this page
                        </label>
                    </div>
                </div>
            </div>

            {{-- Page Content --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="font-semibold text-gray-900">Page Content</h2>
                    <p class="text-xs text-gray-500 mt-1">Use the editor to create rich content. Tailwind CSS classes are supported.</p>
                </div>
                <div class="p-6">
                    <textarea name="content" 
                              id="content"
                              class="tinymce-editor"
                              rows="20">{{ old('content', $page?->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.site-pages.index') }}"
                   class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fa-solid fa-save mr-2"></i>
                    {{ $page ? 'Update Page' : 'Create Page' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .tox-tinymce {
        border-radius: 0.5rem !important;
        border: 1px solid #d1d5db !important;
    }
    .tox-editor-header {
        border-bottom: 1px solid #e5e7eb !important;
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        height: 500,
        branding: false,
        license_key: 'gpl',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'link image media table | code fullscreen | removeformat help',
        menubar: 'file edit view insert format tools table help',
        content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; }',
        
        // Allow all HTML elements and attributes (for Tailwind CSS)
        valid_elements: '*[*]',
        extended_valid_elements: '*[*]',
        valid_children: '+body[style],+div[style]',
        
        // Keep raw HTML intact
        verify_html: false,
        cleanup: false,
        
        // Image upload configuration
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_url: '{{ route("profile.upload.image") }}', // Reuse existing image upload route
        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                fetch('{{ route("profile.upload.image") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.location) {
                        resolve(result.location);
                    } else {
                        reject('Image upload failed');
                    }
                })
                .catch(error => {
                    reject('Image upload failed: ' + error);
                });
            });
        },
    });
</script>
@endpush
@endsection
