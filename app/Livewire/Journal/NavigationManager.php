<?php

namespace App\Livewire\Journal;

use App\Models\Journal;
use App\Models\NavigationMenu;
use App\Models\NavigationMenuItem;
use App\Models\NavigationMenuItemAssignment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
class NavigationManager extends Component
{
    public Journal $journal;

    // Modal states
    public bool $showMenuModal = false;
    public bool $showItemModal = false;

    // Menu form data
    public ?string $editingMenuId = null;
    public string $editingMenuTitle = '';
    public ?string $editingMenuArea = null;

    // Item form data
    public ?string $editingItemId = null;
    public string $editingItemTitle = '';
    public string $editingItemType = 'custom';
    public ?string $editingItemUrl = null;
    public ?string $editingItemRouteName = null;
    public ?string $editingItemIcon = null;
    public string $editingItemTarget = '_self';

    public function mount(string $journal)
    {
        $this->journal = current_journal();
    }

    // =====================================================
    // COMPUTED PROPERTIES
    // =====================================================

    public function getMenusProperty()
    {
        return NavigationMenu::where('journal_id', $this->journal->id)
            ->with(['assignments.item'])
            ->get();
    }

    public function getAllItemsProperty()
    {
        return NavigationMenuItem::where('journal_id', $this->journal->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
    }

    public function getAssignedItemsProperty()
    {
        if (!$this->editingMenuId) {
            return collect();
        }

        return NavigationMenuItemAssignment::where('menu_id', $this->editingMenuId)
            ->whereNull('parent_id')
            ->with('item')
            ->orderBy('order')
            ->get();
    }

    public function getUnassignedItemsListProperty()
    {
        if (!$this->editingMenuId) {
            return $this->allItems;
        }

        $assignedIds = NavigationMenuItemAssignment::where('menu_id', $this->editingMenuId)
            ->pluck('menu_item_id')
            ->toArray();

        return NavigationMenuItem::where('journal_id', $this->journal->id)
            ->where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->orderBy('title')
            ->get();
    }

    public function getAvailableRoutesProperty()
    {
        return NavigationMenuItem::getAvailableRoutes();
    }

    // =====================================================
    // MENU ACTIONS
    // =====================================================

    public function createMenu()
    {
        $this->resetMenuForm();
        $this->showMenuModal = true;
    }

    public function editMenu($menuId)
    {
        $menu = NavigationMenu::find($menuId);
        if (!$menu) return;

        $this->editingMenuId = $menu->id;
        $this->editingMenuTitle = $menu->title;
        $this->editingMenuArea = $menu->area_name;
        $this->showMenuModal = true;
    }

    public function saveMenu()
    {
        $this->validate([
            'editingMenuTitle' => 'required|string|max:255',
        ]);

        // If assigning an area, check if another menu has it
        if ($this->editingMenuArea) {
            $existingMenu = NavigationMenu::where('journal_id', $this->journal->id)
                ->where('area_name', $this->editingMenuArea)
                ->when($this->editingMenuId, fn($q) => $q->where('id', '!=', $this->editingMenuId))
                ->first();

            if ($existingMenu) {
                // Unassign the area from the other menu
                $existingMenu->update(['area_name' => null]);
            }
        }

        if ($this->editingMenuId) {
            NavigationMenu::where('id', $this->editingMenuId)->update([
                'title' => $this->editingMenuTitle,
                'area_name' => $this->editingMenuArea,
            ]);
        } else {
            NavigationMenu::create([
                'journal_id' => $this->journal->id,
                'title' => $this->editingMenuTitle,
                'area_name' => $this->editingMenuArea,
                'is_active' => true,
            ]);
        }

        $this->showMenuModal = false;
        $this->resetMenuForm();
    }

    public function deleteMenu($menuId)
    {
        NavigationMenu::where('id', $menuId)->delete();
    }

    protected function resetMenuForm()
    {
        $this->editingMenuId = null;
        $this->editingMenuTitle = '';
        $this->editingMenuArea = null;
    }

    // =====================================================
    // ITEM ACTIONS
    // =====================================================

    public function createItem()
    {
        $this->resetItemForm();
        $this->showItemModal = true;
    }

    public function editItem($itemId)
    {
        $item = NavigationMenuItem::find($itemId);
        if (!$item) return;

        $this->editingItemId = $item->id;
        $this->editingItemTitle = $item->title;
        $this->editingItemType = $item->type;
        $this->editingItemUrl = $item->url;
        $this->editingItemRouteName = $item->route_name;
        $this->editingItemIcon = $item->icon;
        $this->editingItemTarget = $item->target ?? '_self';
        $this->showItemModal = true;
    }

    public function saveItem()
    {
        $this->validate([
            'editingItemTitle' => 'required|string|max:255',
            'editingItemType' => 'required|in:custom,route,page',
        ]);

        $data = [
            'journal_id' => $this->journal->id,
            'title' => $this->editingItemTitle,
            'type' => $this->editingItemType,
            'url' => $this->editingItemType === 'custom' ? $this->editingItemUrl : null,
            'route_name' => $this->editingItemType === 'route' ? $this->editingItemRouteName : null,
            'icon' => $this->editingItemIcon,
            'target' => $this->editingItemTarget,
            'is_active' => true,
        ];

        if ($this->editingItemId) {
            NavigationMenuItem::where('id', $this->editingItemId)->update($data);
        } else {
            NavigationMenuItem::create($data);
        }

        $this->showItemModal = false;
        $this->resetItemForm();
    }

    public function deleteItem($itemId)
    {
        NavigationMenuItem::where('id', $itemId)->delete();
    }

    protected function resetItemForm()
    {
        $this->editingItemId = null;
        $this->editingItemTitle = '';
        $this->editingItemType = 'custom';
        $this->editingItemUrl = null;
        $this->editingItemRouteName = null;
        $this->editingItemIcon = null;
        $this->editingItemTarget = '_self';
    }

    // =====================================================
    // ASSIGNMENT ACTIONS
    // =====================================================

    public function assignItem($itemId)
    {
        if (!$this->editingMenuId) return;

        $maxOrder = NavigationMenuItemAssignment::where('menu_id', $this->editingMenuId)
            ->whereNull('parent_id')
            ->max('order') ?? 0;

        NavigationMenuItemAssignment::create([
            'menu_id' => $this->editingMenuId,
            'menu_item_id' => $itemId,
            'order' => $maxOrder + 1,
        ]);
    }

    public function unassignItem($assignmentId)
    {
        NavigationMenuItemAssignment::where('id', $assignmentId)->delete();
    }

    public function moveUp($assignmentId)
    {
        $assignment = NavigationMenuItemAssignment::find($assignmentId);
        if (!$assignment) return;

        $previous = NavigationMenuItemAssignment::where('menu_id', $assignment->menu_id)
            ->whereNull('parent_id')
            ->where('order', '<', $assignment->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previous) {
            $tempOrder = $assignment->order;
            $assignment->update(['order' => $previous->order]);
            $previous->update(['order' => $tempOrder]);
        }
    }

    public function moveDown($assignmentId)
    {
        $assignment = NavigationMenuItemAssignment::find($assignmentId);
        if (!$assignment) return;

        $next = NavigationMenuItemAssignment::where('menu_id', $assignment->menu_id)
            ->whereNull('parent_id')
            ->where('order', '>', $assignment->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($next) {
            $tempOrder = $assignment->order;
            $assignment->update(['order' => $next->order]);
            $next->update(['order' => $tempOrder]);
        }
    }

    // =====================================================
    // RENDER
    // =====================================================

    public function render()
    {
        return view('livewire.journal.navigation-manager', [
            'menus' => $this->menus,
            'allItems' => $this->allItems,
            'assignedItems' => $this->assignedItems,
            'unassignedItemsList' => $this->unassignedItemsList,
            'availableRoutes' => $this->availableRoutes,
        ])->title('Navigation Manager - ' . $this->journal->name);
    }
}
