<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistributionSettingsController extends Controller
{
    /**
     * Show the distribution settings page.
     */
    public function edit(): View
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        // Authorization check (e.g., policy) should be here
        // $this->authorize('update', $journal); 

        return view('admin.journals.distribution', compact('journal'));
    }

    /**
     * Update the distribution settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        // $this->authorize('update', $journal);

        $validated = $request->validate([
            'license.copyright_holder_type' => 'required|in:author,context,other',
            'license.copyright_holder_other' => 'nullable|string|max:255',
            'license.url' => 'nullable|url|max:255',
            'license.terms' => 'nullable|string',
            'license.copyright_year' => 'nullable|in:issue,article',

            'indexing.description' => 'nullable|string|max:500',
            'indexing.custom_tags' => 'nullable|string', // Intentionally allowing raw HTML meta tags, sanitization might be needed on output or restricted here if strict security

            'access.open_access_policy' => 'nullable|string',
            'access.enable_oai' => 'boolean',

            'archiving.lockss' => 'boolean',
            'archiving.clockss' => 'boolean',
            'archiving.policy' => 'nullable|string',
        ]);

        $journal->update([
            'copyright_holder_type' => $validated['license']['copyright_holder_type'],
            'copyright_holder_other' => $validated['license']['copyright_holder_other'] ?? null,
            'license_url' => $validated['license']['url'] ?? null,
            'license_terms' => $validated['license']['terms'] ?? null,
            'copyright_year_basis' => $validated['license']['copyright_year'] ?? 'issue',

            'search_description' => $validated['indexing']['description'] ?? null,
            'custom_headers' => $validated['indexing']['custom_tags'] ?? null,

            'open_access_policy' => $validated['access']['open_access_policy'] ?? null,
            'enable_oai' => $request->boolean('access.enable_oai'),

            'enable_lockss' => $request->boolean('archiving.lockss'),
            'enable_clockss' => $request->boolean('archiving.clockss'),
            'archiving_policy' => $validated['archiving']['policy'] ?? null,
        ]);

        return back()->with('success', 'Distribution settings updated.');
    }
}
