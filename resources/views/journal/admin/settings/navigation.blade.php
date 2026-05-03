@extends('layouts.app')

@section('title', 'Navigation Manager - ' . $journal->name)

@section('content')
{{-- Alpine.js Drag & Drop Functionality --}}
<script>
function navigationManager(initialData) {
    return {
        ...initialData,

        // Drag and Drop Methods
        handleDragStart(event, itemId, itemType) {
            this.draggedItem = itemId;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', JSON.stringify({
                itemId: itemId,
                itemType: itemType
            }));

            // Add visual feedback
            event.target.classList.add('opacity-50', 'scale-95');
        },

        handleDragEnd(event) {
            this.draggedItem = null;
            this.draggedOverItem = null;

            // Remove visual feedback
            event.target.classList.remove('opacity-50', 'scale-95');
        },

        handleDragOver(event, zoneType) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';

            // Add visual feedback to drop zone
            if (zoneType === 'assigned') {
                event.currentTarget.classList.add('ring-2', 'ring-indigo-400', 'ring-opacity-50');

                // Check if hovering over a specific menu item
                const menuItem = event.target.closest('.menu-item');
                if (menuItem) {
                    const rect = menuItem.getBoundingClientRect();
                    const y = event.clientY - rect.top;
                    const threshold = rect.height * 0.7;

                    if (y > threshold) {
                        // Show child indicator
                        menuItem.classList.add('bg-indigo-50', 'border-indigo-300');
                        menuItem.classList.remove('bg-white');
                    } else {
                        // Show sibling indicator
                        menuItem.classList.add('border-t-2', 'border-t-indigo-400');
                    }
                }
            }
        },

        handleDragLeave(event, zoneType) {
            // Remove visual feedback from drop zone
            if (zoneType === 'assigned') {
                event.currentTarget.classList.remove('ring-2', 'ring-indigo-400', 'ring-opacity-50');

                // Remove item-specific feedback
                const menuItems = event.currentTarget.querySelectorAll('.menu-item');
                menuItems.forEach(item => {
                    item.classList.remove('bg-indigo-50', 'border-indigo-300', 'border-t-2', 'border-t-indigo-400');
                    item.classList.add('bg-white');
                });
            }
        },

        handleDrop(event, zoneType) {
            event.preventDefault();

            // Remove visual feedback
            event.currentTarget.classList.remove('ring-2', 'ring-indigo-400', 'ring-opacity-50');

            try {
                const data = JSON.parse(event.dataTransfer.getData('text/plain'));
                const draggedElement = document.querySelector(`[data-item-id="${data.itemId}"]`);

                if (!draggedElement) return;

                // Check if dropping onto another menu item (for nesting)
                const dropTarget = event.target.closest('.menu-item');
                let parentId = null;

                if (dropTarget && dropTarget !== draggedElement && zoneType === 'assigned') {
                    // Dropping onto another item - make it a child
                    parentId = dropTarget.dataset.itemId;

                    // Add the item as a child of the drop target
                    this.addAsChild(draggedElement, dropTarget, data.itemId);
                    return;
                }

                // Handle different drop scenarios
                if (zoneType === 'assigned' && data.itemType === 'available') {
                    // Dropping from available to assigned - assign the item
                    const menuId = this.selectedMenuId;
                    const routeName = draggedElement.querySelector('input[name="route_name"]')?.value || '';
                    this.assignItem(menuId, data.itemId, routeName, parentId);
                } else if (zoneType === 'assigned' && data.itemType === 'assigned') {
                    // Reordering within assigned items
                    this.reorderItems(event, data.itemId);
                }
            } catch (error) {
                console.error('Error handling drop:', error);
            }
        },

        reorderItems(event, draggedItemId) {
            const draggedElement = document.querySelector(`[data-item-id="${draggedItemId}"]`);
            const dropTarget = event.target.closest('.menu-item') || event.currentTarget;

            // Determine if dropping as child or sibling
            let newParentId = null;
            let targetContainer = event.currentTarget; // Default to root container

            if (dropTarget.classList.contains('menu-item') && dropTarget !== draggedElement) {
                // Check if dropping near the bottom of the item (to make it a child)
                const rect = dropTarget.getBoundingClientRect();
                const y = event.clientY - rect.top;
                const threshold = rect.height * 0.7; // Bottom 30% makes it a child

                if (y > threshold) {
                    // Make it a child
                    newParentId = dropTarget.dataset.itemId;
                    targetContainer = dropTarget.querySelector('ul') || this.createChildrenContainer(dropTarget);
                } else {
                    // Make it a sibling at the same level
                    targetContainer = dropTarget.parentElement;
                    newParentId = dropTarget.dataset.parentId || null;
                }
            }

            // Remove from current location
            draggedElement.remove();

            // Update parent ID in dataset
            draggedElement.dataset.parentId = newParentId || '';

            // Add visual indicator for submenu
            const submenuIndicator = draggedElement.querySelector('.text-indigo-600');
            if (newParentId) {
                if (!submenuIndicator) {
                    const typeSpan = draggedElement.querySelector('.text-slate-400');
                    if (typeSpan) {
                        typeSpan.innerHTML += ' <span class="text-indigo-600 font-medium">(submenu)</span>';
                    }
                }
                draggedElement.classList.add('ml-6', 'border-l-4', 'border-l-indigo-200');
            } else {
                if (submenuIndicator) submenuIndicator.remove();
                draggedElement.classList.remove('ml-6', 'border-l-4', 'border-l-indigo-200');
            }

            // Insert at the correct position
            const siblings = Array.from(targetContainer.children).filter(el => el.classList.contains('menu-item'));
            let insertBeforeElement = null;
            const rect = targetContainer.getBoundingClientRect();
            const y = event.clientY - rect.top;

            for (let sibling of siblings) {
                const siblingRect = sibling.getBoundingClientRect();
                const siblingY = siblingRect.top - rect.top + siblingRect.height / 2;

                if (y < siblingY) {
                    insertBeforeElement = sibling;
                    break;
                }
            }

            if (insertBeforeElement) {
                targetContainer.insertBefore(draggedElement, insertBeforeElement);
            } else {
                targetContainer.appendChild(draggedElement);
            }

            // Update order on server
            this.updateOrder(event.currentTarget);
        },

        createChildrenContainer(parentElement) {
            let container = parentElement.querySelector('ul');
            if (!container) {
                container = document.createElement('ul');
                container.className = 'mt-3 space-y-2';
                parentElement.appendChild(container);
            }
            return container;
        },

        updateOrder(container) {
            const items = Array.from(container.querySelectorAll('.menu-item'));
            const orderData = items.map((item, index) => {
                // Find parent by checking if this item is inside another menu-item
                let parentId = null;
                let parentElement = item.parentElement;
                while (parentElement && parentElement !== container) {
                    if (parentElement.classList.contains('menu-item')) {
                        parentId = parentElement.dataset.itemId;
                        break;
                    }
                    parentElement = parentElement.parentElement;
                }

                return {
                    id: item.dataset.itemId,
                    order: index + 1,
                    parent_id: parentId
                };
            });

            this.isLoading = true;

            fetch('{{ route("journal.settings.navigation.reorder", $journal->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ order: orderData })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showToast('Menu order updated successfully', 'success');
                } else {
                    this.showToast('Failed to update menu order', 'error');
                }
            })
            .catch(error => {
                console.error('Error updating order:', error);
                this.showToast('Failed to update menu order', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        addAsChild(draggedElement, parentElement, itemId) {
            // Find or create children container
            let childrenContainer = parentElement.querySelector('ul');
            if (!childrenContainer) {
                childrenContainer = document.createElement('ul');
                childrenContainer.className = 'mt-3 space-y-2';
                parentElement.appendChild(childrenContainer);
            }

            // Clone and modify the dragged element for nesting
            const childElement = draggedElement.cloneNode(true);
            childElement.classList.add('ml-6', 'border-l-4', 'border-l-indigo-200');
            childElement.dataset.parentId = parentElement.dataset.itemId;

            // Update the submenu indicator
            const submenuIndicator = childElement.querySelector('.text-indigo-600');
            if (!submenuIndicator) {
                const typeSpan = childElement.querySelector('.text-slate-400');
                if (typeSpan) {
                    typeSpan.innerHTML += ' <span class="text-indigo-600 font-medium">(submenu)</span>';
                }
            }

            // Add to children container
            childrenContainer.appendChild(childElement);

            // Remove from original location
            draggedElement.remove();

            // Update order on server
            this.updateOrder(parentElement.closest('ul'));
        },

        assignItem(menuId, itemId, routeName = '', parentId = null) {
            this.isLoading = true;

            const formData = new FormData();
            formData.append('menu_id', menuId);
            formData.append('menu_item_id', itemId);
            if (routeName) {
                formData.append('route_name', routeName);
            }
            if (parentId) {
                formData.append('parent_id', parentId);
            }

            fetch('{{ route("journal.settings.navigation.assign", $journal->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showToast('Item assigned successfully', 'success');
                    // Refresh the modal content
                    this.refreshModal();
                } else {
                    this.showToast(data.message || 'Failed to assign item', 'error');
                }
            })
            .catch(error => {
                console.error('Error assigning item:', error);
                this.showToast('Failed to assign item', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        unassignItem(assignmentId) {
            if (!confirm('Are you sure you want to remove this item from the menu?')) {
                return;
            }

            this.isLoading = true;

            fetch(`{{ route("journal.settings.navigation.unassign", [$journal->slug, ":assignmentId"]) }}`.replace(':assignmentId', assignmentId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showToast('Item removed successfully', 'success');
                    this.refreshModal();
                } else {
                    this.showToast(data.message || 'Failed to remove item', 'error');
                }
            })
            .catch(error => {
                console.error('Error removing item:', error);
                this.showToast('Failed to remove item', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        refreshModal() {
            // Simple page refresh for now - in a more advanced implementation,
            // you could fetch just the modal content via AJAX
            window.location.reload();
        },

        submitMenu(form) {
            const formData = new FormData(form);

            this.isLoading = true;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.showMenuModal = false;
                    this.editingMenu = null;
                    // Refresh page to show new menu
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showToast(data.message || 'Failed to save menu', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving menu:', error);
                this.showToast('Failed to save menu', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },



        deleteMenu(menuId) {
            if (!confirm('Are you sure you want to delete this menu? All assignments will be removed.')) {
                return;
            }

            this.isLoading = true;

            fetch(`{{ route("journal.settings.navigation.menus.destroy", [$journal->slug, ":menuId"]) }}`.replace(':menuId', menuId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showToast(data.message, 'success');
                    // Refresh page to remove deleted menu
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showToast(data.message || 'Failed to delete menu', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting menu:', error);
                this.showToast('Failed to delete menu', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        deleteItem(itemId) {
            if (!confirm('Are you sure you want to delete this menu item? It will be removed from all menus.')) {
                return;
            }

            this.isLoading = true;

            fetch(`{{ route("journal.settings.navigation.items.destroy", [$journal->slug, ":itemId"]) }}`.replace(':itemId', itemId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showToast(data.message, 'success');
                    // Refresh page to remove deleted item
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showToast(data.message || 'Failed to delete menu item', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting menu item:', error);
                this.showToast('Failed to delete menu item', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        showToast(message, type = 'info') {
            // Create a simple toast notification
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-[70] px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                info: 'bg-blue-500 text-white'
            };

            toast.classList.add(...colors[type].split(' '));
            toast.textContent = message;

            document.body.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        },

        updatePreviewUrl(path) {
            // This function is called when path input changes
            // Alpine.js will handle the reactive update
        }
    }
}
</script>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="navigationManager({
    showMenuModal: false,
    showAssignModal: false,
    editingMenu: null,
    editingItem: null,
    selectedMenuId: null,
    itemType: 'custom',
    draggedItem: null,
    draggedOverItem: null,
    isLoading: false
})">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Navigation Manager</h1>
            <p class="mt-1 text-sm text-gray-500">Configure navigation menus and menu items for your journal. OJS 3.3 compatible.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('journal.settings.website.edit', $journal->slug) }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Settings
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
            <span class="text-green-800">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    {{-- Loading Overlay --}}
    <div x-show="isLoading" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/20 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl p-6 flex items-center gap-3">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-indigo-600 border-t-transparent"></div>
            <span class="text-slate-700 font-medium">Updating...</span>
        </div>
    </div>

    {{-- SECTION 1: NAVIGATION MENUS --}}
    <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-lg text-slate-800">Navigation Menus</h3>
                <p class="text-sm text-slate-500 mt-1">Create menus and assign them to theme areas.</p>
            </div>
            <button @click="showMenuModal = true; editingMenu = null"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors" type="button">
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
                        <button @click="selectedMenuId = '{{ $menu->id }}'; showAssignModal = true"
                            class="font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                            {{ $menu->title }}
                        </button>
                    </td>
                    <td class="px-4 py-3">
                        @if($menu->area_name === 'primary')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                Primary Navigation
                            </span>
                        @elseif($menu->area_name === 'user')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                User Navigation
                            </span>
                        @elseif($menu->area_name)
                            {{-- Legacy area (footer, etc) - show as deprecated --}}
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                {{ ucfirst($menu->area_name) }} (deprecated)
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
                            <button @click="selectedMenuId = '{{ $menu->id }}'; showAssignModal = true"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                title="Manage Items" type="button">
                                <i class="fa-solid fa-list-check"></i>
                            </button>
                            <button @click="editingMenu = {{ json_encode($menu) }}; showMenuModal = true"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                title="Edit Menu" type="button">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button @click="deleteMenu('{{ $menu->id }}')"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Delete Menu" type="button">
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
            <button @click="window.location.href='{{ route('journal.settings.navigation.items.create', $journal->slug) }}'"
                class="inline-flex items-center px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors" type="button">
                <i class="fa-solid fa-plus mr-2"></i>
                Add Item
            </button>
        </div>

        @if($items->isEmpty())
        <div class="text-center py-12 text-slate-400">
            <i class="fa-solid fa-link text-4xl mb-3"></i>
            <p class="font-medium">No menu items yet</p>
            <p class="text-sm">Create items to add them to your menus.</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $item)
            @php
                $isSystemItem = !empty($item->is_system) || str_starts_with((string)$item->id, 'system_');
            @endphp
            <div class="flex justify-between items-center p-4 {{ $isSystemItem ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200' }} border rounded-xl hover:bg-slate-100 transition-colors group">
                <div class="flex items-center gap-3">
                    @if($item->icon)
                    <i class="{{ $item->icon }} {{ $isSystemItem ? 'text-blue-500' : 'text-slate-400' }}"></i>
                    @else
                    <i class="fa-solid fa-link text-slate-300"></i>
                    @endif
                    <div>
                        <span class="font-medium text-slate-700 block">
                            {{ $item->title }}
                            @if($isSystemItem)
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">System</span>
                            @endif
                        </span>
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
                @if(!$isSystemItem)
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="window.location.href='{{ route('journal.settings.navigation.items.edit', [$journal->slug, $item->id]) }}'"
                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" type="button">
                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                    </button>
                    <button @click="deleteItem('{{ $item->id }}')"
                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" type="button">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- MODAL: CREATE/EDIT MENU --}}
    <div x-show="showMenuModal" x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden" @click.outside="showMenuModal = false">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-lg text-slate-800">
                    <i class="fa-solid fa-bars mr-2 text-indigo-600"></i>
                    <span x-text="editingMenu ? 'Edit Menu' : 'Create Menu'"></span>
                </h3>
                <button @click="showMenuModal = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors" type="button">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="editingMenu ? '{{ route('journal.settings.navigation.menus.update', [$journal->slug, '__MENU_ID__']) }}'.replace('__MENU_ID__', editingMenu.id) : '{{ route('journal.settings.navigation.menus.store', $journal->slug) }}'" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="editingMenu ? 'PUT' : 'POST'">
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Menu Title *</label>
                        <input type="text" name="title" required
                            :value="editingMenu?.title || ''"
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g., Primary Navigation">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Active Theme Area</label>
                        <select name="area_name" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Not Assigned --</option>
                            <option value="primary" :selected="editingMenu?.area_name === 'primary'">Primary Navigation Menu (Header)</option>
                            <option value="user" :selected="editingMenu?.area_name === 'user'">User Navigation Menu (Top Right)</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Only one menu can be assigned to each area.</p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="showMenuModal = false"
                        class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="submitMenu($event.target.closest('form'))"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-check mr-2"></i>
                        <span x-text="editingMenu ? 'Save Changes' : 'Create Menu'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- MODAL: ASSIGN ITEMS TO MENU (DRAG & DROP VERSION) --}}
    <div x-show="showAssignModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">
        <div class="bg-white w-full max-w-6xl rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" @click.outside="showAssignModal = false">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-lg text-slate-800">
                    <i class="fa-solid fa-arrows-alt mr-2 text-indigo-600"></i>
                    Manage Menu Items
                </h3>
                <button @click="showAssignModal = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors" type="button">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                @foreach($menus as $menu)
                <div x-show="selectedMenuId === '{{ $menu->id }}'" class="grid grid-cols-2 gap-6">
                    {{-- Left Column: Assigned Items (Drag & Drop Zone) --}}
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 bg-gradient-to-br from-green-50 to-emerald-50 min-h-[400px]">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-sm text-slate-700 flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-600"></i>
                                Assigned Menu Items
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">
                                    @php echo $menu->assignments()->count(); @endphp items
                                </span>
                            </h4>
                            <div class="text-xs text-slate-500 flex items-center gap-1">
                                <i class="fa-solid fa-info-circle"></i>
                                Drag to reorder
                            </div>
                        </div>

                        <ul class="space-y-3 min-h-[300px]"
                            @drop.prevent="handleDrop($event, 'assigned')"
                            @dragover.prevent="handleDragOver($event, 'assigned')"
                            @dragleave.prevent="handleDragLeave($event, 'assigned')">

                            @php
                                $assignments = $menu->assignments()->with('item', 'children')->rootLevel()->ordered()->get();
                            @endphp

                            @include('journal.admin.settings.navigation._nested_assignments', ['assignments' => $assignments, 'level' => 0])

                            @if($assignments->isEmpty())
                            <li class="text-center py-12 text-slate-400 border-2 border-dashed border-slate-200 rounded-lg bg-white/50">
                                <i class="fa-solid fa-inbox text-3xl mb-3 text-slate-300"></i>
                                <p class="font-medium text-slate-500">No items assigned</p>
                                <p class="text-xs text-slate-400 mt-1">Drag items from the right panel</p>
                            </li>
                            @endif
                        </ul>
                    </div>

                    {{-- Right Column: Available Items (Drag Source) --}}
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 bg-gradient-to-br from-slate-50 to-gray-50 min-h-[400px]">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-sm text-slate-700 flex items-center gap-2">
                                <i class="fa-solid fa-list text-slate-500"></i>
                                Available Menu Items
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-full font-medium">
                                    @php
                                        $assignedItemIds = $assignments->pluck('menu_item_id')->toArray();
                                        $assignedRouteNames = $assignments->pluck('item.route_name')->filter()->toArray();
                                        $unassignedCount = $items->filter(function($item) use ($assignedItemIds, $assignedRouteNames) {
                                            $itemId = (string) $item->id;
                                            if (!str_starts_with($itemId, 'system_')) {
                                                return !in_array($item->id, $assignedItemIds);
                                            }
                                            return !in_array($item->route_name, $assignedRouteNames);
                                        })->count();
                                        echo $unassignedCount;
                                    @endphp items
                                </span>
                            </h4>
                            <div class="text-xs text-slate-500 flex items-center gap-1">
                                <i class="fa-solid fa-mouse-pointer"></i>
                                Click to add
                            </div>
                        </div>

                        <ul class="space-y-3 min-h-[300px] overflow-y-auto max-h-[350px]">
                            @php
                                $unassignedItems = $items->filter(function($item) use ($assignedItemIds, $assignedRouteNames) {
                                    $itemId = (string) $item->id;
                                    if (!str_starts_with($itemId, 'system_')) {
                                        return !in_array($item->id, $assignedItemIds);
                                    }
                                    return !in_array($item->route_name, $assignedRouteNames);
                                });
                            @endphp

                            @forelse($unassignedItems as $item)
                            @php
                                $isSystemItem = !empty($item->is_system);
                                $isVirtualItem = str_starts_with((string)$item->id, 'system_');
                            @endphp
                            <li class="bg-white border border-slate-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 group"
                                :class="{ 'ring-2 ring-indigo-400 ring-opacity-50': draggedOverItem === '{{ $item->id }}' }"
                                draggable="true"
                                data-item-id="{{ $item->id }}"
                                data-item-type="available"
                                @dragstart="handleDragStart($event, '{{ $item->id }}', 'available')"
                                @dragend="handleDragEnd($event)">

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="drag-handle cursor-grab active:cursor-grabbing p-1 text-slate-400 group-hover:text-slate-600 transition-colors">
                                            <i class="fa-solid fa-grip-vertical"></i>
                                        </div>
                                        @if($item->icon)
                                            <i class="{{ $item->icon }} {{ $isSystemItem ? 'text-blue-500' : 'text-slate-400' }} text-sm"></i>
                                        @endif
                                        <div class="flex-1">
                                            <span class="text-sm font-medium text-slate-700 block">{{ $item->title }}</span>
                                            <span class="text-xs {{ $isSystemItem ? 'text-blue-500' : 'text-slate-400' }}">
                                                {{ $isSystemItem ? 'System' : ucfirst($item->type) }}
                                            </span>
                                        </div>
                                    </div>
                                    <button @click="assignItem('{{ $menu->id }}', '{{ $item->id }}', '{{ $isVirtualItem ? $item->route_name : '' }}')"
                                        class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                        <i class="fa-solid fa-plus text-sm"></i>
                                    </button>
                                </div>
                            </li>
                            @empty
                            <li class="text-center py-12 text-slate-400 border-2 border-dashed border-slate-200 rounded-lg bg-white/50">
                                <i class="fa-solid fa-check-double text-3xl mb-3 text-green-300"></i>
                                <p class="font-medium text-green-600">All items assigned!</p>
                                <p class="text-xs text-slate-400 mt-1">Great job organizing your menu</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <div class="text-xs text-slate-500 flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-amber-500"></i>
                        <span>Drag items to reorder or drop on others to create submenus</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-level-up-alt fa-rotate-90 text-indigo-500"></i>
                        <span>Submenu items</span>
                    </div>
                </div>
                <button type="button" @click="showAssignModal = false"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

</div>

@endsection
