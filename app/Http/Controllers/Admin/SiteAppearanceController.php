<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\SiteContentBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * SiteAppearanceController
 * 
 * Manages the "Page Builder" interface for the portal landing page.
 * Allows Site Admin to enable/disable, reorder, and configure blocks.
 */
class SiteAppearanceController extends Controller
{
    /**
     * Display the appearance settings page.
     */
    public function index()
    {
        $blocks = SiteContentBlock::ordered()->get();

        // Group blocks by category for the admin UI
        $blocksByCategory = $blocks->groupBy('category');

        // Fetch all journals for Featured Journals block selection
        $journals = Journal::select('id', 'name', 'abbreviation', 'slug', 'logo_path')
            ->where('enabled', true)
            ->orderBy('name')
            ->get();

        return view('admin.site-appearance.index', compact('blocks', 'blocksByCategory', 'journals'));
    }

    /**
     * Update block order via AJAX (Drag & Drop)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'blocks' => 'required|array',
            'blocks.*.id' => 'required|exists:site_content_blocks,id',
            'blocks.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->blocks as $blockData) {
            SiteContentBlock::where('id', $blockData['id'])
                ->update(['sort_order' => $blockData['sort_order']]);
        }

        SiteContentBlock::clearCache();

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }

    /**
     * Toggle block active state via AJAX
     */
    public function toggle(SiteContentBlock $block)
    {
        $block->is_active = !$block->is_active;
        $block->save();

        return response()->json([
            'success' => true,
            'is_active' => $block->is_active,
            'message' => $block->is_active ? 'Block enabled' : 'Block disabled'
        ]);
    }

    /**
     * Show block configuration form
     */
    public function edit(SiteContentBlock $block)
    {
        $stats = [];
        if ($block->key === 'stats_counter') {
            // Example stats: total journals, submissions, users
            $stats['journals'] = \App\Models\Journal::where('enabled', true)->count();
            $stats['submissions'] = \DB::table('submissions')->count();
            $stats['users'] = \DB::table('users')->count();
            // Add more stats as needed
        }
        return view('admin.site-appearance.edit-block', compact('block', 'stats'));
    }

    /**
     * Update block configuration
     */
    public function update(Request $request, SiteContentBlock $block)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'config' => 'nullable|array',
        ]);

        $block->title = $request->input('title', $block->title);

        // Merge new config with existing
        $existingConfig = $block->config ?? [];
        $newConfig = $request->input('config', []);
        $block->config = array_merge($existingConfig, $newConfig);

        // Handle file uploads in config
        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('site/appearance', 'public');
            $block->setConfig('background_image', $path);
        }

        if ($request->hasFile('logos')) {
            $logos = $block->getConfig('logos', []);
            foreach ($request->file('logos') as $logo) {
                $path = $logo->store('site/logos', 'public');
                $logos[] = $path;
            }
            // Remove duplicates and re-index array
            $logos = array_values(array_unique($logos));
            $block->setConfig('logos', $logos);
        }

        $block->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Block updated successfully']);
        }

        return redirect()
            ->route('admin.site.appearance.index')
            ->with('success', 'Block updated successfully');
    }

    /**
     * Get block configuration as JSON (for modal editing)
     */
    public function getConfig(SiteContentBlock $block)
    {
        $data = [
            'id' => $block->id,
            'key' => $block->key,
            'title' => $block->title,
            'description' => $block->description,
            'config' => $block->config ?? [],
            'is_active' => $block->is_active,
        ];

        // For featured_journals block, include journals list
        if ($block->key === 'featured_journals') {
            $data['journals'] = Journal::select('id', 'name', 'abbreviation', 'slug', 'logo_path')
                ->where('enabled', true)
                ->orderBy('name')
                ->get();
        }

        // For statistics_counter block, include portal stats
        if ($block->key === 'stats_counter') {
            $data['stats'] = [
                'journals' => \App\Models\Journal::where('enabled', true)->count(),
                'submissions' => \DB::table('submissions')->count(),
                'users' => \DB::table('users')->count(),
                // Add more stats as needed
            ];
        }

        // For subject_categories block, ensure categories array exists
        if ($block->key === 'subject_categories') {
            $data['categories'] = $block->getConfig('categories', []);
        }

        return response()->json($data);
    }

    /**
     * Update block configuration via AJAX
     */
    public function updateConfig(Request $request, SiteContentBlock $block)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'config.featured_ids' => 'nullable|array',
            'config.featured_ids.*' => 'exists:journals,id',
            'config.categories' => 'nullable|array',
            'config.categories.*.name' => 'required|string|max:255',
            'config.categories.*.icon' => 'required|string|max:255',
            'config.categories.*.color' => 'required|string|in:blue,red,green,purple,amber,indigo,pink,gray',
        ]);

        if ($request->has('title')) {
            $block->title = $request->input('title');
        }

        if ($request->has('config')) {
            // Merge config but ensure arrays are replaced, not merged
            $existingConfig = $block->config ?? [];
            $newConfig = $request->input('config');
            
            // For array values like featured_ids, replace entirely instead of merging
            foreach ($newConfig as $key => $value) {
                $existingConfig[$key] = $value;
            }
            
            $block->config = $existingConfig;
        }

        $block->save();
        SiteContentBlock::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Configuration updated',
            'block' => $block
        ]);
    }

    /**
     * Delete a logo from indexing partners
     */
    public function deleteLogo(Request $request, SiteContentBlock $block)
    {
        $path = $request->input('path');
        $logos = $block->getConfig('logos', []);

        // Remove from storage
        Storage::disk('public')->delete($path);

        // Remove from config
        $logos = array_filter($logos, fn($l) => $l !== $path);
        $block->setConfig('logos', array_values($logos));
        $block->save();

        return response()->json(['success' => true]);
    }

    /**
     * Reset block to default configuration
     */
    public function reset(SiteContentBlock $block)
    {
        // Re-run the seeder for this specific block
        $seeder = new \Database\Seeders\SiteContentBlockSeeder();
        
        // Get default config from seeder (you'd need to expose this)
        // For now, just clear custom config
        $block->config = [];
        $block->save();

        return response()->json([
            'success' => true,
            'message' => 'Block reset to defaults'
        ]);
    }
}
