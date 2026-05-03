@extends('layouts.admin')

@section('title', 'Site Appearance - Page Builder')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="pageBuilder()">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Page Builder</h1>
                    <p class="text-sm text-gray-500">Customize your portal landing page</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('portal.home') }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fa-solid fa-external-link-alt mr-2"></i>
                        Preview Portal
                    </a>
                    <button @click="saveOrder()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700"
                            :disabled="!hasChanges">
                        <i class="fa-solid fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Block Manager (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Active Blocks --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Active Sections</h2>
                            <p class="text-sm text-gray-500">Drag to reorder • Click toggle to disable</p>
                        </div>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                            <span x-text="activeBlocks.length"></span> Active
                        </span>
                    </div>

                    {{-- Sortable List --}}
                    <div id="active-blocks" class="space-y-3">
                        @foreach($blocks->where('is_active', true) as $block)
                            <div class="block-item bg-gray-50 rounded-lg p-4 border-2 border-transparent hover:border-indigo-200 cursor-move transition-all"
                                 data-id="{{ $block->id }}"
                                 data-key="{{ $block->key }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        {{-- Drag Handle --}}
                                        <div class="drag-handle text-gray-400 hover:text-gray-600">
                                            <i class="fa-solid fa-grip-vertical text-lg"></i>
                                        </div>
                                        
                                        {{-- Block Icon --}}
                                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                            <i class="{{ $block->icon ?? 'fa-solid fa-cube' }} text-indigo-600"></i>
                                        </div>
                                        
                                        {{-- Block Info --}}
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $block->title }}</h3>
                                            <p class="text-sm text-gray-500">{{ $block->description }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        {{-- Edit Button - Full page for complex blocks, modal for simple ones --}}
                                        @if(in_array($block->key, ['indexing_partners', 'hero_search', 'custom_html']))
                                            <a href="{{ route('admin.site.appearance.edit', $block) }}"
                                               class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                               title="Configure">
                                                <i class="fa-solid fa-cog"></i>
                                            </a>
                                        @else
                                            <button @click="editBlock({{ $block->id }})"
                                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                    title="Configure">
                                                <i class="fa-solid fa-cog"></i>
                                            </button>
                                        @endif

                                        {{-- Toggle Switch --}}
                                        <button @click="toggleBlock({{ $block->id }})"
                                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none bg-indigo-600"
                                                title="Toggle Active">
                                            <span class="translate-x-5 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Inactive Blocks --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Available Sections</h2>
                            <p class="text-sm text-gray-500">Enable these sections to add them to your portal</p>
                        </div>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                            {{ $blocks->where('is_active', false)->count() }} Disabled
                        </span>
                    </div>

                    <div id="inactive-blocks" class="space-y-3">
                        @foreach($blocks->where('is_active', false) as $block)
                            <div class="block-item bg-gray-50 rounded-lg p-4 border border-gray-200 opacity-60 hover:opacity-100 transition-all"
                                 data-id="{{ $block->id }}"
                                 data-key="{{ $block->key }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        {{-- Block Icon --}}
                                        <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="{{ $block->icon ?? 'fa-solid fa-cube' }} text-gray-500"></i>
                                        </div>
                                        
                                        {{-- Block Info --}}
                                        <div>
                                            <h3 class="font-semibold text-gray-700">{{ $block->title }}</h3>
                                            <p class="text-sm text-gray-500">{{ $block->description }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        {{-- Edit Button - Full page for complex blocks --}}
                                        @if(in_array($block->key, ['indexing_partners', 'hero_search', 'custom_html']))
                                            <a href="{{ route('admin.site.appearance.edit', $block) }}"
                                               class="p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                                                <i class="fa-solid fa-cog"></i>
                                            </a>
                                        @else
                                            <button @click="editBlock({{ $block->id }})"
                                                    class="p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                                                <i class="fa-solid fa-cog"></i>
                                            </button>
                                        @endif

                                        {{-- Enable Button --}}
                                        <button @click="toggleBlock({{ $block->id }})"
                                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out bg-gray-200"
                                                title="Enable">
                                            <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Column: Preview / Help (1/3) --}}
            <div class="space-y-6">
                {{-- Quick Preview --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fa-solid fa-eye mr-2 text-indigo-500"></i>
                        Current Layout
                    </h3>
                    <div class="space-y-2" id="preview-list">
                        @foreach($blocks->where('is_active', true)->sortBy('sort_order') as $index => $block)
                            <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg text-sm">
                                <span class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                    {{ $index + 1 }}
                                </span>
                                <i class="{{ $block->icon }} text-gray-400"></i>
                                <span class="text-gray-700">{{ $block->title }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Help Card --}}
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
                    <h3 class="font-bold mb-3">
                        <i class="fa-solid fa-lightbulb mr-2"></i>
                        Tips
                    </h3>
                    <ul class="space-y-2 text-sm text-indigo-100">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check mt-1"></i>
                            <span>Drag blocks to reorder them on your portal</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check mt-1"></i>
                            <span>Click the gear icon to customize each block</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check mt-1"></i>
                            <span>Toggle switches to show/hide sections</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check mt-1"></i>
                            <span>Changes are saved automatically</span>
                        </li>
                    </ul>
                </div>

                {{-- Block Categories --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Categories</h3>
                    <div class="space-y-2">
                        @foreach($blocksByCategory as $category => $categoryBlocks)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 capitalize">{{ $category }}</span>
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                                    {{ $categoryBlocks->count() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Block Configuration Modal (Wide Mode) --}}
    <div x-show="showConfigModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="showConfigModal = false">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/50 transition-opacity" @click="showConfigModal = false"></div>

            {{-- Modal (Wide) --}}
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-auto">
                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900" x-text="currentBlock?.title"></h3>
                        <p class="text-sm text-gray-500" x-text="currentBlock?.description"></p>
                    </div>
                    <button @click="showConfigModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <div x-show="loadingConfig" class="text-center py-8">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-500"></i>
                        <p class="text-gray-500 mt-2">Loading configuration...</p>
                    </div>

                    <div x-show="!loadingConfig">
                        {{-- Block Title --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                            <input type="text" x-model="currentBlock.title"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Featured Journals Block - Special UI --}}
                        <template x-if="currentBlock?.key === 'featured_journals'">
                            <div class="space-y-6">
                                {{-- Subtitle --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                                    <input type="text" x-model="currentBlock.config.subtitle"
                                           placeholder="e.g., Explore our top-rated journals"
                                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                {{-- Display Count --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Count</label>
                                        <input type="number" x-model.number="currentBlock.config.display_count" min="1" max="12"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-500 mt-1">Max journals to show on homepage</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Layout Style</label>
                                        <select x-model="currentBlock.config.layout"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="grid">Grid</option>
                                            <option value="carousel">Carousel</option>
                                            <option value="list">List</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Journal Selection --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Select Featured Journals
                                        <span class="text-gray-400 font-normal">(click to select/deselect)</span>
                                    </label>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-80 overflow-y-auto p-1">
                                        <template x-for="journal in currentBlock.journals" :key="journal.id">
                                            <label class="relative cursor-pointer">
                                                <input type="checkbox" 
                                                       :value="journal.id"
                                                       :checked="isJournalSelected(journal.id)"
                                                       @change="toggleJournalSelection(journal.id)"
                                                       class="sr-only peer">
                                                <div class="flex items-center gap-3 p-3 rounded-lg border-2 transition-all
                                                            peer-checked:border-indigo-500 peer-checked:bg-indigo-50
                                                            border-gray-200 hover:border-gray-300 hover:bg-gray-50">
                                                    {{-- Journal Logo/Avatar --}}
                                                    <div class="flex-shrink-0">
                                                        <template x-if="journal.logo_path">
                                                            <img :src="'/storage/' + journal.logo_path" 
                                                                 class="w-10 h-10 rounded-lg object-cover">
                                                        </template>
                                                        <template x-if="!journal.logo_path">
                                                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm"
                                                                 x-text="(journal.abbreviation || journal.name).substring(0, 2).toUpperCase()">
                                                            </div>
                                                        </template>
                                                    </div>
                                                    {{-- Journal Info --}}
                                                    <div class="min-w-0 flex-1">
                                                        <p class="font-medium text-gray-900 text-sm truncate" x-text="journal.name"></p>
                                                        <p class="text-xs text-gray-500 truncate" x-text="journal.abbreviation || journal.slug"></p>
                                                    </div>
                                                    {{-- Check Icon --}}
                                                    <div class="flex-shrink-0">
                                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                                                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-500"
                                                             :class="isJournalSelected(journal.id) ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                                                            <i class="fa-solid fa-check text-white text-xs" 
                                                               x-show="isJournalSelected(journal.id)"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </template>
                                    </div>

                                    {{-- Selection Summary --}}
                                    <div class="mt-3 flex items-center justify-between text-sm">
                                        <span class="text-gray-500">
                                            <span x-text="(currentBlock.config.featured_ids || []).length"></span> journals selected
                                        </span>
                                        <button type="button" @click="currentBlock.config.featured_ids = []"
                                                class="text-red-600 hover:text-red-700 text-sm font-medium">
                                            Clear Selection
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Statistics Counter Block - Special UI --}}
                        <template x-if="currentBlock?.key === 'stats_counter'">
                            <div class="space-y-6">
                                {{-- Subtitle --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                                    <input type="text" x-model="currentBlock.config.subtitle"
                                           placeholder="e.g., Growing academic community"
                                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                {{-- Current Statistics Display --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Current Statistics</label>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <template x-if="currentBlock.stats">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-blue-600" x-text="formatNumber(currentBlock.stats.journals || 0)"></div>
                                                    <div class="text-sm text-gray-600">Active Journals</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-green-600" x-text="formatNumber(currentBlock.stats.submissions || 0)"></div>
                                                    <div class="text-sm text-gray-600">Total Submissions</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-purple-600" x-text="formatNumber(currentBlock.stats.users || 0)"></div>
                                                    <div class="text-sm text-gray-600">Registered Users</div>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!currentBlock.stats">
                                            <p class="text-sm text-gray-500">Loading statistics...</p>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">These statistics are automatically calculated from the database.</p>
                                </div>

                                {{-- Animation Settings --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Animation Duration (ms)</label>
                                        <input type="number" x-model.number="currentBlock.config.animation_duration" min="500" max="5000"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-500 mt-1">How long each counter takes to animate</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Animation Delay (ms)</label>
                                        <input type="number" x-model.number="currentBlock.config.animation_delay" min="0" max="1000"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-500 mt-1">Delay between starting each counter</p>
                                    </div>
                                </div>

                                {{-- Display Options --}}
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" x-model="currentBlock.config.show_icons"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">Show Icons</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" x-model="currentBlock.config.animate_on_scroll"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">Animate on Scroll</span>
                                    </label>
                                </div>
                            </div>
                        </template>

                        {{-- Subject Categories Block - Special UI --}}
                        <template x-if="currentBlock?.key === 'subject_categories'">
                            <div class="space-y-6">
                                {{-- Subtitle --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                                    <input type="text" x-model="currentBlock.config.subtitle"
                                           placeholder="e.g., Find journals in your research area"
                                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                {{-- Layout Settings --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Layout Style</label>
                                        <select x-model="currentBlock.config.layout"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="icon-grid">Icon Grid</option>
                                            <option value="list">List</option>
                                            <option value="dropdown">Dropdown</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Columns</label>
                                        <input type="number" x-model.number="currentBlock.config.columns" min="2" max="8"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-500 mt-1">Number of columns in grid layout</p>
                                    </div>
                                </div>

                                {{-- Display Options --}}
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" x-model="currentBlock.config.show_count"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">Show Journal Count</span>
                                    </label>
                                </div>

                                {{-- Categories Management --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Subject Categories</label>
                                    <div class="space-y-3">
                                        <template x-for="(category, index) in currentBlock.config.categories" :key="index">
                                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="flex-shrink-0">
                                                    <i :class="category.icon" class="text-lg text-gray-600"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="text" x-model="category.name"
                                                           class="w-full text-sm font-medium bg-transparent border-0 focus:ring-0 p-0"
                                                           placeholder="Category name">
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <input type="text" x-model="category.icon"
                                                           class="w-24 text-xs bg-white border border-gray-300 rounded px-2 py-1"
                                                           placeholder="fa-icon">
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <select x-model="category.color"
                                                            class="text-xs bg-white border border-gray-300 rounded px-2 py-1">
                                                        <option value="blue">Blue</option>
                                                        <option value="red">Red</option>
                                                        <option value="green">Green</option>
                                                        <option value="purple">Purple</option>
                                                        <option value="amber">Amber</option>
                                                        <option value="indigo">Indigo</option>
                                                        <option value="pink">Pink</option>
                                                        <option value="gray">Gray</option>
                                                    </select>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <button @click="removeCategory(index)"
                                                            class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded">
                                                        <i class="fa-solid fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Add New Category --}}
                                    <button @click="addCategory()"
                                            class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fa-solid fa-plus mr-2"></i>
                                        Add Category
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Generic Config Fields for other blocks --}}
                        <template x-if="currentBlock?.key !== 'featured_journals' && currentBlock?.key !== 'stats_counter' && currentBlock?.key !== 'subject_categories'">
                            <div class="space-y-4">
                                <template x-if="currentBlock?.config">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <template x-for="(value, key) in currentBlock.config" :key="key">
                                            <div :class="{'md:col-span-2': typeof value === 'string' && value.length > 50}">
                                                <label class="block text-sm font-medium text-gray-700 mb-2 capitalize" 
                                                       x-text="key.replace(/_/g, ' ')"></label>
                                                
                                                {{-- Textarea for long strings --}}
                                                <template x-if="typeof value === 'string' && value.length > 50">
                                                    <textarea x-model="currentBlock.config[key]" rows="3"
                                                              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                                </template>

                                                {{-- Text input for short strings --}}
                                                <template x-if="typeof value === 'string' && value.length <= 50">
                                                    <input type="text" x-model="currentBlock.config[key]"
                                                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                                </template>

                                                {{-- Checkbox for booleans --}}
                                                <template x-if="typeof value === 'boolean'">
                                                    <label class="flex items-center gap-2">
                                                        <input type="checkbox" x-model="currentBlock.config[key]"
                                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                        <span class="text-sm text-gray-600">Enabled</span>
                                                    </label>
                                                </template>

                                                {{-- Number input for integers --}}
                                                <template x-if="typeof value === 'number'">
                                                    <input type="number" x-model.number="currentBlock.config[key]"
                                                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button @click="showConfigModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="saveConfig()"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        <i class="fa-solid fa-save mr-2"></i>
                        Save Configuration
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
function pageBuilder() {
    return {
        hasChanges: false,
        showConfigModal: false,
        loadingConfig: false,
        currentBlock: null,
        activeBlocks: @json($blocks->where('is_active', true)->values()),
        journals: @json($journals),
        
        init() {
            this.initSortable();
        },

        initSortable() {
            const el = document.getElementById('active-blocks');
            if (el) {
                new Sortable(el, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'bg-indigo-50',
                    onEnd: (evt) => {
                        this.hasChanges = true;
                        this.updatePreview();
                    }
                });
            }
        },

        updatePreview() {
            // Update the preview list based on current order
            const items = document.querySelectorAll('#active-blocks .block-item');
            const previewList = document.getElementById('preview-list');
            // Could regenerate preview here
        },

        async saveOrder() {
            const items = document.querySelectorAll('#active-blocks .block-item');
            const blocks = Array.from(items).map((item, index) => ({
                id: parseInt(item.dataset.id),
                sort_order: index + 1
            }));

            try {
                const response = await fetch('{{ route("admin.site.appearance.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ blocks })
                });

                const data = await response.json();
                if (data.success) {
                    this.hasChanges = false;
                    this.showNotification('Order saved successfully', 'success');
                }
            } catch (error) {
                this.showNotification('Failed to save order', 'error');
            }
        },

        async toggleBlock(blockId) {
            try {
                const response = await fetch(`{{ url('admin/site-appearance') }}/${blockId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    location.reload(); // Simplest way to refresh the UI
                }
            } catch (error) {
                this.showNotification('Failed to toggle block', 'error');
            }
        },

        async editBlock(blockId) {
            this.loadingConfig = true;
            this.showConfigModal = true;

            try {
                const response = await fetch(`{{ url('admin/site-appearance') }}/${blockId}/config`);
                const data = await response.json();
                
                // Ensure config object exists with defaults
                if (!data.config) {
                    data.config = {};
                }
                
                // For featured_journals, ensure featured_ids array exists
                if (data.key === 'featured_journals') {
                    data.config.featured_ids = data.config.featured_ids || [];
                    data.config.subtitle = data.config.subtitle || '';
                    data.config.display_count = data.config.display_count || 6;
                    data.config.layout = data.config.layout || 'grid';
                    // Attach journals list to block for template access
                    data.journals = data.journals || this.journals;
                }

                // For statistics_counter, set defaults and attach stats
                if (data.key === 'stats_counter') {
                    data.config.subtitle = data.config.subtitle || '';
                    data.config.animation_duration = data.config.animation_duration || 2000;
                    data.config.animation_delay = data.config.animation_delay || 200;
                    data.config.show_icons = data.config.show_icons !== undefined ? data.config.show_icons : true;
                    data.config.animate_on_scroll = data.config.animate_on_scroll !== undefined ? data.config.animate_on_scroll : true;
                    // Stats are already attached from the API response
                }

                // For subject_categories, set defaults and ensure categories array exists
                if (data.key === 'subject_categories') {
                    data.config.subtitle = data.config.subtitle || '';
                    data.config.layout = data.config.layout || 'icon-grid';
                    data.config.columns = data.config.columns || 6;
                    data.config.show_count = data.config.show_count !== undefined ? data.config.show_count : true;
                    data.config.categories = data.config.categories || [];
                    // Categories are already attached from the API response
                }
                
                this.currentBlock = data;
            } catch (error) {
                this.showNotification('Failed to load configuration', 'error');
            } finally {
                this.loadingConfig = false;
            }
        },

        // Helper for Featured Journals selection
        isJournalSelected(journalId) {
            if (!this.currentBlock?.config?.featured_ids) return false;
            return this.currentBlock.config.featured_ids.includes(journalId);
        },

        toggleJournalSelection(journalId) {
            if (!this.currentBlock.config.featured_ids) {
                this.currentBlock.config.featured_ids = [];
            }
            
            const index = this.currentBlock.config.featured_ids.indexOf(journalId);
            if (index > -1) {
                this.currentBlock.config.featured_ids.splice(index, 1);
            } else {
                this.currentBlock.config.featured_ids.push(journalId);
            }
        },

        // Helper for Subject Categories
        addCategory() {
            if (!this.currentBlock.config.categories) {
                this.currentBlock.config.categories = [];
            }
            
            this.currentBlock.config.categories.push({
                name: 'New Category',
                icon: 'fa-circle',
                color: 'blue'
            });
        },

        removeCategory(index) {
            if (this.currentBlock.config.categories && confirm('Are you sure you want to remove this category?')) {
                this.currentBlock.config.categories.splice(index, 1);
            }
        },

        // Helper for formatting numbers
        formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        },

        async saveConfig() {
            if (!this.currentBlock) return;

            try {
                const response = await fetch(`{{ url('admin/site-appearance') }}/${this.currentBlock.id}/config`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        title: this.currentBlock.title,
                        config: this.currentBlock.config
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.showConfigModal = false;
                    this.showNotification('Configuration saved', 'success');
                }
            } catch (error) {
                this.showNotification('Failed to save configuration', 'error');
            }
        },

        showNotification(message, type) {
            // Simple alert for now - could use toast library
            alert(message);
        }
    }
}
</script>
@endpush
