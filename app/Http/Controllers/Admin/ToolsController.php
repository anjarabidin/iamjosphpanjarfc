<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToolsController extends Controller
{
    /**
     * Display the Tools page.
     */
    public function index()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        // Define available tools
        $tools = $this->getToolsConfig();

        return view('manager.tools.index', compact('journal', 'tools'));
    }

    /**
     * Reset article permissions to journal defaults.
     */
    public function resetPermissions(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        // Get default journal license settings from metadata
        $journalSettings = $journal->metadata ?? [];
        $defaultLicenseUrl = $journalSettings['license_url'] ?? null;
        $defaultCopyrightHolder = $journalSettings['copyright_holder'] ?? $journal->publisher ?? $journal->name;

        // Count affected submissions
        $affectedCount = 0;

        DB::transaction(function () use ($journal, $defaultLicenseUrl, $defaultCopyrightHolder, &$affectedCount) {
            // Get all published submissions for this journal
            $query = Submission::where('journal_id', $journal->id)
                ->where('status', Submission::STATUS_PUBLISHED);

            $affectedCount = $query->count();

            // Update metadata for each submission
            // Since metadata is JSONB, we need to update it properly
            $query->get()->each(function ($submission) use ($defaultLicenseUrl, $defaultCopyrightHolder) {
                $metadata = $submission->metadata ?? [];
                $metadata['license_url'] = $defaultLicenseUrl;
                $metadata['copyright_holder'] = $defaultCopyrightHolder;
                $metadata['copyright_year'] = $submission->published_at?->year ?? date('Y');

                $submission->update(['metadata' => $metadata]);
            });
        });

        return redirect()->back()->with('success', "Article permissions have been reset to journal defaults. {$affectedCount} articles updated.");
    }

    /**
     * Get tools configuration array.
     */
    private function getToolsConfig(): array
    {
        return [
            [
                'key' => 'import_oai',
                'title' => 'OAI-PMH Import',
                'description' => 'Import articles and metadata from external OAI-PMH endpoints (e.g. OJS 3.x).',
                'icon' => 'cloud-arrow-down',
                'color' => 'indigo',
                'route' => route('journal.settings.tools.import.oai.index', ['journal' => current_journal()->slug]),
                'available' => true,
            ],
            [
                'key' => 'scholar',
                'title' => 'Scholar IAMJOS Monitor',
                'description' => 'Monitor the indexing status of your articles on Google Scholar and get notified of changes.',
                'icon' => 'scholar',
                'color' => 'blue',
                'route' => route('journal.settings.stats.scholar.index', ['journal' => current_journal()->slug]),
                'available' => true,
            ],
            [
                'key' => 'native',
                'title' => 'Native XML Plugin',
                'description' => 'Import and export articles and issues in IAMJOS native XML format for backup or migration.',
                'icon' => 'code-bracket',
                'color' => 'indigo',
                'route' => route('journal.settings.tools.native.index', ['journal' => current_journal()->slug]),
                'available' => true,
            ],
            [
                'key' => 'users',
                'title' => 'Users XML Plugin',
                'description' => 'Import and export user accounts and roles in XML format for bulk management.',
                'icon' => 'users',
                'color' => 'purple',
                'route' => '#',
                'available' => true,
            ],
            [
                'key' => 'copernicus',
                'title' => 'ICI XML Exporter',
                'description' => 'Export articles and issues metadata in the format required by Index Copernicus (ICI) World of Journals.',
                'icon' => 'server-stack',
                'color' => 'indigo',
                'route' => route('journal.settings.tools.copernicus.index', ['journal' => current_journal()->slug]),
                'available' => true,
            ],
            [
                'key' => 'crossref',
                'title' => 'CrossRef XML Export',
                'description' => 'Export article metadata in CrossRef XML format for DOI registration and citation linking.',
                'icon' => 'link',
                'color' => 'blue',
                'route' => route('journal.settings.tools.crossref.index', ['journal' => current_journal()->slug]),
                'available' => true,
            ],
            [
                'key' => 'pubmed',
                'title' => 'PubMed XML Export',
                'description' => 'Export article metadata in PubMed XML format for indexing in MEDLINE/PubMed.',
                'icon' => 'beaker',
                'color' => 'emerald',
                'route' => '#',
                'available' => true,
            ],
            [
                'key' => 'doaj',
                'title' => 'DOAJ Export Plugin',
                'description' => 'Export journal metadata for the Directory of Open Access Journals (DOAJ) indexing.',
                'icon' => 'globe-alt',
                'color' => 'amber',
                'route' => '#',
                'available' => true,
            ],
            [
                'key' => 'datacite',
                'title' => 'DataCite Export/Registration',
                'description' => 'Export or register article and supplementary file metadata in DataCite format for DOIs.',
                'icon' => 'document-text',
                'color' => 'rose',
                'route' => '#',
                'available' => true,
            ],
            [
                'key' => 'quicksubmit',
                'title' => 'Quick Submit Plugin',
                'description' => 'Quickly add published articles without going through the full submission workflow.',
                'icon' => 'bolt',
                'color' => 'cyan',
                'route' => '#',
                'available' => true,
            ],
        ];
    }
}
