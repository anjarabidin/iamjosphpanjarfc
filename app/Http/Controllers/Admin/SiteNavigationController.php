<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationMenu;
use App\Models\NavigationMenuItem;
use App\Models\NavigationMenuItemAssignment;
use App\Models\SitePage;
use App\View\Composers\SiteLayoutComposer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * SiteNavigationController
 * 
 * Manages navigation menus for the portal (site-level).
 * Site menus have journal_id = NULL to distinguish from journal menus.
 */
class SiteNavigationController extends Controller
{
    /**
     * Display navigation manager page for site
     */
    public function index(): View
    {
        // Get or create default menus for site (journal_id = null)
        $menus = collect();
        foreach (NavigationMenu::getLocations() as $location => $name) {
            $menu = NavigationMenu::firstOrCreate(
                ['journal_id' => null, 'area_name' => $location],
                ['title' => $name, 'is_active' => true]
            );
            $menus->push($menu->load(['items' => fn($q) => $q->orderBy('order')]));
        }

        // Available routes for creating route-type items
        $availableRoutes = $this->getAvailableRoutes();

        // Get site pages for linking
        $sitePages = SitePage::published()->ordered()->get(['id', 'title', 'slug']);

        return view('admin.site-navigation.index', compact('menus', 'availableRoutes', 'sitePages'));
    }

    /**
     * Store a new navigation item
     */
    public function storeItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menu_id' => 'required|uuid|exists:navigation_menus,id',
            'parent_id' => 'nullable|uuid|exists:navigation_menu_items,id',
            'label' => 'required|string|max:255',
            'type' => 'required|in:custom,page,route,divider',
            'url' => 'nullable|string|max:500',
            'route_name' => 'nullable|string|max:255',
            'route_params' => 'nullable|array',
            'page_id' => 'nullable|uuid|exists:site_pages,id',
            'icon' => 'nullable|string|max:100',
            'target' => 'in:_self,_blank',
        ]);

        // Verify menu belongs to site (not a journal)
        $menu = NavigationMenu::find($validated['menu_id']);
        if ($menu->journal_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add items to journal menus from here.',
            ], 403);
        }

        // Get next order
        $maxOrder = NavigationMenuItem::where('menu_id', $validated['menu_id'])
            ->where('parent_id', $validated['parent_id'] ?? null)
            ->max('order') ?? 0;

        // Handle page type - convert page_id to url
        if ($validated['type'] === 'page' && !empty($validated['page_id'])) {
            $page = SitePage::find($validated['page_id']);
            if ($page) {
                $validated['url'] = route('site.page', $page->slug);
                $validated['route_name'] = 'site.page';
                $validated['route_params'] = ['slug' => $page->slug];
            }
        }

        $item = NavigationMenuItem::create([
            'journal_id' => null, // Site level
            'title' => $validated['label'],
            'type' => $validated['type'],
            'url' => $validated['url'] ?? null,
            'route_name' => $validated['route_name'] ?? null,
            'path' => $validated['route_params'] ?? null,
            'related_id' => $validated['page_id'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'target' => $validated['target'] ?? '_self',
            'is_active' => true,
        ]);

        // Create assignment to the menu
        NavigationMenuItemAssignment::create([
            'menu_id' => $validated['menu_id'],
            'menu_item_id' => $item->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        // Clear navigation cache
        SiteLayoutComposer::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully.',
            'item' => $item,
        ]);
    }

    /**
     * Update a navigation item
     */
    public function updateItem(Request $request, NavigationMenuItem $item): JsonResponse
    {
        // Verify item belongs to a site menu
        if ($item->journal_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit journal menu items from here.',
            ], 403);
        }

        $validated = $request->validate([
            'label' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|in:custom,page,route,divider',
            'url' => 'nullable|string|max:500',
            'route_name' => 'nullable|string|max:255',
            'route_params' => 'nullable|array',
            'page_id' => 'nullable|uuid|exists:site_pages,id',
            'icon' => 'nullable|string|max:100',
            'target' => 'in:_self,_blank',
            'is_active' => 'boolean',
        ]);

        // Handle page type - convert page_id to url
        if (isset($validated['type']) && $validated['type'] === 'page' && !empty($validated['page_id'])) {
            $page = SitePage::find($validated['page_id']);
            if ($page) {
                $validated['url'] = route('site.page', $page->slug);
                $validated['route_name'] = 'site.page';
                $validated['path'] = ['slug' => $page->slug];
            }
        }

        unset($validated['page_id']); // Remove before update

        // Map label to title
        if (isset($validated['label'])) {
            $validated['title'] = $validated['label'];
            unset($validated['label']);
        }

        // Map route_params to path
        if (isset($validated['route_params'])) {
            $validated['path'] = $validated['route_params'];
            unset($validated['route_params']);
        }

        $item->update($validated);

        // Clear navigation cache
        SiteLayoutComposer::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully.',
            'item' => $item->fresh(),
        ]);
    }

    /**
     * Delete a navigation item
     */
    public function destroyItem(NavigationMenuItem $item): JsonResponse
    {
        // Verify item belongs to a site menu
        if ($item->journal_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete journal menu items from here.',
            ], 403);
        }

        $item->delete();

        // Clear navigation cache
        SiteLayoutComposer::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully.',
        ]);
    }

    /**
     * Reorder navigation items (drag-and-drop)
     */
    public function reorderItems(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|uuid|exists:navigation_menu_item_assignments,id',
            'items.*.order' => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|uuid|exists:navigation_menu_item_assignments,id',
        ]);

        foreach ($validated['items'] as $itemData) {
            NavigationMenuItemAssignment::where('id', $itemData['id'])->update([
                'order' => $itemData['order'],
                'parent_id' => $itemData['parent_id'] ?? null,
            ]);
        }

        // Clear navigation cache
        SiteLayoutComposer::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Menu items reordered successfully.',
        ]);
    }

    /**
     * Get available routes for site menu items
     */
    protected function getAvailableRoutes(): array
    {
        return [
            ['name' => 'portal.home', 'label' => 'Homepage', 'params' => []],
            ['name' => 'portal.journals', 'label' => 'Journals', 'params' => []],
            ['name' => 'portal.about', 'label' => 'About', 'params' => []],
            ['name' => 'portal.contact', 'label' => 'Contact', 'params' => []],
            ['name' => 'login', 'label' => 'Login', 'params' => []],
            ['name' => 'register', 'label' => 'Register', 'params' => []],
        ];
    }
}
