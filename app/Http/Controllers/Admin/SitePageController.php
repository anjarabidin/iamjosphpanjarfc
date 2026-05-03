<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * SitePageController
 * 
 * Manages custom static pages for the portal (CMS).
 * Pages can be created with rich text content using TinyMCE.
 */
class SitePageController extends Controller
{
    /**
     * Display a listing of site pages.
     */
    public function index()
    {
        $pages = SitePage::ordered()->get();
        return view('admin.site-pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('admin.site-pages.form', [
            'page' => null,
            'title' => 'Create New Page',
        ]);
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:site_pages,slug',
            'content' => 'nullable|string',
            'is_published' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Ensure unique slug
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (SitePage::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter++;
        }

        $validated['is_published'] = $request->boolean('is_published');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        SitePage::create($validated);

        return redirect()->route('admin.site-pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(SitePage $sitePage)
    {
        return view('admin.site-pages.form', [
            'page' => $sitePage,
            'title' => 'Edit Page: ' . $sitePage->title,
        ]);
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, SitePage $sitePage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:site_pages,slug,' . $sitePage->id,
            'content' => 'nullable|string',
            'is_published' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['is_published'] = $request->boolean('is_published');
        $validated['sort_order'] = $validated['sort_order'] ?? $sitePage->sort_order;

        $sitePage->update($validated);

        return redirect()->route('admin.site-pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page.
     */
    public function destroy(SitePage $sitePage)
    {
        $sitePage->delete();

        return redirect()->route('admin.site-pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    /**
     * Toggle page published state via AJAX.
     */
    public function toggle(SitePage $sitePage)
    {
        $sitePage->is_published = !$sitePage->is_published;
        $sitePage->save();

        return response()->json([
            'success' => true,
            'is_published' => $sitePage->is_published,
            'message' => $sitePage->is_published ? 'Page published' : 'Page unpublished',
        ]);
    }

    /**
     * Reorder pages via AJAX (Drag & Drop).
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'pages' => 'required|array',
            'pages.*.id' => 'required|uuid|exists:site_pages,id',
            'pages.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->pages as $pageData) {
            SitePage::where('id', $pageData['id'])
                ->update(['sort_order' => $pageData['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }
}
