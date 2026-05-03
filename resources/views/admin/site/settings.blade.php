@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Site Settings</h1>
        <p class="mt-1 text-gray-500">Configure global settings for your IAMJOS installation.</p>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Settings Form -->
        <div x-data="{ activeTab: 'general' }">
            <!-- Tabs Navigation -->
            <div class="mb-6 flex space-x-2 border-b border-gray-200">
                <button @click="activeTab = 'general'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'general' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    General Settings
                </button>
                <button @click="activeTab = 'whatsapp'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'whatsapp', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'whatsapp' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    WhatsApp Gateway
                </button>
                <button @click="activeTab = 'recaptcha'"
                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'recaptcha', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'recaptcha' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Google reCAPTCHA
                </button>
            </div>

            <form action="{{ route('admin.site.settings.update') }}" method="POST"
                class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                @csrf

                <!-- General Tab -->
                <div x-show="activeTab === 'general'" class="animate-fade-in">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">General Configuration</h2>

                        <div class="space-y-6">
                            <!-- Site Title -->
                            <div>
                                <label for="site_title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Site Title
                                </label>
                                <input type="text" id="site_title" name="site_title"
                                    value="{{ old('site_title', $siteSetting->site_title) }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">The main title displayed on the browser tab and meta
                                    tags.
                                </p>
                            </div>

                            <!-- Site Intro -->
                            <div>
                                <label for="site_intro" class="block text-sm font-medium text-gray-700 mb-2">
                                    Site Introduction
                                </label>
                                <textarea id="site_intro" name="site_intro" rows="3"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('site_intro', $siteSetting->site_intro) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">A brief description displayed on the portal homepage.
                                </p>
                            </div>

                            <!-- About Content -->
                            <div>
                                <label for="about_content" class="block text-sm font-medium text-gray-700 mb-2">
                                    About the Site
                                </label>
                                <textarea id="about_content" name="about_content" rows="8"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 tinymce-editor">{{ old('about_content', $siteSetting->about_content) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Custom HTML content for the portal about page.
                                    Supports rich text formatting.</p>
                            </div>

                            <!-- Footer Content -->
                            <div>
                                <label for="footer_content" class="block text-sm font-medium text-gray-700 mb-2">
                                    Footer Content
                                </label>
                                <textarea id="footer_content" name="footer_content" rows="8"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 tinymce-editor">{{ old('footer_content', $siteSetting->footer_content) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Custom HTML content for the portal footer. Supports
                                    rich text formatting.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Security & Routing</h2>

                        <div class="space-y-6">
                            <!-- Min Password Length -->
                            <div>
                                <label for="min_password_length" class="block text-sm font-medium text-gray-700 mb-2">
                                    Minimum Password Length
                                </label>
                                <input type="number" id="min_password_length" name="min_password_length" min="6"
                                    max="32"
                                    value="{{ old('min_password_length', $siteSetting->min_password_length) }}"
                                    class="w-full sm:w-1/2 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Redirect to Journal -->
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                                <input type="checkbox" id="redirect_to_journal" name="redirect_to_journal" value="1"
                                    {{ old('redirect_to_journal', $siteSetting->redirect_to_journal) ? 'checked' : '' }}
                                    class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="redirect_to_journal" class="cursor-pointer">
                                    <span class="block text-sm font-medium text-gray-900">Redirect to Single Journal</span>
                                    <span class="block text-xs text-gray-500 mt-1">
                                        If enabled and only one active journal exists, visitors to the portal home will be
                                        automatically redirected to that journal.
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Tab -->
                <div x-show="activeTab === 'whatsapp'" class="p-6 space-y-6 animate-fade-in-up" style="display: none;">

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Configure your WhatsApp Gateway provider here. These settings will be used to send
                                    automated notifications (Submission Ack, LoA, etc.).
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="wa_api_url" class="block text-sm font-medium text-gray-700 mb-2">
                                Gateway API Link
                            </label>
                            <input id="wa_api_url" name="wa_api_url" type="url"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                value="{{ old('wa_api_url', $siteSetting->wa_api_url) }}"
                                placeholder="https://api.wa-gateway.com/v1/send">
                            @error('wa_api_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="wa_sender_number" class="block text-sm font-medium text-gray-700 mb-2">
                                WhatsApp Number
                            </label>
                            <input id="wa_sender_number" name="wa_sender_number" type="text"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                value="{{ old('wa_sender_number', $siteSetting->wa_sender_number) }}"
                                placeholder="628123456789">
                            <p class="mt-1 text-xs text-gray-500">Format: 628xxx (Country code included).</p>
                            @error('wa_sender_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="wa_device_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Device ID / API Token
                            </label>
                            <input id="wa_device_id" name="wa_device_id" type="text"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono"
                                value="{{ old('wa_device_id', $siteSetting->wa_device_id) }}">
                            @error('wa_device_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- reCAPTCHA Tab -->
                <div x-show="activeTab === 'recaptcha'" class="p-6 space-y-6 animate-fade-in-up" style="display: none;">

                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-indigo-800">Global Google reCAPTCHA Configuration</h3>
                                <div class="mt-2 text-sm text-indigo-700">
                                    <p>Configure Google reCAPTCHA v2 keys here. These keys will be used globally across all
                                        journals. Individual journals can enable/disable the feature, but they will use
                                        these shared keys.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="recaptcha_site_key" class="block text-sm font-medium text-gray-700 mb-2">
                                Site Key
                            </label>
                            <input id="recaptcha_site_key" name="recaptcha_site_key" type="text"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono"
                                value="{{ old('recaptcha_site_key', $siteSetting->recaptcha_site_key) }}"
                                placeholder="6LeIxAcTAAAAAJcZZZZZZZZZZZZZZZZZZZZZZZZ">
                            <p class="mt-1 text-xs text-gray-500">Public key used in the HTML code.</p>
                            @error('recaptcha_site_key')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="recaptcha_secret_key" class="block text-sm font-medium text-gray-700 mb-2">
                                Secret Key
                            </label>
                            <input id="recaptcha_secret_key" name="recaptcha_secret_key" type="text"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono"
                                value="{{ old('recaptcha_secret_key', $siteSetting->recaptcha_secret_key) }}"
                                placeholder="6LeIxAcTAAAAAGG-vFI1TnRWxXXXXXXXXXXXXXXX">
                            <p class="mt-1 text-xs text-gray-500">Private key for server-side validation.</p>
                            @error('recaptcha_secret_key')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 flex items-center justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 hover:shadow-indigo-500/40 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        // TinyMCE initialization
        tinymce.init({
            selector: '#footer_content, #about_content',
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
