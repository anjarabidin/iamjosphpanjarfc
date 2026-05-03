<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class JournalController extends Controller
{
    /**
     * Display all journals (Super Admin only).
     */
    public function index(): View
    {
        $journals = Journal::withCount(['submissions', 'issues'])
            ->orderBy('name')
            ->get();

        return view('admin.journals.index', compact('journals'));
    }

    /**
     * Show the form for creating a new journal.
     */
    public function create(): View
    {
        return view('admin.journals.create');
    }

    /**
     * Store a newly created journal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'publisher' => 'nullable|string|max:255',
            'issn_print' => 'nullable|string|max:20',
            'issn_online' => 'nullable|string|max:20',
            'url_issn_print' => 'nullable|string|max:255',
            'url_issn_online' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        // Generate slug from name
        $slug = Str::slug($validated['abbreviation'] ?? $validated['name']);

        // Ensure slug is unique
        $originalSlug = $slug;
        $count = 1;
        while (Journal::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        try {
            DB::beginTransaction();

            $journal = Journal::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'path' => $slug,
                'abbreviation' => $validated['abbreviation'],
                'description' => $validated['description'],
                'publisher' => $validated['publisher'],
                'issn_print' => $validated['issn_print'],
                'url_issn_print' => $validated['url_issn_print'] ?? null,
                'issn_online' => $validated['issn_online'],
                'url_issn_online' => $validated['url_issn_online'] ?? null,
                'enabled' => true,
                'visible' => true,
            ]);

            // Upload logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store("journals/{$journal->id}", 'public');
                $journal->update(['logo_path' => $path]);
            }

            // Seed default roles for the newly created journal
            Role::seedDefaultRolesForJournal($journal);

            DB::commit();

            return redirect()->route('admin.journals.index')
                ->with('success', 'Journal created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create journal: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the journal.
     * This can be called from global admin or journal-scoped admin.
     */
    public function edit(?string $journalSlugOrId = null): View
    {
        // Check if we're in journal context (called from journal-scoped route)
        $journal = current_journal();

        if (!$journal && $journalSlugOrId) {
            // Called from global admin route
            $journal = Journal::findOrFail($journalSlugOrId);
        }

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        return view('admin.journals.edit', compact('journal'));
    }

    /**
     * Display the journal settings page (Journal Manager level).
     */
    public function settings(): View
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        // Fetch sections and categories for this journal
        $sections = $journal->sections()->ordered()->get();
        $categories = \App\Models\Category::where('journal_id', $journal->id)->ordered()->get();

        return view('admin.journals.settings', compact('journal', 'sections', 'categories'));
    }

    /**
     * Update the journal settings.
     */
    public function update(Request $request, ?string $journalSlugOrId = null): RedirectResponse
    {
        // Get journal from context or parameter
        $journal = current_journal();

        if (!$journal && $journalSlugOrId) {
            $journal = Journal::findOrFail($journalSlugOrId);
        }

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'summary' => 'nullable|string',
            'about' => 'nullable|string',
            // 'editorial_team_description' => 'nullable|string',
            'publisher' => 'nullable|string|max:255',
            'issn_print' => 'nullable|string|max:20',
            'issn_online' => 'nullable|string|max:20',
            'url_issn_print' => 'nullable|string|max:255',
            'url_issn_online' => 'nullable|string|max:255',
            'show_summary' => 'boolean',
            'enabled' => 'boolean',
            'visible' => 'boolean',
            'logo' => 'nullable|image|max:2048',
            'thumbnail' => 'nullable|image|max:1024',
        ]);

        $journal->update([
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'],
            'description' => $validated['description'],
            'summary' => $validated['summary'] ?? null,
            'show_summary' => $validated['show_summary'] ?? false,
            'about' => $validated['about'] ?? null,
            // 'editorial_team_description' => $validated['editorial_team_description'] ?? null,
            'publisher' => $validated['publisher'],
            'issn_print' => $validated['issn_print'],
            'url_issn_print' => $validated['url_issn_print'] ?? null,
            'issn_online' => $validated['issn_online'],
            'url_issn_online' => $validated['url_issn_online'] ?? null,
            'enabled' => $validated['enabled'] ?? true,
            'visible' => $validated['visible'] ?? true,
            'path' => Str::lower($validated['abbreviation']) ?? $journal->path,
            'slug' => Str::slug($validated['abbreviation']) ?? $journal->slug,
        ]);

        // Upload logo
        if ($request->hasFile('logo')) {
            if ($journal->logo_path) {
                Storage::disk('public')->delete($journal->logo_path);
            }
            $path = $request->file('logo')->store("journals/{$journal->id}", 'public');
            $journal->update(['logo_path' => $path]);
        }

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            if ($journal->thumbnail_path) {
                Storage::disk('public')->delete($journal->thumbnail_path);
            }
            $path = $request->file('thumbnail')->store("journals/{$journal->id}", 'public');
            $journal->update(['thumbnail_path' => $path]);
        }

        // Redirect based on context
        if (current_journal()) {
            return redirect()->route('journal.admin.settings', ['journal' => $journal->slug])
                ->with('success', 'Journal settings updated successfully.');
        }

        return redirect()->route('admin.journals.index')
            ->with('success', 'Journal settings updated successfully.');
    }

    /**
     * Update journal settings (from /{journal}/settings page).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        $tab = $request->input('tab', 'masthead');

        if ($tab === 'masthead') {
            // Validate masthead data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'abbreviation' => 'nullable|string|max:50',
                'publisher' => 'nullable|string|max:255',
                'issn_print' => 'nullable|string|max:20',
                'issn_online' => 'nullable|string|max:20',
                'url_issn_print' => 'nullable|string|max:255',
                'url_issn_online' => 'nullable|string|max:255',
                'summary' => 'nullable|string',
                'show_summary' => 'boolean',
                'editorial_team' => 'nullable|string',
                'about' => 'nullable|string',
            ]);

            // Update journal columns
            $journal->update([
                'name' => $validated['name'],
                'abbreviation' => $validated['abbreviation'],
                'publisher' => $validated['publisher'],
                'issn_print' => $validated['issn_print'],
                'url_issn_print' => $validated['url_issn_print'] ?? null,
                'issn_online' => $validated['issn_online'],
                'url_issn_online' => $validated['url_issn_online'] ?? null,
                'summary' => $validated['summary'] ?? null,
                'show_summary' => $request->boolean('show_summary'),
            ]);

            // Save descriptions to settings JSONB
            $settings = $journal->settings ?? [];
            $settings['masthead'] = [
                'editorial_team' => $validated['editorial_team'] ?? '',
                'about' => $validated['about'] ?? '',
            ];
            $journal->update(['settings' => $settings]);

            return back()->with('success', 'Masthead settings saved successfully.');
        }

        if ($tab === 'contact') {
            // Validate contact data
            $validated = $request->validate([
                'mailing_address' => 'nullable|string',
                'contact_name' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:50',
                'contact_affiliation' => 'nullable|string|max:255',
                'support_name' => 'nullable|string|max:255',
                'support_email' => 'nullable|email|max:255',
                'support_phone' => 'nullable|string|max:50',
            ]);

            // Save to settings JSONB
            $settings = $journal->settings ?? [];
            $settings['contact'] = [
                'mailing_address' => $validated['mailing_address'] ?? '',
                'principal' => [
                    'name' => $validated['contact_name'] ?? '',
                    'email' => $validated['contact_email'] ?? '',
                    'phone' => $validated['contact_phone'] ?? '',
                    'affiliation' => $validated['contact_affiliation'] ?? '',
                ],
                'support' => [
                    'name' => $validated['support_name'] ?? '',
                    'email' => $validated['support_email'] ?? '',
                    'phone' => $validated['support_phone'] ?? '',
                ],
            ];
            $journal->update(['settings' => $settings]);

            return back()->with('success', 'Contact settings saved successfully.');
        }

        return back()->with('error', 'Unknown settings tab.');
    }

    /**
     * Remove the specified journal (Super Admin only).
     */
    public function destroy(Journal $journal): RedirectResponse
    {
        $journal->delete();

        return redirect()->route('admin.journals.index')
            ->with('success', 'Journal deleted successfully.');
    }
}
