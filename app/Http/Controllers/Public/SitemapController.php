<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    /**
     * Generate the sitemap.xml
     */
    public function index()
    {
        // 1. Fetch Enabled Journals
        $journals = Journal::where('enabled', true)->get();
        
        // 2. Ambil Artikel (Submission)
        $articles = Submission::leftJoin('publications', 'submissions.id', '=', 'publications.submission_id')
            // --- BAGIAN INI YANG MEMFILTER PUBLISHED ---
            ->where('submissions.status', Submission::STATUS_PUBLISHED)
            // ------------------------------------------
            ->select(
                'submissions.id',
                'submissions.seq_id',
                'submissions.updated_at',
                'submissions.journal_id',
                'submissions.slug',
                'publications.date_published',
                // Prioritas tanggal: Date Published > Updated At
                DB::raw("COALESCE(publications.date_published, submissions.updated_at) as last_mod_date")
            )
            ->with(['journal'])
            ->orderBy('last_mod_date', 'desc')
            ->limit(2000)
            ->get();

        return response()->view('public.sitemap', compact('journals', 'articles'))
            ->header('Content-Type', 'text/xml');
    }
}
