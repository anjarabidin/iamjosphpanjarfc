<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Services\DoiService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * DOI Settings Controller
 * 
 * Handles DOI (Digital Object Identifier) configuration for journals.
 * Implements OJS 3.3 DOI Plugin settings interface with standard Laravel MVC.
 */
class DoiSettingsController extends Controller
{
    /**
     * Display the DOI settings form.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        return view('journal.admin.settings.doi', compact('journal'));
    }

    /**
     * Update DOI settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        // Validate request
        $validated = $request->validate([
            'doi_objects' => 'nullable|array',
            'doi_objects.*' => 'string|in:issues,articles,galleys',
            'doi_prefix' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && !str_starts_with($value, '10.')) {
                        $fail('DOI prefix must start with "10."');
                    }
                },
            ],
            'doi_suffix_type' => [
                'required',
                Rule::in(['default', 'manual', 'custom_pattern']),
            ],
            'doi_custom_pattern' => [
                'nullable',
                'required_if:doi_suffix_type,custom_pattern',
                'string',
                'max:255',
            ],
        ], [
            'doi_prefix.required' => 'DOI prefix is required.',
            'doi_prefix.starts_with' => 'DOI prefix must start with "10."',
            'doi_suffix_type.required' => 'Please select a DOI suffix generation method.',
            'doi_suffix_type.in' => 'Invalid DOI suffix type selected.',
            'doi_custom_pattern.required_if' => 'Custom pattern is required when using custom pattern mode.',
        ]);

        // Determine if DOI should be enabled (based on whether prefix is provided)
        $doiObjects = $request->input('doi_objects', []);
        $doiPrefix = $request->input('doi_prefix');
        $isDoisEnabled = !empty($doiObjects) && !empty($doiPrefix);

        // Update journal settings
        $journal->update([
            'doi_enabled' => $isDoisEnabled,
            'doi_objects' => $doiObjects,
            'doi_prefix' => $doiPrefix,
            'doi_suffix_type' => $request->input('doi_suffix_type'),
            'doi_custom_pattern' => $request->input('doi_custom_pattern'),
        ]);

        return redirect()
            ->route('journal.settings.doi.edit', ['journal' => $journal->slug])
            ->with('success', 'DOI settings saved successfully.');
    }

    /**
     * Reassign (regenerate) all DOIs for the journal.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reassign(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        // Check if DOIs are enabled
        if (!$journal->doi_enabled) {
            return redirect()
                ->route('journal.settings.doi.edit', ['journal' => $journal->slug])
                ->with('error', 'DOI feature is not enabled. Please configure DOI settings first.');
        }

        // Call service to regenerate all DOIs
        $stats = DoiService::regenerateAll($journal);

        $message = sprintf(
            'DOI reassignment completed. Success: %d, Failed: %d, Skipped: %d',
            $stats['success'],
            $stats['failed'],
            $stats['skipped']
        );

        $type = $stats['failed'] > 0 ? 'warning' : 'success';

        return redirect()
            ->route('journal.settings.doi.edit', ['journal' => $journal->slug])
            ->with($type, $message);
    }

    /**
     * Preview DOI format based on current settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            return response()->json(['error' => 'Journal not found'], 404);
        }

        $prefix = $request->input('doi_prefix', $journal->doi_prefix ?? '10.xxxx');
        $suffixType = $request->input('doi_suffix_type', 'default');
        $customPattern = $request->input('doi_custom_pattern', '%j.v%vi%i.%a');

        // Generate example suffix
        $exampleSuffix = match ($suffixType) {
            'default' => sprintf('%s.v1i1.100', $journal->path),
            'manual' => '[manually-entered-suffix]',
            'custom_pattern' => str_replace(
                ['%j', '%v', '%i', '%Y', '%a'],
                [$journal->path, '1', '1', date('Y'), '100'],
                $customPattern
            ),
            default => sprintf('%s.v1i1.100', $journal->path),
        };

        return response()->json([
            'preview' => "{$prefix}/{$exampleSuffix}",
            'prefix' => $prefix,
            'suffix' => $exampleSuffix,
        ]);
    }
}
