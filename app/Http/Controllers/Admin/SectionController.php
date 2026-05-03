<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SectionController extends Controller
{
    /**
     * Get the current journal from context.
     */
    protected function getJournal(): Journal
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal context not found.');
        }

        return $journal;
    }

    /**
     * Display a listing of sections.
     */
    public function index(): View
    {
        $journal = $this->getJournal();

        $sections = Section::where('journal_id', $journal->id)
            ->withCount('submissions')
            ->ordered()
            ->get();

        return view('admin.sections.index', compact('sections', 'journal'));
    }

    /**
     * Show the form for creating a new section.
     */
    public function create(): View
    {
        $journal = $this->getJournal();

        return view('admin.sections.create', compact('journal'));
    }

    /**
     * Store a newly created section.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'nullable|string|max:10',
            'policy' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $journal = $this->getJournal();

        // Get next sort order
        $maxOrder = Section::where('journal_id', $journal->id)->max('sort_order') ?? 0;

        Section::create([
            'journal_id' => $journal->id,
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'],
            'policy' => $validated['policy'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('journal.admin.sections.index', ['journal' => $journal->slug])
            ->with('success', 'Section created successfully.');
    }

    /**
     * Show the form for editing the section.
     */
    public function edit(string $journalSlug, Section $section): View
    {
        $journal = $this->getJournal();

        // Ensure section belongs to this journal
        if ($section->journal_id !== $journal->id) {
            abort(404);
        }

        return view('admin.sections.edit', compact('section', 'journal'));
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, string $journalSlug, Section $section): RedirectResponse
    {
        $journal = $this->getJournal();

        // Ensure section belongs to this journal
        if ($section->journal_id !== $journal->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'nullable|string|max:10',
            'policy' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $section->update([
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'],
            'policy' => $validated['policy'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('journal.admin.sections.index', ['journal' => $journal->slug])
            ->with('success', 'Section updated successfully.');
    }

    /**
     * Update section order (via AJAX).
     */
    public function reorder(Request $request): RedirectResponse
    {
        $journal = $this->getJournal();

        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*' => 'uuid',
        ]);

        foreach ($validated['sections'] as $index => $sectionId) {
            // Only update sections that belong to this journal
            Section::where('id', $sectionId)
                ->where('journal_id', $journal->id)
                ->update(['sort_order' => $index]);
        }

        return back()->with('success', 'Section order updated.');
    }

    /**
     * Remove the specified section (soft delete).
     */
    public function destroy(string $journalSlug, Section $section): RedirectResponse
    {
        $journal = $this->getJournal();

        // Ensure section belongs to this journal
        if ($section->journal_id !== $journal->id) {
            abort(404);
        }

        if ($section->submissions()->exists()) {
            return back()->with('error', 'Cannot delete section with existing submissions.');
        }

        $section->delete();

        return redirect()->route('journal.admin.sections.index', ['journal' => $journal->slug])
            ->with('success', 'Section deleted successfully.');
    }
}
