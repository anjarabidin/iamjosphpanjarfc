<?php

namespace App\View\Composers;

use App\Models\SidebarBlock;
use Illuminate\View\View;

class PublicSidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // 1. Try to get the parameter from the route (usually 'journal' or 'slug')
        // We use 'journal' as the default standard key.
        $routeParam = request()->route('journal'); 

        $journal = null;

        // CASE A: It's already a Journal Model (Route Model Binding is working)
        if ($routeParam instanceof \App\Models\Journal) {
            $journal = $routeParam;
        } 
        // CASE B: It's a String/Slug (e.g., "jco") -> Query the DB
        elseif (is_string($routeParam)) {
            $journal = \App\Models\Journal::where('path', $routeParam)
                        ->orWhere('slug', $routeParam) // Fallback check
                        ->first();
        }
        // CASE C: Fallback - Check the first URL segment if route param failed
        elseif (!$routeParam) {
             $segment = request()->segment(1); // e.g. iamjos.test/jco/...
             if ($segment && !in_array($segment, ['login', 'register', 'admin', 'settings'])) {
                 $journal = \App\Models\Journal::where('path', $segment)
                    ->orWhere('slug', $segment)
                    ->first();
             }
        }

        // 2. Safety Check: If still no journal, pass empty collection and exit
        if (!$journal) {
            $view->with('sidebarBlocks', collect());
            // Also share empty currentJournal to prevent errors if view uses it
            $view->with('currentJournal', null);
            return;
        }

        // 3. Fetch Blocks using the ID we found
        $sidebarBlocks = SidebarBlock::where('journal_id', $journal->id)
            ->where('is_active', true)
            ->orderBy('order', 'asc') // DB column is 'order'
            ->get();

        // 4. Transform/Map the blocks (Compatibility fix)
        $sidebarBlocks = $sidebarBlocks->map(function ($block) {
            if ($block->type === 'system' && str_starts_with($block->component_name, 'sidebar.')) {
                $block->component_name = str_replace('sidebar.', 'public.blocks.', $block->component_name);
                
                if (str_contains($block->component_name, 'submit-block')) {
                     $block->component_name = str_replace('submit-block', 'make-submission-block', $block->component_name);
                }
            }
            return $block;
        });

        // 5. Share data
        $view->with([
            'sidebarBlocks' => $sidebarBlocks,
            'currentJournal' => $journal, // Also share the journal object itself just in case
        ]);
    }
}
