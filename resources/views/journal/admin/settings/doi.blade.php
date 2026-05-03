@extends('layouts.app')

@section('title', 'DOI Settings')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Page Header --}}
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-sm mb-4">
            <a href="{{ route('journal.settings.index', ['journal' => $journal->slug]) }}" 
               class="text-gray-500 hover:text-indigo-600 transition">
                <i class="fa-solid fa-gear mr-1"></i>Settings
            </a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('journal.settings.website.edit', ['journal' => $journal->slug]) }}" 
               class="text-gray-500 hover:text-indigo-600 transition">Website</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">DOI</span>
        </nav>
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">DOI Plugin Settings</h1>
                <p class="text-gray-500 mt-1">Configure Digital Object Identifiers (DOI) for this journal.</p>
            </div>
            <a href="https://www.doi.org/" target="_blank" 
               class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <i class="fa-solid fa-external-link"></i>
                Learn about DOI
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-start gap-3" 
             x-data="{ show: true }" x-show="show" x-transition>
            <i class="fa-solid fa-check-circle text-emerald-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm text-emerald-800">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3"
             x-data="{ show: true }" x-show="show" x-transition>
            <i class="fa-solid fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3"
             x-data="{ show: true }" x-show="show" x-transition>
            <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm text-amber-800">{{ session('warning') }}</p>
            </div>
            <button @click="show = false" class="text-amber-600 hover:text-amber-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- DOI Status Banner --}}
    @if($journal->doi_enabled)
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-emerald-800">DOI is Active</h4>
                <p class="text-xs text-emerald-600">Prefix: <code class="bg-emerald-100 px-1.5 py-0.5 rounded">{{ $journal->doi_prefix }}</code></p>
            </div>
        </div>
    @else
        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-circle-xmark text-gray-400 text-lg"></i>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700">DOI is Not Configured</h4>
                <p class="text-xs text-gray-500">Configure a DOI prefix and select content types below to enable DOIs.</p>
            </div>
        </div>
    @endif

    {{-- Main Settings Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('journal.settings.doi.update', ['journal' => $journal->slug]) }}" 
              method="POST" 
              x-data="doiSettingsForm()" 
              class="divide-y divide-gray-100">
            @csrf
            @method('PUT')

            {{-- Section 1: Journal Content --}}
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-newspaper text-indigo-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Journal Content</h3>
                        <p class="text-sm text-gray-500 mt-1">Select which content types should have DOIs assigned.</p>
                        
                        <div class="mt-4 space-y-3">
                            @php
                                $doiObjects = old('doi_objects', $journal->doi_objects ?? []);
                                $contentTypes = [
                                    'issues' => ['label' => 'Issues', 'icon' => 'fa-calendar-alt', 'desc' => 'Assign DOIs to journal issues'],
                                    'articles' => ['label' => 'Articles', 'icon' => 'fa-file-alt', 'desc' => 'Assign DOIs to published articles'],
                                    'galleys' => ['label' => 'Galleys', 'icon' => 'fa-file-pdf', 'desc' => 'Assign DOIs to publication files (PDF, HTML, etc.)'],
                                ];
                            @endphp

                            @foreach($contentTypes as $key => $type)
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition {{ in_array($key, $doiObjects) ? 'bg-indigo-50 border-indigo-300' : '' }}">
                                    <input type="checkbox" 
                                           name="doi_objects[]" 
                                           value="{{ $key }}"
                                           class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                           {{ in_array($key, $doiObjects) ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid {{ $type['icon'] }} text-gray-400 w-4"></i>
                                            <span class="font-medium text-gray-900">{{ $type['label'] }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $type['desc'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: DOI Prefix --}}
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-hashtag text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">
                            DOI Prefix 
                            <span class="text-red-500">*</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Enter your DOI prefix assigned by your DOI registration agency (e.g., CrossRef, DataCite).
                            The prefix must start with <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">10.</code>
                        </p>
                        
                        <div class="mt-4 max-w-md">
                            <div class="relative">
                                <input type="text" 
                                       name="doi_prefix" 
                                       id="doi_prefix"
                                       x-model="doiPrefix"
                                       value="{{ old('doi_prefix', $journal->doi_prefix) }}"
                                       placeholder="10.12345"
                                       class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('doi_prefix') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fa-solid fa-fingerprint"></i>
                                </span>
                            </div>
                            @error('doi_prefix')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Example: <code class="bg-gray-100 px-1 rounded">10.12345</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: DOI Suffix --}}
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-code text-amber-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">DOI Suffix</h3>
                        <p class="text-sm text-gray-500 mt-1">Choose how DOI suffixes are generated for your content.</p>
                        
                        @php
                            $currentSuffixType = old('doi_suffix_type', $journal->doi_suffix_type ?? 'default');
                        @endphp

                        <div class="mt-4 space-y-3">
                            {{-- Default Pattern --}}
                            <label class="block p-4 rounded-lg border cursor-pointer transition"
                                   :class="suffixType === 'default' ? 'bg-indigo-50 border-indigo-300' : 'border-gray-200 hover:bg-gray-50'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" 
                                           name="doi_suffix_type" 
                                           value="default"
                                           x-model="suffixType"
                                           class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                           {{ $currentSuffixType === 'default' ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-900 block">Use default patterns</span>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Format: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">%j.v%vi%i.%a</code>
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Example: <span class="font-mono" x-text="doiPrefix + '/' + '{{ $journal->path }}.v1i1.100'"></span>
                                        </p>
                                    </div>
                                </div>
                            </label>

                            {{-- Manual Entry --}}
                            <label class="block p-4 rounded-lg border cursor-pointer transition"
                                   :class="suffixType === 'manual' ? 'bg-indigo-50 border-indigo-300' : 'border-gray-200 hover:bg-gray-50'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" 
                                           name="doi_suffix_type" 
                                           value="manual"
                                           x-model="suffixType"
                                           class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                           {{ $currentSuffixType === 'manual' ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-900 block">Enter individual DOI suffixes</span>
                                        <p class="text-sm text-gray-500 mt-1">
                                            You will manually enter a unique suffix for each article on its metadata page.
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Best for journals with existing DOI conventions or specific naming requirements.
                                        </p>
                                    </div>
                                </div>
                            </label>

                            {{-- Custom Pattern --}}
                            <div class="p-4 rounded-lg border transition"
                                 :class="suffixType === 'custom_pattern' ? 'bg-indigo-50 border-indigo-300' : 'border-gray-200 hover:bg-gray-50'">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="radio" 
                                           name="doi_suffix_type" 
                                           value="custom_pattern"
                                           x-model="suffixType"
                                           class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                           {{ $currentSuffixType === 'custom_pattern' ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-900 block">Use custom pattern</span>
                                        <p class="text-sm text-gray-500 mt-1">Define your own pattern using placeholders.</p>
                                    </div>
                                </label>
                                
                                {{-- Custom Pattern Input --}}
                                <div x-show="suffixType === 'custom_pattern'" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-4 ml-6">
                                    <label for="doi_custom_pattern" class="block text-sm font-medium text-gray-700 mb-1">
                                        Custom Pattern <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="doi_custom_pattern" 
                                           id="doi_custom_pattern"
                                           x-model="customPattern"
                                           value="{{ old('doi_custom_pattern', $journal->doi_custom_pattern ?? '%j.v%vi%i.%a') }}"
                                           placeholder="%j.v%vi%i.%a"
                                           class="w-full max-w-md px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm {{ $errors->has('doi_custom_pattern') ? 'border-red-300' : 'border-gray-300' }}">
                                    @error('doi_custom_pattern')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    
                                    {{-- Pattern Placeholders Guide --}}
                                    <div class="mt-3 p-3 bg-white rounded-lg border border-gray-100">
                                        <p class="text-xs font-semibold text-gray-600 mb-2">Available Placeholders:</p>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                                            <div class="flex items-center gap-2">
                                                <code class="bg-gray-100 px-1.5 py-0.5 rounded">%j</code>
                                                <span class="text-gray-600">Journal path</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <code class="bg-gray-100 px-1.5 py-0.5 rounded">%v</code>
                                                <span class="text-gray-600">Volume</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <code class="bg-gray-100 px-1.5 py-0.5 rounded">%i</code>
                                                <span class="text-gray-600">Issue number</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <code class="bg-gray-100 px-1.5 py-0.5 rounded">%Y</code>
                                                <span class="text-gray-600">Year</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <code class="bg-gray-100 px-1.5 py-0.5 rounded">%a</code>
                                                <span class="text-gray-600">Article ID</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Live Preview --}}
                                    <div class="mt-3 p-3 bg-indigo-50 rounded-lg">
                                        <p class="text-xs font-semibold text-indigo-600 mb-1">Preview:</p>
                                        <code class="text-sm font-mono text-indigo-800" x-text="generatePreview()"></code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="p-6 bg-gray-50 flex items-center justify-end gap-3">
                <a href="{{ route('journal.settings.website.edit', ['journal' => $journal->slug]) }}" 
                   class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition text-sm font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition text-sm font-medium flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Reassign DOIs Section --}}
    <div class="mt-8 bg-amber-50 border border-amber-200 rounded-xl overflow-hidden">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-rotate text-amber-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-amber-900">Reassign DOIs</h3>
                    <p class="text-sm text-amber-700 mt-1">
                        Changing settings above <strong>does not affect existing DOIs</strong>. 
                        Use this action to clear and regenerate all DOIs for published articles based on current settings.
                    </p>
                    
                    <div class="mt-4 p-4 bg-white/60 rounded-lg border border-amber-200">
                        <div class="flex items-start gap-2 text-sm text-amber-800">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <div>
                                <p class="font-semibold">Warning:</p>
                                <ul class="mt-1 list-disc list-inside text-xs space-y-1">
                                    <li>This will overwrite ALL existing DOIs for published articles</li>
                                    <li>Once DOIs are registered externally, they should not be changed</li>
                                    <li>Only use this for initial setup or before external registration</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('journal.settings.doi.reassign', ['journal' => $journal->slug]) }}" 
                          method="POST" 
                          class="mt-4"
                          onsubmit="return confirm('WARNING: This will overwrite ALL existing DOIs for published articles in this journal.\n\nThis action cannot be undone.\n\nAre you sure you want to continue?');">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2 text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition text-sm font-medium"
                                {{ !$journal->doi_enabled ? 'disabled' : '' }}>
                            <i class="fa-solid fa-arrows-rotate"></i>
                            Reassign DOIs
                        </button>
                        @if(!$journal->doi_enabled)
                            <p class="mt-2 text-xs text-amber-600">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Save valid DOI settings first to enable this feature.
                            </p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DOI Statistics (if enabled) --}}
    @if($journal->doi_enabled)
        @php
            // Get submission IDs for this journal
            $submissionIds = \App\Models\Submission::where('journal_id', $journal->id)
                ->where('status', 'published')
                ->pluck('id');
            
            // Count publications with and without DOIs
            $totalPublished = \App\Models\Publication::whereIn('submission_id', $submissionIds)
                ->where('status', \App\Models\Publication::STATUS_PUBLISHED)
                ->count();
            
            $withDoi = \App\Models\Publication::whereIn('submission_id', $submissionIds)
                ->where('status', \App\Models\Publication::STATUS_PUBLISHED)
                ->whereNotNull('doi')
                ->where('doi', '!=', '')
                ->count();
            
            $withoutDoi = $totalPublished - $withDoi;
        @endphp
        <div class="mt-8 bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">DOI Statistics</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <p class="text-3xl font-bold text-gray-900">{{ $totalPublished }}</p>
                        <p class="text-sm text-gray-500 mt-1">Total Published</p>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-lg text-center">
                        <p class="text-3xl font-bold text-emerald-600">{{ $withDoi }}</p>
                        <p class="text-sm text-emerald-600 mt-1">With DOI</p>
                    </div>
                    <div class="p-4 bg-amber-50 rounded-lg text-center">
                        <p class="text-3xl font-bold text-amber-600">{{ $withoutDoi }}</p>
                        <p class="text-sm text-amber-600 mt-1">Without DOI</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function doiSettingsForm() {
    return {
        doiPrefix: '{{ old('doi_prefix', $journal->doi_prefix ?? '10.') }}',
        suffixType: '{{ old('doi_suffix_type', $journal->doi_suffix_type ?? 'default') }}',
        customPattern: '{{ old('doi_custom_pattern', $journal->doi_custom_pattern ?? '%j.v%vi%i.%a') }}',
        journalPath: '{{ $journal->path }}',

        generatePreview() {
            if (!this.doiPrefix || !this.customPattern) {
                return 'Configure prefix and pattern to see preview';
            }

            let suffix = this.customPattern
                .replace(/%j/g, this.journalPath)
                .replace(/%v/g, '1')
                .replace(/%i/g, '1')
                .replace(/%Y/g, new Date().getFullYear())
                .replace(/%a/g, '100');

            return this.doiPrefix + '/' + suffix;
        }
    }
}
</script>
@endpush
