@extends('layouts.app')

@section('title', 'Distribution Settings - ' . ($journal->abbreviation ?? 'IAMJOS'))

@section('content')
    <div x-data="{
        activeTab: 'license'
    }">

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <i class="fa-solid fa-check-circle text-emerald-600"></i>
                <span class="text-sm text-emerald-800">{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-600 hover:text-emerald-800">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Distribution Settings</h1>
                <p class="mt-1 text-sm text-gray-500">Manage licensing, indexing, and access policies.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="/{{ $journal->slug ?? '#' }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View Journal Site
                </a>
            </div>
        </div>

        <!-- Main Settings Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex overflow-x-auto" aria-label="Tabs">
                    <button @click="activeTab = 'license'"
                        :class="activeTab === 'license' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-scale-balanced mr-2"></i>
                        License
                    </button>
                    <button @click="activeTab = 'indexing'"
                        :class="activeTab === 'indexing' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-magnifying-glass mr-2"></i>
                        Search Indexing
                    </button>
                    <button @click="activeTab = 'access'"
                        :class="activeTab === 'access' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-lock-open mr-2"></i>
                        Access
                    </button>
                    <button @click="activeTab = 'archiving'"
                        :class="activeTab === 'archiving' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-box-archive mr-2"></i>
                        Archiving
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <form action="{{ route('journal.settings.distribution.update', ['journal' => $journal->slug]) }}" method="POST"
                class="p-6 lg:p-8 relative">
                @csrf
                @method('PUT')

                <!-- TAB 1: LICENSE -->
                <div x-show="activeTab === 'license'" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-scale-balanced text-indigo-600"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">License Settings</h3>
                            <p class="text-sm text-gray-500">Configure copyright and licensing terms for submissions.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 max-w-4xl" x-data="{
                        copyrightType: '{{ old('license.copyright_holder_type', $journal->copyright_holder_type ?? 'author') }}',
                        licenseUrl: '{{ old('license.url', $journal->license_url) }}',
                        customLicenseUrl: '{{ old('license.url', $journal->license_url) }}',
                        licensePresets: [
                            { url: 'https://creativecommons.org/licenses/by-nc-nd/4.0', name: 'CC Attribution-NonCommercial-NoDerivatives 4.0' },
                            { url: 'https://creativecommons.org/licenses/by-nc/4.0', name: 'CC Attribution-NonCommercial 4.0' },
                            { url: 'https://creativecommons.org/licenses/by-nc-sa/4.0', name: 'CC Attribution-NonCommercial-ShareAlike 4.0' },
                            { url: 'https://creativecommons.org/licenses/by-nd/4.0', name: 'CC Attribution-NoDerivatives 4.0' },
                            { url: 'https://creativecommons.org/licenses/by/4.0', name: 'CC Attribution 4.0' },
                            { url: 'https://creativecommons.org/licenses/by-sa/4.0', name: 'CC Attribution-ShareAlike 4.0' }
                        ],
                        get selectedLicense() {
                            if (this.licensePresets.some(p => p.url === this.customLicenseUrl)) {
                                return this.customLicenseUrl;
                            }
                            return this.customLicenseUrl ? 'other' : '';
                        },
                        setLicense(url) {
                            if (url === 'other') {
                                this.licenseUrl = 'other';
                                // Keep existing custom url or clear? Usually keep if switching back and forth
                            } else {
                                this.licenseUrl = url;
                                this.customLicenseUrl = url;
                            }
                        }
                    }">

                        <!-- Copyright Holder -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Copyright Holder</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="license[copyright_holder_type]" value="author"
                                        x-model="copyrightType"
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Author</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="license[copyright_holder_type]" value="context"
                                        x-model="copyrightType"
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Journal</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="license[copyright_holder_type]" value="other"
                                        x-model="copyrightType"
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Custom</span>
                                </label>
                            </div>

                            <!-- Custom Copyright Input -->
                            <div x-show="copyrightType === 'other'" x-collapse class="mt-3 pl-6">
                                <input type="text" name="license[copyright_holder_other]"
                                    value="{{ old('license.copyright_holder_other', $journal->copyright_holder_other) }}"
                                    class="w-full sm:w-1/2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Enter copyright holder name">
                            </div>
                        </div>

                        <!-- License -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">License</label>
                            <!-- Hidden input actually submits the URL -->
                            <input type="hidden" name="license[url]" x-model="customLicenseUrl">

                            <div class="space-y-3">
                                <template x-for="preset in licensePresets" :key="preset.url">
                                    <label class="flex items-center">
                                        <input type="radio" name="license_selector" :value="preset.url"
                                            :checked="customLicenseUrl === preset.url" @change="setLicense(preset.url)"
                                            class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700" x-text="preset.name"></span>
                                        <a :href="preset.url" target="_blank"
                                            class="ml-2 text-xs text-indigo-500 hover:text-indigo-700">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </label>
                                </template>

                                <label class="flex items-center">
                                    <input type="radio" name="license_selector" value="other"
                                        :checked="selectedLicense === 'other'" @change="setLicense('other')"
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Other license URL</span>
                                </label>
                            </div>

                            <!-- Custom License Input -->
                            <div x-show="selectedLicense === 'other'" x-collapse class="mt-3 pl-6">
                                <label class="block text-xs text-gray-500 mb-1">License URL</label>
                                <input type="url" x-model="customLicenseUrl"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="https://example.com/license">
                            </div>
                        </div>

                        <!-- License Terms -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">License Terms</label>
                            <textarea name="license[terms]" rows="6"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter the license terms that will be displayed with published content...">{{ old('license.terms', $journal->license_terms) }}</textarea>
                        </div>

                        <!-- Copyright Year Basis -->
                        <div>
                            <span class="block text-sm font-medium text-gray-700 mb-2">Copyright Year Basis</span>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="license[copyright_year]" value="issue"
                                        {{ old('license.copyright_year', $journal->copyright_year_basis) === 'issue' ? 'checked' : '' }}
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Use Issue publication date</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="license[copyright_year]" value="article"
                                        {{ old('license.copyright_year', $journal->copyright_year_basis) === 'article' ? 'checked' : '' }}
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Use Article publication date</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: SEARCH INDEXING -->
                <div x-show="activeTab === 'indexing'" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-magnifying-glass text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Search Indexing</h3>
                            <p class="text-sm text-gray-500">Manage search engine optimization (SEO) and indexing settings.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 max-w-4xl">
                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Description</label>
                            <textarea name="indexing[description]" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="A brief description of the journal for search results...">{{ old('indexing.description', $journal->search_description) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">This text generally appears in search results below the
                                page title.</p>
                        </div>

                        <!-- Custom Tags -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Custom Header Tags</label>
                            <textarea name="indexing[custom_tags]" rows="4"
                                class="w-full font-mono text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="<meta name='google-site-verification' content='...' />">{{ old('indexing.custom_tags', $journal->custom_headers) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Add custom HTML meta tags for the site header (e.g. for
                                verification).</p>
                        </div>

                        <!-- Sitemap URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sitemap URL</label>
                            <div class="flex rounded-md shadow-sm">
                                <input type="text" readonly value="{{ url('sitemap.xml') }}"
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-lg border-gray-300 bg-gray-50 text-gray-500 sm:text-sm focus:border-gray-300 focus:ring-0">
                                <a href="{{ url('sitemap.xml') }}" target="_blank"
                                    class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i> Open
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: ACCESS -->
                <div x-show="activeTab === 'access'" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-lock-open text-emerald-600"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Access Settings</h3>
                            <p class="text-sm text-gray-500">Control who can access journal content and when.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 max-w-4xl">
                        <!-- Open Access Policy -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Open Access Policy</label>
                            <textarea name="access[open_access_policy]" rows="6"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="State the journal's policy regarding access to articles...">{{ old('access.open_access_policy', $journal->open_access_policy) }}</textarea>
                        </div>

                        <!-- Enable OAI -->
                        <div>
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Enable OAI-PMH</h4>
                                    <p class="text-sm text-gray-500">Allow metadata harvesting via Open Archives Initiative
                                        Protocol.</p>
                                </div>
                                <div x-data="{ enabled: {{ old('access.enable_oai', $journal->enable_oai) ? 'true' : 'false' }} }">
                                    <input type="hidden" name="access[enable_oai]" :value="enabled ? 1 : 0">
                                    <button type="button" @click="enabled = !enabled"
                                        :class="enabled ? 'bg-emerald-600' : 'bg-gray-200'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                        <span class="sr-only">Use setting</span>
                                        <span aria-hidden="true" :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: ARCHIVING -->
                <div x-show="activeTab === 'archiving'" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-box-archive text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Archiving</h3>
                            <p class="text-sm text-gray-500">Enable long-term preservation services (LOCKSS/CLOCKSS).</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 max-w-4xl">

                        <!-- Archiving Networks -->
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Archiving Networks</label>

                            <!-- LOCKSS -->
                            <div class="relative flex items-start">
                                <div class="flex h-5 items-center">
                                    <input type="checkbox" name="archiving[lockss]" value="1"
                                        {{ old('archiving.lockss', $journal->enable_lockss) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label class="font-medium text-gray-700">Enable LOCKSS</label>
                                    <p class="text-gray-500">Enable LOCKSS to store and distribute journal content at
                                        participating libraries via a LOCKSS Publisher Manifest page.</p>
                                </div>
                            </div>

                            <!-- CLOCKSS -->
                            <div class="relative flex items-start">
                                <div class="flex h-5 items-center">
                                    <input type="checkbox" name="archiving[clockss]" value="1"
                                        {{ old('archiving.clockss', $journal->enable_clockss) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label class="font-medium text-gray-700">Enable CLOCKSS</label>
                                    <p class="text-gray-500">Enable CLOCKSS to store and distribute journal content via a
                                        CLOCKSS Publisher Manifest page.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Archiving Policy -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Archiving Policy</label>
                            <textarea name="archiving[policy]" rows="6"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                placeholder="Describe the journal's digital archiving policy...">{{ old('archiving.policy', $journal->archiving_policy) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Sticky Save Button -->
                <div
                    class="mt-10 pt-6 border-t border-gray-200 flex justify-end sticky bottom-0 bg-white py-4 -mb-6 -mx-6 px-6 shadow-md z-10 rounded-b-xl">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg shadow-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-floppy-disk mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script>
        // Javascript helpers can be placed here if needed in the future
    </script>
@endpush
