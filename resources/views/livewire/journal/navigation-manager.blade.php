<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Navigation Manager</h1>
            <p class="mt-1 text-sm text-gray-500">Configure navigation menus and menu items for your journal. OJS 3.3 compatible.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('journal.settings.index', $journal->slug) }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Settings
            </a>
        </div>
    </div>

    {{-- SECTION 1: NAVIGATION MENUS --}}
    <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-lg text-slate-800">Navigation Menus</h3>
                <p class="text-sm text-slate-500 mt-1">Create menus and assign them to theme areas.</p>
            </div>
            <button wire:click="createMenu"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-plus mr-2"></i>
                Add Menu
            </button>
        </div>

        @if($menus->isEmpty())
        <div class="text-center py-12 text-slate-400">
            <i class="fa-solid fa-bars text-4xl mb-3"></i>
            <p class="font-medium">No navigation menus yet</p>
            <p class="text-sm">Create a menu to get started.</p>
        </div>
        @else
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs rounded-lg">
                <tr>
                    <th class="px-4 py-3 rounded-l-lg">Title</th>
                    <th class="px-4 py-3">Assigned Area</th>
                    <th class="px-4 py-3">Items</th>
                    <th class="px-4 py-3 text-right rounded-r-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($menus as $menu)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3">
                        <button wire:click="editMenu('{{ $menu->id }}')"
                            class="font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                            {{ $menu->title }}
                        </button>
                    </td>
                    <td class="px-4 py-3">
                        @if($menu->area_name)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $menu->area_name === 'primary' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $menu->area_name === 'primary' ? 'Primary Navigation' : 'User Navigation' }}
                            </span>
                        @else
                            <span class="text-slate-400 italic text-xs">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600 text-xs max-w-xs truncate">
                        {{ $menu->items_preview }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="editMenu('{{ $menu->id }}')"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button wire:click="deleteMenu('{{ $menu->id }}')"
                                wire:confirm="Are you sure you want to delete this menu?"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- SECTION 2: NAVIGATION MENU ITEMS --}}
    <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-lg text-slate-800">Navigation Menu Items</h3>
                <p class="text-sm text-slate-500 mt-1">Create reusable menu items that can be assigned to any menu.</p>
            </div>
            <button wire:click="createItem"
                class="inline-flex items-center px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-plus mr-2"></i>
                Add Item
            </button>
        </div>

        @if($allItems->isEmpty())
        <div class="text-center py-12 text-slate-400">
            <i class="fa-solid fa-link text-4xl mb-3"></i>
            <p class="font-medium">No menu items yet</p>
            <p class="text-sm">Create items to add them to your menus.</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($allItems as $item)
            <div class="flex justify-between items-center p-4 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors group">
                <div class="flex items-center gap-3">
                    @if($item->icon)
                    <i class="{{ $item->icon }} text-slate-400"></i>
                    @else
                    <i class="fa-solid fa-link text-slate-300"></i>
                    @endif
                    <div>
                        <span class="font-medium text-slate-700 block">{{ $item->title }}</span>
                        <span class="text-xs text-slate-400">
                            {{ ucfirst($item->type) }}
                            @if($item->type === 'route' && $item->route_name)
                                — {{ $item->route_name }}
                            @elseif($item->type === 'custom' && $item->url)
                                — {{ Str::limit($item->url, 30) }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button wire:click="editItem('{{ $item->id }}')"
                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                    </button>
                    <button wire:click="deleteItem('{{ $item->id }}')"
                        wire:confirm="Are you sure you want to delete this item? It will be removed from all menus."
                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- MODAL: EDIT MENU (OJS 3.3 Two-Column UI) --}}
    @if($showMenuModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:keydown.escape.window="$set('showMenuModal', false)">
        <div class="bg-white w-full max-w-5xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]" @click.outside="$wire.set('showMenuModal', false)">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-lg text-slate-800">
                    <i class="fa-solid fa-bars mr-2 text-indigo-600"></i>
                    {{ $editingMenuId ? 'Edit Menu' : 'Create Menu' }}
                </h3>
                <button wire:click="$set('showMenuModal', false)" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 overflow-y-auto flex-1">
                {{-- Menu Settings Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Menu Title *</label>
                        <input type="text" wire:model="editingMenuTitle"
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g., Primary Navigation">
                        @error('editingMenuTitle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Active Theme Area</label>
                        <select wire:model="editingMenuArea"
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Not Assigned --</option>
                            <option value="primary">Primary Navigation Menu (Header)</option>
                            <option value="user">User Navigation Menu (Top Right)</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Only one menu can be assigned to each area.</p>
                    </div>
                </div>

                {{-- THE TWO-COLUMN ASSIGNMENT UI --}}
                @if($editingMenuId)
                <div class="grid grid-cols-2 gap-0 border border-slate-300 rounded-xl overflow-hidden">
                    {{-- Left Column: Assigned Items --}}
                    <div class="border-r border-slate-300 p-4 bg-slate-50">
                        <h4 class="font-bold text-sm text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-green-500"></i>
                            Assigned Menu Items
                        </h4>
                        <ul class="space-y-2 min-h-[200px] max-h-[400px] overflow-y-auto">
                            @forelse($assignedItems as $assignment)
                            <li class="bg-white border border-slate-200 p-3 rounded-lg flex justify-between items-center shadow-sm hover:shadow transition-shadow"
                                wire:key="assigned-{{ $assignment->id }}">
                                <div class="flex items-center gap-2">
                                    @if($assignment->item?->icon)
                                    <i class="{{ $assignment->item->icon }} text-slate-400 text-sm"></i>
                                    @endif
                                    <span class="text-sm font-medium text-slate-700">{{ $assignment->item?->title ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click="moveUp('{{ $assignment->id }}')"
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                                        title="Move Up">
                                        <i class="fa-solid fa-chevron-up text-xs"></i>
                                    </button>
                                    <button wire:click="moveDown('{{ $assignment->id }}')"
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                                        title="Move Down">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </button>
                                    <button wire:click="unassignItem('{{ $assignment->id }}')"
                                        class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors ml-1"
                                        title="Remove from menu">
                                        <i class="fa-solid fa-times text-xs"></i>
                                    </button>
                                </div>
                            </li>
                            @empty
                            <li class="text-center py-8 text-slate-400">
                                <i class="fa-solid fa-inbox text-2xl mb-2"></i>
                                <p class="text-xs">No items assigned.</p>
                                <p class="text-xs">Use the arrows to add items →</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Right Column: Unassigned Items --}}
                    <div class="p-4 bg-white">
                        <h4 class="font-bold text-sm text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-list text-slate-400"></i>
                            Available Menu Items
                        </h4>
                        <ul class="space-y-2 min-h-[200px] max-h-[400px] overflow-y-auto">
                            @forelse($unassignedItemsList as $item)
                            <li class="flex items-center gap-3 p-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg transition-colors"
                                wire:key="unassigned-{{ $item->id }}">
                                <button wire:click="assignItem('{{ $item->id }}')"
                                    class="flex items-center gap-1 px-2.5 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs font-medium rounded-lg transition-colors">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    Add
                                </button>
                                <div class="flex items-center gap-2 flex-1">
                                    @if($item->icon)
                                    <i class="{{ $item->icon }} text-slate-400 text-sm"></i>
                                    @endif
                                    <span class="text-sm text-slate-600">{{ $item->title }}</span>
                                    <span class="text-xs text-slate-400">({{ ucfirst($item->type) }})</span>
                                </div>
                            </li>
                            @empty
                            <li class="text-center py-8 text-slate-400">
                                <i class="fa-solid fa-check-double text-2xl mb-2"></i>
                                <p class="text-xs">All items are assigned!</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                @else
                <div class="text-center py-8 bg-slate-50 rounded-xl border border-slate-200">
                    <i class="fa-solid fa-info-circle text-2xl text-slate-400 mb-2"></i>
                    <p class="text-sm text-slate-500">Save the menu first to assign items.</p>
                </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('showMenuModal', false)"
                    class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="saveMenu"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-check mr-2"></i>
                    {{ $editingMenuId ? 'Save Changes' : 'Create Menu' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL: CREATE/EDIT ITEM --}}
    @if($showItemModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:keydown.escape.window="$set('showItemModal', false)">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden" @click.outside="$wire.set('showItemModal', false)">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-lg text-slate-800">
                    <i class="fa-solid fa-link mr-2 text-indigo-600"></i>
                    {{ $editingItemId ? 'Edit Menu Item' : 'Create Menu Item' }}
                </h3>
                <button wire:click="$set('showItemModal', false)" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-4">
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Title *</label>
                    <input type="text" wire:model="editingItemTitle"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g., About Us">
                    @error('editingItemTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Navigation Page Type</label>
                    <select wire:model.live="editingItemType"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="custom">Custom Link</option>
                        <option value="route">System Page</option>
                        <option value="page">Custom Page</option>
                    </select>
                </div>

                {{-- Custom URL (for custom type) --}}
                @if($editingItemType === 'custom')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">URL</label>
                    <input type="text" wire:model="editingItemUrl"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="https://example.com">
                </div>
                @endif

                {{-- Route Name (for route type) --}}
                @if($editingItemType === 'route')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">System Page</label>
                    <select wire:model="editingItemRouteName"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Select a page --</option>
                        @foreach($availableRoutes as $route)
                        <option value="{{ $route['name'] }}">{{ $route['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Icon --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Icon (optional)</label>
                    <input type="text" wire:model="editingItemIcon"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="fa-solid fa-info-circle">
                    <p class="text-xs text-slate-500 mt-1">Font Awesome icon class. <a href="https://fontawesome.com/icons" target="_blank" class="text-indigo-600 hover:underline">Browse icons</a></p>
                </div>

                {{-- Target --}}
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="editingItemTarget" value="_blank"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            {{ $editingItemTarget === '_blank' ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">Open in new tab</span>
                    </label>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('showItemModal', false)"
                    class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="saveItem"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-check mr-2"></i>
                    {{ $editingItemId ? 'Save Changes' : 'Create Item' }}
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
