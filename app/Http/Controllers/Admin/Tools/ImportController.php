<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Jobs\ImportOaiBatchJob;
use App\Models\Journal;
use App\Services\OaiHarvesterService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    protected $oaiService;

    public function __construct(OaiHarvesterService $oaiService)
    {
        $this->oaiService = $oaiService;
    }

    /**
     * Show the Import OAI Page (Journal Scoped)
     */
    public function index($journal)
    {
        // Journal is already resolved by middleware/route binding, check if it's the model or slug
        if (!($journal instanceof Journal)) {
             $journal = Journal::where('slug', $journal)->firstOrFail();
        }

        // Validate permission (optional if middleware handles it, but good practice)
        // $this->authorize('manage', $journal); 

        // Get sections for THIS journal only
        $sections = $journal->sections()->orderBy('name')->get();

        return view('journal.settings.tools.import_oai', compact('journal', 'sections'));
    }

    /**
     * Preview Import (Validate & Count)
     */
    public function preview(Request $request, $journal)
    {
        if (!($journal instanceof Journal)) {
             $journal = Journal::where('slug', $journal)->firstOrFail();
        }

        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $url = $request->input('url');
            $this->oaiService->validateUrl($url);
            $count = $this->oaiService->countRecords($url);
            
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "Found approximately {$count} records."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Start Harvest Job
     */
    public function harvest(Request $request, $journal)
    {
        if (!($journal instanceof Journal)) {
             $journal = Journal::where('slug', $journal)->firstOrFail();
        }

        $request->validate([
            'url' => 'required|url',
            'section_id' => 'required|exists:sections,id',
        ]);

        try {
            $url = $request->input('url');
            $sectionId = $request->input('section_id');

            // Verify section belongs to journal
            $section = $journal->sections()->findOrFail($sectionId);

            // Double check validation
            $this->oaiService->validateUrl($url);

            // Dispatch Job with JOURNAL ID
            ImportOaiBatchJob::dispatch($url, $journal->id, $section->id, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Harvesting started in the background (Journal: ' . $journal->abbreviation . ').'
            ]);

        } catch (\Exception $e) {
             return response()->json([
                'success' => false,
                'message' => "Failed to start harvest: " . $e->getMessage()
            ], 500);
        }
    }
}
