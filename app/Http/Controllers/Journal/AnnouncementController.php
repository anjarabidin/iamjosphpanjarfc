<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements for the current journal.
     */
    public function index()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        $announcements = Announcement::where('journal_id', $journal->id)
            ->with('user')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('journal.admin.announcements.index', compact('journal', 'announcements'));
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
        ]);

        $announcement = Announcement::create([
            'journal_id' => $journal->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_urgent' => $request->boolean('is_urgent', false),
            'published_at' => $validated['published_at'] ?? now(),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()
            ->route('journal.announcements.index', ['journal' => $journal->slug])
            ->with('success', 'Announcement created successfully.');
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        $journal = current_journal();

        if (!$journal || $announcement->journal_id !== $journal->id) {
            abort(404, 'Announcement not found');
        }

        return response()->json([
            'id' => $announcement->id,
            'title' => $announcement->title,
            'excerpt' => $announcement->excerpt,
            'content' => $announcement->content,
            'is_active' => $announcement->is_active,
            'is_urgent' => $announcement->is_urgent,
            'published_at' => $announcement->published_at?->format('Y-m-d\TH:i'),
            'expires_at' => $announcement->expires_at?->format('Y-m-d\TH:i'),
        ]);
    }

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $journal = current_journal();

        if (!$journal || $announcement->journal_id !== $journal->id) {
            abort(404, 'Announcement not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_urgent' => $request->boolean('is_urgent', false),
            'published_at' => $validated['published_at'] ?? $announcement->published_at,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()
            ->route('journal.announcements.index', ['journal' => $journal->slug])
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(Announcement $announcement)
    {
        $journal = current_journal();

        if (!$journal || $announcement->journal_id !== $journal->id) {
            abort(404, 'Announcement not found');
        }

        $announcement->delete();

        return redirect()
            ->route('journal.announcements.index', ['journal' => $journal->slug])
            ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Toggle the active status of an announcement.
     */
    public function toggleActive(Announcement $announcement)
    {
        $journal = current_journal();

        if (!$journal || $announcement->journal_id !== $journal->id) {
            abort(404, 'Announcement not found');
        }

        $announcement->update([
            'is_active' => !$announcement->is_active,
        ]);

        return redirect()
            ->route('journal.announcements.index', ['journal' => $journal->slug])
            ->with('success', 'Announcement status updated.');
    }
}
