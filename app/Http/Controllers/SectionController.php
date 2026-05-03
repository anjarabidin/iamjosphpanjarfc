<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SectionController extends Controller
{
    /**
     * Store a newly created section.
     */
    public function store(Request $request): RedirectResponse
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'nullable|string|max:20',
            'policy' => 'nullable|string',
            'meta_indexed' => 'boolean',
            'meta_reviewed' => 'boolean',
        ]);

        // Get next sort order
        $maxOrder = Section::where('journal_id', $journal->id)->max('sort_order') ?? 0;

        Section::create([
            'journal_id' => $journal->id,
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'] ?? null,
            'policy' => $validated['policy'] ?? null,
            'meta_indexed' => $request->boolean('meta_indexed'),
            'meta_reviewed' => $request->boolean('meta_reviewed'),
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'Section created successfully.');
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, string $journal, string $sectionId): RedirectResponse
    {
        $currentJournal = current_journal();

        if (!$currentJournal) {
            abort(404, 'Journal not found.');
        }

        // Optional: Verify route param matches context
        if ($currentJournal->slug !== $journal) {
            // abort(404); // Optional Check
        }

        $section = Section::findOrFail($sectionId);

        if ($section->journal_id !== $currentJournal->id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'nullable|string|max:20',
            'policy' => 'nullable|string',
            'meta_indexed' => 'boolean',
            'meta_reviewed' => 'boolean',
        ]);

        $section->update([
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'] ?? null,
            'policy' => $validated['policy'] ?? null,
            'meta_indexed' => $request->boolean('meta_indexed'),
            'meta_reviewed' => $request->boolean('meta_reviewed'),
        ]);

        return back()->with('success', 'Section updated successfully.');
    }

    /**
     * Remove the specified section.
     */
    public function destroy(string $journal, string $sectionId): RedirectResponse
    {
        $currentJournal = current_journal();

        if (!$currentJournal) {
            abort(404, 'Journal not found.');
        }

        $section = Section::findOrFail($sectionId);

        if ($section->journal_id !== $currentJournal->id) {
            abort(403, 'Unauthorized.');
        }

        // Check if section has submissions
        if ($section->submissions()->exists()) {
            return back()->with('error', 'Cannot delete section with existing submissions.');
        }

        $section->delete();

        return back()->with('success', 'Section deleted successfully.');
    }
}
