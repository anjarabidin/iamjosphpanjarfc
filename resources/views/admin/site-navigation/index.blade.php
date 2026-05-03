@extends('layouts.admin')

@section('title', 'Site Navigation')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="navigationManager()">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Site Navigation</h1>
                    <p class="text-sm text-gray-500">Manage navigation menus for the portal</p>
                </div>
                <a href="{{ route('portal.home') }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fa-solid fa-external-link-alt mr-2"></i>
                    Preview Portal
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Success Message --}}
        <div x-show="successMessage" x-cloak
             class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-800" x-text="successMessage"></span>
            </div>
        </div>

        {{-- Tabs for Menu Locations --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Tab Headers --}}
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    @foreach($menus as $menu)
                        <button @click="activeTab = '{{ $menu->area_name }}'"
                                :class="activeTab === '{{ $menu->area_name }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                            {{ $menu->title }}
                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full" 
                                  :class="activeTab === '{{ $menu->area_name }}' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500'">
                                {{ $menu->items->count() }}
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- Tab Content --}}
            @foreach($menus as $menu)
                <div x-show="activeTab === '{{ $menu->area_name }}'" x-cloak class="p-6">
                    {{-- Add Item Button --}}
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-sm text-gray-500">Drag items to reorder. Click to edit.</p>
                        <button @click="openAddModal('{{ $menu->id }}')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Add Menu Item
                        </button>
                    </div>

                    {{-- Menu Items --}}
                    @if($menu->items->count() > 0)
                        <div class="space-y-2" id="menu-{{ $menu->id }}" data-menu-id="{{ $menu->id }}">
                            @foreach($menu->items->sortBy('order') as $assignment)
                                <div class="menu-item flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-200 cursor-move"
                                     data-item-id="{{ $assignment->id }}">
                                    {{-- Drag Handle --}}
                                    <div class="drag-handle text-gray-400 hover:text-gray-600">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </div>

                                    {{-- Item Icon --}}
                                    <div class="w-8 h-8 rounded-lg {{ $assignment->item->type === 'divider' ? 'bg-gray-200' : 'bg-blue-100' }} flex items-center justify-center flex-shrink-0">
                                        @if($assignment->item->type === 'divider')
                                            <i class="fa-solid fa-minus text-gray-500"></i>
                                        @elseif($assignment->item->icon)
                                            <i class="{{ $assignment->item->icon }} text-blue-600"></i>
                                        @else
                                            <i class="fa-solid fa-link text-blue-600"></i>
                                        @endif
                                    </div>

                                    {{-- Item Details --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-gray-900">{{ $assignment->item->title }}</div>
                                        <div class="text-xs text-gray-500 truncate">
                                            @if($assignment->item->type === 'route')
                                                <span class="text-indigo-600">Route:</span> {{ $assignment->item->route_name }}
                                            @elseif($assignment->item->type === 'divider')
                                                <span class="text-gray-400">— Divider —</span>
                                            @else
                                                {{ $assignment->item->url ?? '#' }}
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Status & Type Badges --}}
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs px-2 py-1 rounded-full {{ $assignment->item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $assignment->item->is_active ? 'Active' : 'Hidden' }}
                                        </span>
                                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 capitalize">
                                            {{ $assignment->item->type }}
                                        </span>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1">
                                        <button @click="openEditModal('{{ $assignment->item->id }}', {{ json_encode($assignment->item) }})"
                                                class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                                title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button @click="deleteItem('{{ $assignment->item->id }}')"
                                                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg"
                                                title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-bars text-gray-400"></i>
                            </div>
                            <p>No menu items yet. Add your first item above.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Add/Edit Item Modal --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="closeModal()"></div>

            <div class="inline-block w-full max-w-lg my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-10">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="editingItem ? 'Edit Menu Item' : 'Add Menu Item'"></h3>
                </div>

                {{-- Modal Body --}}
                <form @submit.prevent="saveItem()" class="p-6 space-y-4">
                    {{-- Label --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Label <span class="text-red-500">*</span></label>
                        <input type="text" x-model="formData.label" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Menu label">
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select x-model="formData.type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="custom">Custom Link</option>
                            <option value="route">Internal Route</option>
                            <option value="page">Site Page</option>
                            <option value="divider">Divider</option>
                        </select>
                    </div>

                    {{-- URL (for custom type) --}}
                    <div x-show="formData.type === 'custom'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                        <input type="text" x-model="formData.url"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="https://example.com or /path">
                    </div>

                    {{-- Route (for route type) --}}
                    <div x-show="formData.type === 'route'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Route</label>
                        <select x-model="formData.route_name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select a route...</option>
                            @foreach($availableRoutes as $route)
                                <option value="{{ $route['name'] }}">{{ $route['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Page (for page type) --}}
                    <div x-show="formData.type === 'page'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Page</label>
                        <select x-model="formData.page_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select a page...</option>
                            @foreach($sitePages as $sitePage)
                                <option value="{{ $sitePage->id }}">{{ $sitePage->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Icon --}}
                    <div x-show="formData.type !== 'divider'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon (FontAwesome class)</label>
                        <input type="text" x-model="formData.icon"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="e.g., fa-solid fa-home">
                    </div>

                    {{-- Target --}}
                    <div x-show="formData.type !== 'divider'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Open in</label>
                        <select x-model="formData.target"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="_self">Same window</option>
                            <option value="_blank">New window</option>
                        </select>
                    </div>

                    {{-- Active --}}
                    <div class="flex items-center gap-3" x-show="editingItem">
                        <input type="checkbox" x-model="formData.is_active" id="is_active"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                    </div>
                </form>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-end gap-3">
                    <button @click="closeModal()" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="saveItem()" type="button"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700"
                            :disabled="saving">
                        <span x-show="!saving" x-text="editingItem ? 'Update' : 'Add Item'"></span>
                        <span x-show="saving"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function navigationManager() {
    return {
        activeTab: '{{ $menus->first()?->area_name ?? "primary" }}',
        showModal: false,
        editingItem: null,
        currentMenuId: null,
        saving: false,
        successMessage: '',
        formData: {
            label: '',
            type: 'custom',
            url: '',
            route_name: '',
            page_id: '',
            icon: '',
            target: '_self',
            is_active: true,
        },

        init() {
            // Initialize Sortable for each menu
            document.querySelectorAll('[id^="menu-"]').forEach(el => {
                new Sortable(el, {
                    animation: 150,
                    handle: '.drag-handle',
                    onEnd: (evt) => {
                        this.reorderItems(evt.target.dataset.menuId);
                    }
                });
            });
        },

        openAddModal(menuId) {
            this.editingItem = null;
            this.currentMenuId = menuId;
            this.formData = {
                label: '',
                type: 'custom',
                url: '',
                route_name: '',
                page_id: '',
                icon: '',
                target: '_self',
                is_active: true,
            };
            this.showModal = true;
        },

        openEditModal(itemId, item) {
            this.editingItem = itemId;
            this.currentMenuId = item.menu_id;
            this.formData = {
                label: item.title,
                type: item.type,
                url: item.url || '',
                route_name: item.route_name || '',
                page_id: '',
                icon: item.icon || '',
                target: item.target || '_self',
                is_active: item.is_active,
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingItem = null;
        },

        async saveItem() {
            this.saving = true;
            const url = this.editingItem 
                ? `/admin/site-navigation/items/${this.editingItem}`
                : '/admin/site-navigation/items';
            const method = this.editingItem ? 'PUT' : 'POST';

            try {
                const payload = { ...this.formData };
                if (!this.editingItem) {
                    payload.menu_id = this.currentMenuId;
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (data.success) {
                    this.successMessage = data.message;
                    this.closeModal();
                    setTimeout(() => location.reload(), 500);
                }
            } catch (error) {
                console.error('Error saving item:', error);
            } finally {
                this.saving = false;
            }
        },

        async deleteItem(itemId) {
            if (!confirm('Are you sure you want to delete this menu item?')) return;

            try {
                const response = await fetch(`/admin/site-navigation/items/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.successMessage = data.message;
                    setTimeout(() => location.reload(), 500);
                }
            } catch (error) {
                console.error('Error deleting item:', error);
            }
        },

        async reorderItems(menuId) {
            const container = document.getElementById(`menu-${menuId}`);
            const items = Array.from(container.querySelectorAll('.menu-item')).map((el, index) => ({
                id: el.dataset.itemId,
                order: index
            }));

            try {
                await fetch('/admin/site-navigation/items/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ items })
                });
            } catch (error) {
                console.error('Error reordering items:', error);
            }
        }
    }
}
</script>
@endpush
@endsection
