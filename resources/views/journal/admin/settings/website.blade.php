@php
    $journal = current_journal();
    $journalSlug = $journal->slug;
@endphp

<x-app-layout :journal="$journal" :journalSlug="$journalSlug">
    <x-slot name="title">Website Settings - {{ $journal->name }}</x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="{ activeTab: 'appearance' }">
        {{-- Page Header --}}
        <div class="mb-8">
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('journal.settings.index', $journalSlug) }}" class="hover:text-indigo-600">Settings</a>
                <span class="mx-2">/</span>
                <span class="text-gray-700">Website</span>
            </nav>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Website Settings</h1>
                    <p class="text-gray-500 mt-1">Configure your journal's public website appearance</p>
                </div>
                <a href="{{ route('journal.public.home', $journalSlug) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-external-link-alt mr-2"></i>
                    Preview
                </a>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Tabs Navigation (OJS 3.3 Style) --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-8">
                <button @click="activeTab = 'appearance'"
                    :class="activeTab === 'appearance' ? 'border-indigo-500 text-indigo-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fa-solid fa-palette mr-2"></i>
                    Appearance
                </button>
                <button @click="activeTab = 'information'"
                    :class="activeTab === 'information' ? 'border-indigo-500 text-indigo-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    Information
                </button>
                <button @click="activeTab = 'sections'"
                    :class="activeTab === 'sections' ? 'border-indigo-500 text-indigo-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fa-solid fa-th-large mr-2"></i>
                    Sections
                </button>
                <button @click="activeTab = 'security'"
                    :class="activeTab === 'security' ? 'border-indigo-500 text-indigo-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fa-solid fa-shield-halved mr-2"></i>
                    Security
                </button>
            </nav>
        </div>

        {{-- Form --}}
        <form action="{{ route('journal.settings.website.update', $journalSlug) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ============================================ --}}
            {{-- Tab: APPEARANCE (Parent Tab) --}}
            {{-- ============================================ --}}
            <div x-show="activeTab === 'appearance'" x-cloak class="space-y-6" x-data="{ appearanceTab: 'setup' }">

                {{-- Sub Tabs Navigation --}}
                <div class="border-b border-gray-200 mb-4">
                    <nav class="-mb-px flex space-x-6">
                        <button type="button" @click="appearanceTab = 'setup'"
                            :class="appearanceTab === 'setup' ? 'border-indigo-500 text-indigo-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Setup
                        </button>
                        <button type="button" @click="appearanceTab = 'advanced'"
                            :class="appearanceTab === 'advanced' ? 'border-indigo-500 text-indigo-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Advanced
                        </button>
                        <button type="button" @click="appearanceTab = 'menu_nav'"
                            :class="appearanceTab === 'menu_nav' ? 'border-indigo-500 text-indigo-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Menu Nav
                        </button>
                    </nav>
                </div>

                {{-- Sub-tab: SETUP (Old Setup Content) --}}
                <div x-show="appearanceTab === 'setup'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Logo Upload --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                        <div class="flex items-start gap-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Logo</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload a logo image to be displayed at the top of every journal page.
                                </p>

                                {{-- Current Logo Preview --}}
                                @if ($journal->logo_path)
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200 inline-block">
                                        <img src="{{ Storage::url($journal->logo_path) }}" alt="Current Logo"
                                            class="max-h-20 w-auto">
                                        <p class="text-xs text-gray-500 mt-2">Current Logo</p>
                                    </div>
                                @endif

                                {{-- File Input --}}
                                <div class="relative">
                                    <input type="file" name="logo" id="logo_input"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Recommended: PNG or JPG. Max size: 2MB.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Journal Thumbnail --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                        <div class="flex items-start gap-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Journal Thumbnail</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    A small image that represents this journal. Used in journal listings and search
                                    results.
                                </p>

                                {{-- Current Thumbnail Preview --}}
                                @if ($journal->thumbnail_path)
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200 inline-block">
                                        <img src="{{ Storage::url($journal->thumbnail_path) }}" alt="Current Thumbnail"
                                            class="max-h-24 w-auto rounded">
                                        <p class="text-xs text-gray-500 mt-2">Current Thumbnail</p>
                                    </div>
                                @endif

                                {{-- File Input --}}
                                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                <p class="mt-2 text-xs text-gray-500">
                                    Recommended: 150x150px square image. Max size: 2MB.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Homepage Image --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                        <div class="flex items-start gap-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Homepage Image</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    This image will be displayed prominently on the journal homepage.
                                </p>

                                {{-- Current Homepage Image Preview --}}
                                @if ($journal->homepage_image_path)
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <img src="{{ Storage::url($journal->homepage_image_path) }}"
                                            alt="Homepage Image" class="max-h-40 w-auto rounded-lg shadow-sm">
                                        <p class="text-xs text-gray-500 mt-2">Current Homepage Image</p>
                                    </div>
                                @endif

                                {{-- File Input --}}
                                <input type="file" name="homepage_image" accept="image/jpeg,image/png,image/webp"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                <p class="mt-2 text-xs text-gray-500">
                                    Recommended: 1200x400px or wider. Max size: 2MB.
                                </p>

                                {{-- Header Background Toggle (OJS 3.3 Behavior) --}}
                                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm text-blue-800 mb-3">
                                        <i class="fa-solid fa-info-circle mr-1"></i>
                                        When a homepage image has been uploaded, you can choose to display it in the
                                        background of the header instead of its usual position on the homepage.
                                    </p>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="show_homepage_image_in_header" value="1"
                                            {{ $journal->show_homepage_image_in_header ? 'checked' : '' }}
                                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-900">
                                            Show the homepage image as the header background
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Page Footer (Rich Text) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Page Footer</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Custom HTML content that will appear at the bottom of every page.
                            Use this for copyright notices, contact info, or additional links.
                        </p>

                        {{-- CKEditor/Rich Text Area --}}
                        <textarea name="page_footer" id="page_footer" rows="6"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                            placeholder="<p>© 2024 Your Journal Name. All rights reserved.</p>">{{ $journal->page_footer }}</textarea>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fa-solid fa-code mr-1"></i>
                            HTML is allowed. Common tags: &lt;p&gt;, &lt;a&gt;, &lt;strong&gt;, &lt;br&gt;
                        </p>
                    </div>

                    {{-- Additional Content (Homepage Bottom) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Additional Content</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Any content entered here will appear on your homepage after the latest issue content.
                            Use this to display "Indexed By" logos, sponsors, or partner institutions.
                        </p>

                        <textarea name="additional_content" id="additional_content" rows="6"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                            placeholder="<p><strong>Indexed by:</strong> Google Scholar, Crossref, Dimensions...</p>">{{ $journal->additional_content }}</textarea>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fa-solid fa-code mr-1"></i>
                            HTML and images are allowed. This content appears centered at the bottom of your homepage.
                        </p>
                    </div>

                    {{-- Announcements --}}
                    <div x-data="{ enabled: {{ $journal->enable_announcements ? 'true' : 'false' }}, onHomepage: {{ $journal->show_announcements_on_homepage ? 'true' : 'false' }} }"
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Announcements</h3>
                            <p class="text-sm text-gray-500">Manage news and events for your readers.</p>
                        </div>

                        <div class="space-y-6">
                            {{-- Enable Toggle --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="enable_announcements" value="0">
                                    <input id="enable_announcements" name="enable_announcements" type="checkbox"
                                        value="1" x-model="enabled"
                                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                        {{ old('enable_announcements', $journal->enable_announcements) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_announcements" class="font-medium text-gray-700">Enable
                                        announcements</label>
                                    <p class="text-gray-500">Announcements may be published to inform readers of
                                        journal news and events.</p>
                                </div>
                            </div>

                            {{-- Settings Group (Visible if Enabled) --}}
                            <div x-show="enabled" x-transition
                                class="space-y-6 pl-6 border-l-2 border-gray-100 ml-2.5">
                                {{-- Introduction --}}
                                <div>
                                    <label for="announcements_introduction"
                                        class="block text-sm font-medium text-gray-700 mb-1">Introduction</label>
                                    <textarea name="announcements_introduction" id="announcements_introduction" rows="3"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('announcements_introduction', $journal->announcements_introduction) }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">This text will appear at the top of the
                                        Announcements page.</p>
                                </div>

                                <div class="border-t border-gray-100 pt-4">
                                    {{-- Homepage Toggle --}}
                                    <div class="flex items-start mb-4">
                                        <div class="flex items-center h-5">
                                            <input type="hidden" name="show_announcements_on_homepage"
                                                value="0">
                                            <input id="show_announcements_on_homepage"
                                                name="show_announcements_on_homepage" type="checkbox" value="1"
                                                x-model="onHomepage"
                                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                {{ old('show_announcements_on_homepage', $journal->show_announcements_on_homepage) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="show_announcements_on_homepage"
                                                class="font-medium text-gray-700">Display on Homepage</label>
                                        </div>
                                    </div>

                                    {{-- Number Input --}}
                                    <div x-show="onHomepage" class="ml-8">
                                        <label for="num_announcements_homepage"
                                            class="block text-sm font-medium text-gray-700 mb-1">How many announcements
                                            to display?</label>
                                        <input type="number" id="num_announcements_homepage"
                                            name="num_announcements_homepage" min="1" max="10"
                                            class="block w-24 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            value="{{ old('num_announcements_homepage', $journal->num_announcements_homepage) }}">
                                        <p class="mt-1 text-xs text-gray-500">Leave empty to display none.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-tab: ADVANCED (Old Appearance Content) --}}
                <div x-show="appearanceTab === 'advanced'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Favicon Upload --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                        <div class="flex items-start gap-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Favicon</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload a favicon to be displayed in the browser tab.
                                </p>

                                {{-- Current Favicon Preview --}}
                                @if ($journal->favicon_path)
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200 inline-block">
                                        <img src="{{ Storage::url($journal->favicon_path) }}" alt="Current Favicon"
                                            class="h-8 w-8">
                                        <p class="text-xs text-gray-500 mt-2">Current Favicon</p>
                                        <button type="button"
                                            onclick="if(confirm('Delete favicon?')) { 
                                                fetch('{{ route('journal.settings.website.favicon.delete', $journalSlug) }}', {
                                                    method: 'DELETE',
                                                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'}
                                                }).then(() => location.reload());
                                            }"
                                            class="text-red-600 text-xs mt-1 hover:underline">Remove</button>
                                    </div>
                                @endif

                                {{-- File Input --}}
                                <div class="relative">
                                    <input type="file" name="favicon" accept=".ico,.png,.jpg,.svg,.webp"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Recommended: ICO, PNG or SVG. Max size: 1MB.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Theme Colors</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Primary Color --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="primary_color" id="primary_color"
                                        value="{{ $settings['primary_color'] ?? '#4F46E5' }}"
                                        class="w-14 h-12 rounded-lg border border-gray-300 cursor-pointer p-1">
                                    <input type="text" id="primary_color_text"
                                        value="{{ $settings['primary_color'] ?? '#4F46E5' }}"
                                        class="flex-1 rounded-lg border-gray-300 text-sm font-mono" readonly>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Navigation bar, buttons, links</p>
                            </div>

                            {{-- Secondary Color --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="secondary_color" id="secondary_color"
                                        value="{{ $settings['secondary_color'] ?? '#7C3AED' }}"
                                        class="w-14 h-12 rounded-lg border border-gray-300 cursor-pointer p-1">
                                    <input type="text" id="secondary_color_text"
                                        value="{{ $settings['secondary_color'] ?? '#7C3AED' }}"
                                        class="flex-1 rounded-lg border-gray-300 text-sm font-mono" readonly>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Accents, gradients, highlights</p>
                            </div>
                        </div>

                        {{-- Color Preview --}}
                        <div class="mt-6 p-4 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-500 mb-3">Preview:</p>
                            <div class="flex gap-4">
                                <div class="h-12 w-32 rounded-lg flex items-center justify-center text-white text-sm font-medium"
                                    id="primary_preview"
                                    style="background-color: {{ $settings['primary_color'] ?? '#4F46E5' }};">
                                    Primary
                                </div>
                                <div class="h-12 w-32 rounded-lg flex items-center justify-center text-white text-sm font-medium"
                                    id="secondary_preview"
                                    style="background-color: {{ $settings['secondary_color'] ?? '#7C3AED' }};">
                                    Secondary
                                </div>
                                <div class="h-12 flex-1 rounded-lg flex items-center justify-center text-white text-sm font-medium"
                                    id="gradient_preview"
                                    style="background: linear-gradient(135deg, {{ $settings['primary_color'] ?? '#4F46E5' }}, {{ $settings['secondary_color'] ?? '#7C3AED' }});">
                                    Gradient Preview
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-tab: MENU NAV --}}
                <div x-show="appearanceTab === 'menu_nav'" x-cloak class="grid grid-cols-1 gap-6">
                    {{-- NAVIGATION & SIDEBAR SETTINGS --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Navigation & Layout</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Configure your journal's navigation menus and sidebar content.
                        </p>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {{-- Navigation Manager Card --}}
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <div
                                    class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-bars text-slate-600"></i>
                                        <span class="font-medium text-slate-800">Navigation</span>
                                    </div>
                                    <a href="{{ route('journal.settings.navigation.index', $journalSlug) }}"
                                        class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Manage
                                    </a>
                                </div>
                                <div class="p-4">
                                    <p class="text-sm text-slate-600 mb-3">Configure primary navigation menus and
                                        links.</p>
                                    <ul class="text-xs text-slate-500 space-y-1 mb-3">
                                        <li>• Create custom menu items</li>
                                        <li>• Assign to header/user navigation</li>
                                        <li>• Reorder menu items</li>
                                    </ul>
                                    <a href="{{ route('journal.settings.navigation.index', $journalSlug) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-xs font-medium rounded text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fa-solid fa-bars mr-1.5"></i>
                                        Navigation Manager
                                    </a>
                                </div>
                            </div>

                            {{-- Sidebar Manager Card --}}
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <div
                                    class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-columns text-slate-600"></i>
                                        <span class="font-medium text-slate-800">Sidebar</span>
                                    </div>
                                    <a href="{{ route('journal.settings.sidebar.index', $journalSlug) }}"
                                        class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Manage
                                    </a>
                                </div>
                                <div class="p-4">
                                    <p class="text-sm text-slate-600 mb-3">Configure sidebar content and layout.</p>
                                    <ul class="text-xs text-slate-500 space-y-1 mb-3">
                                        <li>• Add custom content blocks</li>
                                        <li>• Configure widgets and modules</li>
                                        <li>• Reorder sidebar elements</li>
                                    </ul>
                                    <a href="{{ route('journal.settings.sidebar.index', $journalSlug) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-xs font-medium rounded text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fa-solid fa-columns mr-1.5"></i>
                                        Sidebar Manager
                                    </a>
                                </div>
                            </div>

                            {{-- DOI Settings Card --}}
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <div
                                    class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-fingerprint text-slate-600"></i>
                                        <span class="font-medium text-slate-800">DOI</span>
                                    </div>
                                    <a href="{{ route('journal.settings.doi.edit', $journalSlug) }}"
                                        class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Manage
                                    </a>
                                </div>
                                <div class="p-4">
                                    <p class="text-sm text-slate-600 mb-3">Configure Digital Object Identifiers (DOI).
                                    </p>
                                    <ul class="text-xs text-slate-500 space-y-1 mb-3">
                                        <li>• Set DOI prefix</li>
                                        <li>• Configure suffix patterns</li>
                                        <li>• Assign DOIs to content</li>
                                    </ul>
                                    <a href="{{ route('journal.settings.doi.edit', $journalSlug) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-xs font-medium rounded text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fa-solid fa-fingerprint mr-1.5"></i>
                                        DOI Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- Tab: SECTIONS (Visibility Toggles) --}}
            {{-- ============================================ --}}
            <div x-show="activeTab === 'sections'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Homepage Sections</h3>
                    <p class="text-sm text-gray-500 mb-6">Control which sections appear on your journal homepage.</p>

                    <div class="space-y-4">
                        <label
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <div>
                                <span class="font-medium text-gray-900">Show Announcements</span>
                                <p class="text-sm text-gray-500">Display the latest announcements section</p>
                            </div>
                            <input type="checkbox" name="show_announcements" value="1"
                                {{ !empty($settings['show_announcements']) ? 'checked' : '' }}
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </label>

                        <label
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <div>
                                <span class="font-medium text-gray-900">Show Editorial Team</span>
                                <p class="text-sm text-gray-500">Display editors with their roles on homepage</p>
                            </div>
                            <input type="checkbox" name="show_editorial_team" value="1"
                                {{ !empty($settings['show_editorial_team']) ? 'checked' : '' }}
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </label>

                        <label
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <div>
                                <span class="font-medium text-gray-900">Show Indexed In</span>
                                <p class="text-sm text-gray-500">Display indexing partner logos</p>
                            </div>
                            <input type="checkbox" name="show_indexed_in" value="1"
                                {{ !empty($settings['show_indexed_in']) ? 'checked' : '' }}
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </label>
                    </div>
                </div>

                {{-- Indexed In Logos --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Indexing Databases</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Upload logos of indexing databases where your journal is listed (Scopus, Google Scholar, DOAJ,
                        etc.)
                    </p>

                    @php
                        $val = $settings['indexed_in_images'] ?? [];
                        $indexedImages = is_array($val) ? $val : json_decode($val, true) ?? [];
                    @endphp

                    @if (count($indexedImages) > 0)
                        <div class="flex flex-wrap gap-4 mb-4">
                            @foreach ($indexedImages as $image)
                                <div class="relative group">
                                    <img src="{{ Storage::url($image) }}" alt="Indexer"
                                        class="h-16 w-auto object-contain bg-gray-50 rounded-lg border border-gray-200 p-2">
                                    <button type="button"
                                        onclick="if(confirm('Remove this logo?')) { 
                                            fetch('{{ route('journal.settings.website.indexed-image.delete', $journalSlug) }}', {
                                                method: 'DELETE',
                                                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'},
                                                body: JSON.stringify({path: '{{ $image }}'})
                                            }).then(() => location.reload());
                                        }"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <i class="fa-solid fa-times text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <input type="file" name="indexed_in_images[]" accept="image/*" multiple
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-2 text-xs text-gray-500">
                        You can select multiple files. Recommended: PNG with transparent background.
                    </p>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- Tab: INFORMATION --}}
            {{-- ============================================ --}}
            <div x-show="activeTab === 'information'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Information for Readers --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">For Readers</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Information for readers will be displayed on the journal's website.
                    </p>
                    <textarea name="info_readers" id="info_readers" rows="6"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ $journal->info_readers }}</textarea>
                </div>

                {{-- Information for Authors --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">For Authors</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Information for authors will be displayed on the journal's website.
                    </p>
                    <textarea name="info_authors" id="info_authors" rows="6"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ $journal->info_authors }}</textarea>
                </div>

                {{-- Information for Librarians --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">For Librarians</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Information for librarians will be displayed on the journal's website.
                    </p>
                    <textarea name="info_librarians" id="info_librarians" rows="6"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ $journal->info_librarians }}</textarea>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- Tab: SECURITY --}}
            {{-- ============================================ --}}
            <div x-show="activeTab === 'security'" x-cloak class="space-y-6">
                {{-- reCAPTCHA Config --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-robot text-indigo-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Google reCAPTCHA v2 (Checkbox)</h3>
                            <p class="text-sm text-gray-500">Protect your login and register pages from bots.</p>
                        </div>
                    </div>

                    @if ($siteSetting && $siteSetting->recaptcha_site_key && $siteSetting->recaptcha_secret_key)
                        {{-- Enable/Disable Checkbox --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <div class="relative flex items-start">
                                <div class="flex h-5 items-center">
                                    <input type="hidden" name="is_recaptcha_enabled" value="0">
                                    <input id="is_recaptcha_enabled" name="is_recaptcha_enabled" type="checkbox"
                                        value="1"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        {{ old('is_recaptcha_enabled', $journal->is_recaptcha_enabled ?? false) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_recaptcha_enabled" class="font-medium text-gray-700">Enable
                                        reCAPTCHA</label>
                                    <p class="text-gray-500">Show the "I'm not a robot" checkbox on login forms.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm text-blue-700">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                You are using global reCAPTCHA keys configured in Site Settings.
                            </p>
                        </div>
                    @else
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-md flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                            <div>
                                <h4 class="text-sm font-medium text-amber-800">Global Configuration Missing</h4>
                                <p class="text-sm text-amber-700 mt-1">
                                    reCAPTCHA cannot be enabled because the global API keys have not been configured by
                                    the site administrator.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fa-solid fa-save mr-2"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Color Picker JavaScript --}}
    @push('scripts')
        <script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sync color pickers with text inputs
                const primaryColor = document.getElementById('primary_color');
                const primaryText = document.getElementById('primary_color_text');
                const primaryPreview = document.getElementById('primary_preview');

                const secondaryColor = document.getElementById('secondary_color');
                const secondaryText = document.getElementById('secondary_color_text');
                const secondaryPreview = document.getElementById('secondary_preview');

                const gradientPreview = document.getElementById('gradient_preview');

                function updatePreviews() {
                    if (primaryPreview) primaryPreview.style.backgroundColor = primaryColor.value;
                    if (primaryText) primaryText.value = primaryColor.value;
                    if (secondaryPreview) secondaryPreview.style.backgroundColor = secondaryColor.value;
                    if (secondaryText) secondaryText.value = secondaryColor.value;
                    if (gradientPreview) {
                        gradientPreview.style.background =
                            `linear-gradient(135deg, ${primaryColor.value}, ${secondaryColor.value})`;
                    }
                }

                if (primaryColor) primaryColor.addEventListener('input', updatePreviews);
                if (secondaryColor) secondaryColor.addEventListener('input', updatePreviews);
            });
        </script>
        <script>
            tinymce.init({
                selector: '#page_footer, #info_readers, #info_authors, #info_librarians, #announcements_introduction, #additional_content',
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
</x-app-layout>
