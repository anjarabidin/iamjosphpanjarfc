@extends('layouts.admin')

@section('title', 'Edit Block: ' . $block->title)

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('admin.site.appearance.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Back to Page Builder
                </a>
                <h1 class="text-2xl font-bold text-gray-900 mt-4">Edit: {{ $block->title }}</h1>
                <p class="text-gray-500">{{ $block->description }}</p>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.site.appearance.update', $block) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Basic Settings --}}
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Basic Settings</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                <input type="text" name="title" value="{{ $block->title }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ $block->is_active ? 'checked' : '' }} id="is_active"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="is_active" class="text-sm text-gray-700">Active (show on portal)</label>
                            </div>
                        </div>
                    </div>

                    {{-- Block-Specific Configuration --}}
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Configuration</h2>

                        @switch($block->key)
                            {{-- Hero Search Block --}}
                            @case('hero_search')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Headline</label>
                                        <input type="text" name="config[headline]" value="{{ $block->getConfig('headline') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subheadline</label>
                                        <input type="text" name="config[subheadline]"
                                            value="{{ $block->getConfig('subheadline') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="config[show_popular_topics]" value="1"
                                            {{ $block->getConfig('show_popular_topics', true) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600">
                                        <label class="text-sm text-gray-700">Show Popular Topics</label>
                                    </div>

                                    <hr class="border-gray-100 my-4">

                                    {{-- Social Proof Settings --}}
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-3">Social Proof (Avatars & Stars)</h3>
                                        <div class="flex items-center gap-3 mb-4">
                                            <input type="checkbox" name="config[show_social_proof]" value="1"
                                                {{ $block->getConfig('show_social_proof', true) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600">
                                            <label class="text-sm text-gray-700">Show Social Proof Section</label>
                                        </div>

                                        {{-- Current Logos --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Institution Logos (Max
                                                4)</label>
                                            @php $logos = $block->getConfig('logos', []); @endphp

                                            @if (count($logos) > 0)
                                                <div class="grid grid-cols-4 gap-4 mb-4">
                                                    @foreach ($logos as $index => $logo)
                                                        <div class="relative group bg-gray-100 rounded-lg p-4">
                                                            <img src="{{ Storage::url($logo) }}" alt="Logo"
                                                                class="h-12 w-full object-contain">
                                                            <button type="button" onclick="deleteLogo('{{ $logo }}')"
                                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <i class="fa-solid fa-times text-xs"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 mb-4">No logos uploaded yet.</p>
                                            @endif
                                        </div>

                                        {{-- Upload New Logos --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fa-solid fa-upload mr-2"></i>
                                                Upload Logos
                                            </label>
                                            <div
                                                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                                <input type="file" name="logos[]" multiple accept="image/*"
                                                    id="social-proof-logo-upload" class="hidden" onchange="previewLogos(this)">
                                                <label for="social-proof-logo-upload" class="cursor-pointer">
                                                    <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                                    <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                                                    <p class="text-xs text-gray-400">PNG, JPG, SVG (Max 2MB)</p>
                                                </label>
                                            </div>
                                            <!-- We can reuse the same preview container id since only one case is active at a time, or give it a unique one if ensuring distinctness. Given the JS simply finds 'logo-preview', let's use a unique ID here and update the JS call or just rely on the fact that only one block type form is rendered. Wait, the JS function looks for 'logo-preview' by ID. So I should use id="logo-preview" here too. -->
                                            <div id="logo-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                                        </div>
                                    </div>
                                </div>
                            @break

                            {{-- Featured Journals Block --}}
                            @case('featured_journals')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                        <input type="text" name="config[title]"
                                            value="{{ $block->getConfig('title', 'Featured Journals') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                                        <input type="text" name="config[subtitle]" value="{{ $block->getConfig('subtitle') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Layout</label>
                                            <select name="config[layout]" class="w-full rounded-lg border-gray-300">
                                                <option value="grid"
                                                    {{ $block->getConfig('layout') === 'grid' ? 'selected' : '' }}>Grid</option>
                                                <option value="carousel"
                                                    {{ $block->getConfig('layout') === 'carousel' ? 'selected' : '' }}>Carousel
                                                </option>
                                                <option value="list"
                                                    {{ $block->getConfig('layout') === 'list' ? 'selected' : '' }}>List</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Columns</label>
                                            <select name="config[columns]" class="w-full rounded-lg border-gray-300">
                                                @for ($i = 2; $i <= 6; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ $block->getConfig('columns', 4) == $i ? 'selected' : '' }}>
                                                        {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Journals to Show</label>
                                        <input type="number" name="config[limit]" value="{{ $block->getConfig('limit', 8) }}"
                                            min="1" max="20" class="w-full rounded-lg border-gray-300">
                                    </div>
                                </div>
                            @break

                            {{-- Indexing Partners Block --}}
                            @case('indexing_partners')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                        <input type="text" name="config[title]"
                                            value="{{ $block->getConfig('title', 'Indexed by Major Databases') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Layout Mode</label>
                                        <select name="config[layout]" class="w-full rounded-lg border-gray-300">
                                            <option value="auto"
                                                {{ $block->getConfig('layout', 'auto') === 'auto' ? 'selected' : '' }}>Auto
                                                (Marquee if > 6 logos)</option>
                                            <option value="static-grid"
                                                {{ $block->getConfig('layout') === 'static-grid' ? 'selected' : '' }}>Static Grid
                                                (Centered)</option>
                                            <option value="marquee"
                                                {{ $block->getConfig('layout') === 'marquee' ? 'selected' : '' }}>Marquee (Always
                                                Scrolling)</option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Auto mode switches to scrolling marquee when more
                                            than 6 logos are uploaded.</p>
                                    </div>

                                    {{-- Current Logos --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Logos</label>
                                        @php $logos = $block->getConfig('logos', []); @endphp

                                        @if (count($logos) > 0)
                                            <div class="grid grid-cols-4 gap-4 mb-4">
                                                @foreach ($logos as $index => $logo)
                                                    <div class="relative group bg-gray-100 rounded-lg p-4">
                                                        <img src="{{ Storage::url($logo) }}" alt="Logo"
                                                            class="h-12 w-full object-contain">
                                                        <button type="button" onclick="deleteLogo('{{ $logo }}')"
                                                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <i class="fa-solid fa-times text-xs"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 mb-4">No logos uploaded yet.</p>
                                        @endif
                                    </div>

                                    {{-- Upload New Logos --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fa-solid fa-upload mr-2"></i>
                                            Upload New Logos
                                        </label>
                                        <div
                                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                            <input type="file" name="logos[]" multiple accept="image/*" id="logo-upload"
                                                class="hidden" onchange="previewLogos(this)">
                                            <label for="logo-upload" class="cursor-pointer">
                                                <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                                <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                                                <p class="text-xs text-gray-400">PNG, JPG, SVG, GIF (Max 2MB each)</p>
                                            </label>
                                        </div>
                                        <div id="logo-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                                    </div>
                                </div>
                            @break

                            {{-- Latest Articles Block --}}
                            @case('latest_articles')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                        <input type="text" name="config[title]"
                                            value="{{ $block->getConfig('title', 'Latest Publications') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                                        <input type="text" name="config[subtitle]"
                                            value="{{ $block->getConfig('subtitle') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Layout</label>
                                            <select name="config[layout]" class="w-full rounded-lg border-gray-300">
                                                <option value="cards"
                                                    {{ $block->getConfig('layout') === 'cards' ? 'selected' : '' }}>Cards</option>
                                                <option value="list"
                                                    {{ $block->getConfig('layout') === 'list' ? 'selected' : '' }}>List</option>
                                                <option value="masonry"
                                                    {{ $block->getConfig('layout') === 'masonry' ? 'selected' : '' }}>Masonry
                                                </option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Articles to Show</label>
                                            <input type="number" name="config[limit]"
                                                value="{{ $block->getConfig('limit', 6) }}" min="1" max="20"
                                                class="w-full rounded-lg border-gray-300">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="config[show_abstract]" value="1"
                                                {{ $block->getConfig('show_abstract', true) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600">
                                            <span class="text-sm text-gray-700">Show Abstract</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="config[show_authors]" value="1"
                                                {{ $block->getConfig('show_authors', true) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600">
                                            <span class="text-sm text-gray-700">Show Authors</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="config[show_journal]" value="1"
                                                {{ $block->getConfig('show_journal', true) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600">
                                            <span class="text-sm text-gray-700">Show Journal</span>
                                        </label>
                                    </div>
                                </div>
                            @break

                            {{-- Newsletter CTA Block --}}
                            @case('newsletter_cta')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Headline</label>
                                        <input type="text" name="config[headline]"
                                            value="{{ $block->getConfig('headline', 'Stay Updated') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subheadline</label>
                                        <input type="text" name="config[subheadline]"
                                            value="{{ $block->getConfig('subheadline') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                                        <input type="text" name="config[button_text]"
                                            value="{{ $block->getConfig('button_text', 'Subscribe') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                </div>
                            @break

                            {{-- Custom HTML Block --}}
                            @case('custom_html')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">HTML Content</label>
                                        <textarea name="config[html_content]" rows="10" class="w-full rounded-lg border-gray-300 font-mono text-sm">{{ $block->getConfig('html_content') }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">⚠️ Be careful with custom HTML. Only use trusted
                                            content.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">CSS Classes</label>
                                        <input type="text" name="config[css_classes]"
                                            value="{{ $block->getConfig('css_classes') }}"
                                            class="w-full rounded-lg border-gray-300" placeholder="e.g., bg-gray-100 py-8">
                                    </div>
                                </div>
                            @break

                            {{-- Statistics Counter Block --}}
                            @case('stats_counter')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                        <input type="text" name="config[title]"
                                            value="{{ $block->getConfig('title', 'Platform Statistics') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                                        <input type="text" name="config[subtitle]"
                                            value="{{ $block->getConfig('subtitle') }}"
                                            class="w-full rounded-lg border-gray-300">
                                    </div>

                                    {{-- Current Statistics --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Statistics</label>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            @if (isset($stats))
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div class="text-center">
                                                        <div class="text-2xl font-bold text-blue-600">
                                                            {{ number_format($stats['journals'] ?? 0) }}</div>
                                                        <div class="text-sm text-gray-600">Active Journals</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-2xl font-bold text-green-600">
                                                            {{ number_format($stats['submissions'] ?? 0) }}</div>
                                                        <div class="text-sm text-gray-600">Total Submissions</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-2xl font-bold text-purple-600">
                                                            {{ number_format($stats['users'] ?? 0) }}</div>
                                                        <div class="text-sm text-gray-600">Registered Users</div>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500">Loading statistics...</p>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">These statistics are automatically calculated from
                                            the database.</p>
                                    </div>

                                    {{-- Configuration Options --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Animation Duration
                                                (ms)</label>
                                            <input type="number" name="config[animation_duration]"
                                                value="{{ $block->getConfig('animation_duration', 2000) }}" min="500"
                                                max="5000" class="w-full rounded-lg border-gray-300">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Animation Delay
                                                (ms)</label>
                                            <input type="number" name="config[animation_delay]"
                                                value="{{ $block->getConfig('animation_delay', 200) }}" min="0"
                                                max="1000" class="w-full rounded-lg border-gray-300">
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-6">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="config[show_icons]" value="1"
                                                {{ $block->getConfig('show_icons', true) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600">
                                            <span class="text-sm text-gray-700">Show Icons</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="config[animate_on_scroll]" value="1"
                                                {{ $block->getConfig('animate_on_scroll', true) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600">
                                            <span class="text-sm text-gray-700">Animate on Scroll</span>
                                        </label>
                                    </div>
                                </div>
                            @break

                            {{-- Default for other blocks --}}

                            @default
                                <div class="space-y-4">
                                    @foreach ($block->config ?? [] as $key => $value)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2 capitalize">
                                                {{ str_replace('_', ' ', $key) }}
                                            </label>
                                            @if (is_bool($value))
                                                <input type="checkbox" name="config[{{ $key }}]" value="1"
                                                    {{ $value ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                            @elseif(is_array($value))
                                                <textarea name="config[{{ $key }}]" rows="3"
                                                    class="w-full rounded-lg border-gray-300 font-mono text-sm">{{ json_encode($value, JSON_PRETTY_PRINT) }}</textarea>
                                            @else
                                                <input type="text" name="config[{{ $key }}]"
                                                    value="{{ $value }}" class="w-full rounded-lg border-gray-300">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                        @endswitch
                    </div>

                    {{-- Actions --}}
                    <div class="p-6 bg-gray-50 flex items-center justify-between">
                        <a href="{{ route('admin.site.appearance.index') }}"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                            <i class="fa-solid fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewLogos(input) {
                const preview = document.getElementById('logo-preview');
                preview.innerHTML = '';

                if (input.files) {
                    Array.from(input.files).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'bg-gray-100 rounded-lg p-4 flex items-center justify-center';
                            div.innerHTML = `<img src="${e.target.result}" class="h-12 max-w-full object-contain">`;
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }

            function deleteLogo(path) {
                if (!confirm('Are you sure you want to delete this logo?')) return;

                fetch('{{ route('admin.site.appearance.logo.delete', $block) }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            path: path
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }
        </script>
    @endpush
@endsection
