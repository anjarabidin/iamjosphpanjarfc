<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Generate path from name if not provided
        $path = !empty($validated['path'])
            ? Str::slug($validated['path'])
            : Str::slug($validated['name']);

        // Ensure path is unique within journal
        $originalPath = $path;
        $counter = 1;
        while (Category::where('journal_id', $journal->id)->where('path', $path)->exists()) {
            $path = $originalPath . '-' . $counter++;
        }

        // Get next sort order
        $maxOrder = Category::where('journal_id', $journal->id)->max('sort_order') ?? 0;

        Category::create([
            'journal_id' => $journal->id,
            'name' => $validated['name'],
            'path' => $path,
            'description' => $validated['description'] ?? null,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, string $journal, string $categoryId): RedirectResponse
    {
        $currentJournal = current_journal();

        if (!$currentJournal) {
            abort(404, 'Journal not found.');
        }

        $category = Category::findOrFail($categoryId);

        if ($category->journal_id !== $currentJournal->id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Generate path from name if not provided
        $path = !empty($validated['path'])
            ? Str::slug($validated['path'])
            : Str::slug($validated['name']);

        // Ensure path is unique within journal (excluding current category)
        $originalPath = $path;
        $counter = 1;
        while (Category::where('journal_id', $currentJournal->id)
            ->where('path', $path)
            ->where('id', '!=', $category->id)
            ->exists()
        ) {
            $path = $originalPath . '-' . $counter++;
        }

        $category->update([
            'name' => $validated['name'],
            'path' => $path,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(string $journal, string $categoryId): RedirectResponse
    {
        $currentJournal = current_journal();

        if (!$currentJournal) {
            abort(404, 'Journal not found.');
        }

        $category = Category::findOrFail($categoryId);

        if ($category->journal_id !== $currentJournal->id) {
            abort(403, 'Unauthorized.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
