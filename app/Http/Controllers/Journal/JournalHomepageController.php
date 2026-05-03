<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class JournalHomepageController extends Controller
{
    /**
     * Display the journal public homepage.
     */
    public function index(string $journalSlug)
    {
        $journal = Journal::where('slug', $journalSlug)
            ->where('enabled', true)
            ->firstOrFail();

        // Get all website settings with defaults
        $settings = $this->getSettingsWithDefaults($journal);

        // Get announcements if enabled (New logic using journals table columns)
        $announcements = collect();
        // Check both the global enable switch AND the homepage display switch
        if ($journal->enable_announcements && $journal->show_announcements_on_homepage) {
            $count = $journal->num_announcements_homepage ?? 5; // Default to 5 if null
            if ($count > 0) {
                $announcements = Announcement::where('journal_id', $journal->id)
                    ->where('is_active', true)
                    ->where('published_at', '<=', now())
                    ->orderBy('published_at', 'desc')
                    ->take($count)
                    ->get();
            }
        } elseif (!empty($settings['show_announcements'])) {
            // Fallback to legacy setting if new columns are not yet in use or populated
             $announcements = $this->getAnnouncements($journal);
        }

        // Get editorial team if enabled
        $editorialTeam = collect();
        if ($settings['show_editorial_team']) {
            $editorialTeam = $this->getEditorialTeam($journal);
        }

        // Get indexed in images
        $val = $settings['indexed_in_images'] ?? [];
        $indexedInImages = is_array($val) ? $val : json_decode($val, true) ?? [];

        // Get current issue
        $currentIssue = $journal->issues()
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->first();

        // Get published articles count
        $publishedCount = $journal->submissions()
            ->where('status', 'published')
            ->count();

        return view('journal.public.home', compact(
            'journal',
            'settings',
            'announcements',
            'editorialTeam',
            'indexedInImages',
            'currentIssue',
            'publishedCount'
        ));
    }

    /**
     * Get settings with defaults merged.
     */
    private function getSettingsWithDefaults(Journal $journal): array
    {
        $defaults = [
            // Content
            'about' => '',
            'masthead' => ['about' => '', 'editorial_team' => ''],

            // Appearance
            'hero_image' => null,
            'primary_color' => '#4F46E5',
            'secondary_color' => '#7C3AED',

            // Hero Content
            'hero_title' => $journal->name,
            'hero_description' => $journal->description ?? 'A peer-reviewed scholarly journal dedicated to advancing knowledge and research.',
            'hero_tagline' => 'Peer-Reviewed • Open Access • Indexed',

            // Stats
            'stat_acceptance_rate' => '25%',
            'stat_review_time' => '4 Weeks',
            'stat_impact_factor' => 'N/A',
            'stat_citations' => '1000+',

            // Section Visibility
            'show_announcements' => true,
            'show_editorial_team' => true,
            'show_indexed_in' => true,
            'show_stats' => true,

            // Indexed In
            'indexed_in_images' => [],

            // Footer
            'footer_description' => $journal->description ?? 'A leading academic journal.',
            'social_facebook' => '',
            'social_twitter' => '',
            'social_linkedin' => '',
            'social_instagram' => '',
            'contact_email' => '',
            'contact_phone' => '',
            'contact_address' => '',
        ];

        $actual = $journal->getWebsiteSettings();

        return array_merge($defaults, $actual);
    }

    /**
     * Get announcements for the journal.
     */
    private function getAnnouncements(Journal $journal)
    {
        // Fetch the 3 latest active announcements that are published and not expired
        $announcements = Announcement::where('journal_id', $journal->id)
            ->where('is_active', true)
            // Published: published_at is null or in the past
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            // Not expired: expires_at is null or in the future
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('is_urgent', 'desc') // Show urgent first
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // If no real announcements, return placeholder data for preview
        if ($announcements->isEmpty()) {
            return collect([
                (object) [
                    'id' => 1,
                    'title' => 'Call for Papers: Special Issue 2026',
                    'excerpt' => 'We invite researchers to submit papers for our upcoming special issue on emerging technologies.',
                    'created_at' => now()->subDays(2),
                    'is_urgent' => true,
                ],
                (object) [
                    'id' => 2,
                    'title' => 'New Indexing Partnership',
                    'excerpt' => 'We are pleased to announce our journal has been indexed in Scopus.',
                    'created_at' => now()->subDays(5),
                    'is_urgent' => false,
                ],
                (object) [
                    'id' => 3,
                    'title' => 'Editorial Board Update',
                    'excerpt' => 'Welcome to our new editorial board members for 2026.',
                    'created_at' => now()->subDays(10),
                    'is_urgent' => false,
                ],
            ]);
        }

        return $announcements;
    }

    /**
     * Get editorial team for the journal.
     */
    private function getEditorialTeam(Journal $journal)
    {
        // Get users with Editor roles in this journal
        $editorRoles = ['Editor', 'Editor-in-Chief', 'Section Editor', 'Journal Manager'];

        $editorialTeam = collect();

        foreach ($editorRoles as $roleName) {
            $users = $journal->usersWithRole($roleName);
            foreach ($users as $user) {
                $editorialTeam->push((object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $roleName,
                    'affiliation' => $user->affiliation ?? 'Institution',
                    'email' => $user->email,
                    'avatar' => $user->avatar_url ?? null,
                    'orcid' => $user->orcid ?? null,
                ]);
            }
        }

        // If no editors found, return placeholder data
        if ($editorialTeam->isEmpty()) {
            return collect([
                (object) [
                    'id' => 1,
                    'name' => 'Dr. Jane Smith',
                    'role' => 'Editor-in-Chief',
                    'affiliation' => 'University of Technology',
                    'email' => 'editor@journal.com',
                    'avatar' => null,
                    'orcid' => null,
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Prof. John Doe',
                    'role' => 'Associate Editor',
                    'affiliation' => 'Research Institute',
                    'email' => 'associate@journal.com',
                    'avatar' => null,
                    'orcid' => null,
                ],
            ]);
        }

        return $editorialTeam->take(6);
    }
}
