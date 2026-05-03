<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\SidebarBlock;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SidebarController extends Controller
{
    /**
     * Display sidebar manager page
     */
    public function index(): View
    {
        $journal = current_journal();

        // Get all sidebar blocks for this journal
        $activeBlocks = SidebarBlock::forJournal($journal->id)
            ->where('is_active', true)
            ->ordered()
            ->get();

        $inactiveBlocks = SidebarBlock::forJournal($journal->id)
            ->where('is_active', false)
            ->ordered()
            ->get();

        // Get available system blocks that haven't been added yet
        $existingSystemComponents = SidebarBlock::forJournal($journal->id)
            ->where('type', 'system')
            ->pluck('component_name')
            ->toArray();

        $availableSystemBlocks = collect(SidebarBlock::getSystemBlocks())
            ->filter(fn($block) => !in_array($block['component'], $existingSystemComponents))
            ->toArray();

        return view('journal.admin.settings.sidebar', compact(
            'journal',
            'activeBlocks',
            'inactiveBlocks',
            'availableSystemBlocks'
        ));
    }

    /**
     * Store a new sidebar block
     */
    /**
     * Store a new sidebar block
     */
    public function store(Request $request): JsonResponse
    {
        $journal = current_journal();

        $validated = $request->validate([
            'type' => 'required|in:block,page',
            'title' => 'required|string|max:255',
            'sidebar_html' => 'nullable|string', // Teaser/Sidebar Content
            'full_page_html' => 'nullable|string', // Full Page Content
            // Keep legacy content support if needed, or just map above
            'component_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'position' => 'in:left,right',
            'settings' => 'nullable|array',
            'slug' => ($request->type === 'page') ? 'required|string|max:255|alpha_dash' : 'nullable|string|max:255|alpha_dash',
            'show_title' => 'boolean',
        ]);

        if ($validated['type'] === 'page') {
             // Check unique slug per journal
             $exists = SidebarBlock::where('journal_id', $journal->id)
                ->where('slug', $validated['slug'])
                ->exists();
             if ($exists) {
                 return response()->json(['message' => 'The page URL (slug) is already in use.'], 422);
             }
        }

        // Map content based on type
        $content = null;
        $sidebarContent = null;

        if ($validated['type'] === 'page') {
            $content = $validated['full_page_html'] ?? '';
            $sidebarContent = $validated['sidebar_html'] ?? '';
        } else {
            // Block type: content column holds the sidebar HTML
            $content = $validated['sidebar_html'] ?? ''; // Map sidebar_html to content
            $sidebarContent = null;
        }

        // Get next order
        $maxOrder = SidebarBlock::forJournal($journal->id)
            ->where('position', $validated['position'] ?? 'right')
            ->max('order') ?? 0;

        $block = SidebarBlock::create([
            'journal_id' => $journal->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $content,
            'sidebar_content' => $sidebarContent,
            'slug' => $validated['type'] === 'page' ? $validated['slug'] : null,
            'show_title' => $validated['show_title'] ?? true,
            'component_name' => $validated['component_name'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'settings' => $validated['settings'] ?? null,
            'position' => $validated['position'] ?? 'right',
            'order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sidebar block created successfully.',
            'block' => $block,
        ]);
    }

    /**
     * Update a sidebar block
     */
    public function update(Request $request, string $journal, SidebarBlock $block): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'sidebar_html' => 'nullable|string',
            'full_page_html' => 'nullable|string',
             // Legacy
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'type' => 'sometimes|in:block,page',
            'slug' => ($request->input('type', $block->type) === 'page') 
                ? 'required|string|max:255|alpha_dash' 
                : 'nullable|string|max:255|alpha_dash',
            'show_title' => 'boolean',
        ]);

        if (isset($validated['type']) && $validated['type'] === 'page' && isset($validated['slug'])) {
            // Unique check excluding current block
            $exists = SidebarBlock::where('journal_id', $block->journal_id)
               ->where('slug', $validated['slug'])
               ->where('id', '!=', $block->id)
               ->exists();
            if ($exists) {
                return response()->json(['message' => 'The page URL (slug) is already in use.'], 422);
            }
        }

        // Prepare update data
        $updateData = collect($validated)->except(['sidebar_html', 'full_page_html', 'content'])->toArray();

        // Handle Content Mapping
        // If sidebar_html is present in request, we assume it's a new format update
        if ($request->has('sidebar_html') || $request->has('full_page_html')) {
            $type = $validated['type'] ?? $block->type;
            
            if ($type === 'page') {
                $updateData['content'] = $request->input('full_page_html', $block->content);
                $updateData['sidebar_content'] = $request->input('sidebar_html', $block->sidebar_content);
            } else {
                $updateData['content'] = $request->input('sidebar_html', $block->content);
                $updateData['sidebar_content'] = null;
            }
        } elseif ($request->has('content')) {
            // Legacy/Direct update fallback
            $updateData['content'] = $request->input('content');
        }

        $block->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Sidebar block updated successfully.',
            'block' => $block->fresh(),
        ]);
    }

    /**
     * Delete a sidebar block
     */
    public function destroy(string $journal, SidebarBlock $block): JsonResponse
    {
        $block->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sidebar block deleted successfully.',
        ]);
    }

    /**
     * Toggle active status of a sidebar block
     */
    public function toggle(string $journal, SidebarBlock $block): JsonResponse
    {
        $block->update(['is_active' => !$block->is_active]);

        return response()->json([
            'success' => true,
            'message' => $block->is_active ? 'Block activated.' : 'Block deactivated.',
            'block' => $block->fresh(),
        ]);
    }

    /**
     * Reorder sidebar blocks (drag-and-drop)
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'blocks' => 'required|array',
            'blocks.*.id' => 'required|uuid|exists:sidebar_blocks,id',
            'blocks.*.order' => 'required|integer|min:0',
            'blocks.*.is_active' => 'required|boolean',
        ]);

        foreach ($validated['blocks'] as $blockData) {
            SidebarBlock::where('id', $blockData['id'])->update([
                'order' => $blockData['order'],
                'is_active' => $blockData['is_active'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sidebar blocks reordered successfully.',
        ]);
    }

    /**
     * Add a system block
     */
    public function addSystemBlock(Request $request): JsonResponse
    {
        $journal = current_journal();

        $validated = $request->validate([
            'block_key' => 'required|string',
        ]);

        $systemBlocks = SidebarBlock::getSystemBlocks();

        if (!isset($systemBlocks[$validated['block_key']])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid system block.',
            ], 400);
        }

        $blockInfo = $systemBlocks[$validated['block_key']];

        // Check if already exists
        $exists = SidebarBlock::forJournal($journal->id)
            ->where('component_name', $blockInfo['component'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This block already exists.',
            ], 400);
        }

        // Get next order
        $maxOrder = SidebarBlock::forJournal($journal->id)
            ->where('position', 'right')
            ->max('order') ?? 0;

        $block = SidebarBlock::create([
            'journal_id' => $journal->id,
            'type' => 'system',
            'title' => $blockInfo['name'],
            'component_name' => $blockInfo['component'],
            'icon' => $blockInfo['icon'],
            'position' => 'right',
            'order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'System block added successfully.',
            'block' => $block,
        ]);
    }
}
