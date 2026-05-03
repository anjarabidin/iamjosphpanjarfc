<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\JournalSetting;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingsController extends Controller
{
    /**
     * Show the website settings form.
     */
    public function edit()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        // Get all current settings for this journal
        $settings = $journal->getWebsiteSettings();

        // Define defaults for missing settings
        $defaults = $this->getDefaultSettings($journal);

        // Merge with actual settings (actual takes precedence)
        $settings = array_merge($defaults, $settings);

        // Get Site Setting to access global reCAPTCHA keys
        $siteSetting = SiteSetting::first();

        return view('journal.admin.settings.website', compact('journal', 'settings', 'siteSetting'));
    }

    /**
     * Update website settings.
     */
    public function update(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        // Validate file uploads
        $request->validate([
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'favicon' => 'nullable|mimes:ico,png,jpg,svg,webp|max:1024',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'homepage_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'indexed_in_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg|max:2048',
        ]);

        // Handle Logo Upload (stored in journals table)
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($journal->logo_path) {
                Storage::disk('public')->delete($journal->logo_path);
            }
            $journal->logo_path = $request->file('logo')->store("journals/{$journal->id}/appearance", 'public');
        }

        // Handle Favicon Upload (stored in journals table)
        if ($request->hasFile('favicon')) {
            if ($journal->favicon_path) {
                Storage::disk('public')->delete($journal->favicon_path);
            }
            $journal->favicon_path = $request->file('favicon')->store("journals/{$journal->id}/appearance", 'public');
        }

        // Handle Thumbnail Upload (stored in journals table)
        if ($request->hasFile('thumbnail')) {
            if ($journal->thumbnail_path) {
                Storage::disk('public')->delete($journal->thumbnail_path);
            }
            $journal->thumbnail_path = $request->file('thumbnail')->store("journals/{$journal->id}/appearance", 'public');
        }

        // Handle Homepage Image Upload (stored in journals table)
        if ($request->hasFile('homepage_image')) {
            if ($journal->homepage_image_path) {
                Storage::disk('public')->delete($journal->homepage_image_path);
            }
            $journal->homepage_image_path = $request->file('homepage_image')->store("journals/{$journal->id}/appearance", 'public');
        }

        // Handle Header Background Toggle (stored in journals table)
        $journal->show_homepage_image_in_header = $request->boolean('show_homepage_image_in_header');

        // Handle Page Footer (stored in journals table)
        if ($request->has('page_footer')) {
            $journal->page_footer = $request->input('page_footer');
        }

        // Handle Additional Content (stored in journals table)
        if ($request->has('additional_content')) {
            $journal->additional_content = $request->input('additional_content');
        }

        // Handle Information Content (stored in journals table)
        if ($request->has('info_readers')) {
            $journal->info_readers = $request->input('info_readers');
        }
        if ($request->has('info_authors')) {
            $journal->info_authors = $request->input('info_authors');
        }
        if ($request->has('info_librarians')) {
            $journal->info_librarians = $request->input('info_librarians');
        }

        // Handle Announcement Settings (stored in journals table)
        $journal->enable_announcements = $request->boolean('enable_announcements');
        if ($request->has('announcements_introduction')) {
            $journal->announcements_introduction = $request->input('announcements_introduction');
        }
        $journal->show_announcements_on_homepage = $request->boolean('show_announcements_on_homepage');
        if ($request->has('num_announcements_homepage')) {
            $journal->num_announcements_homepage = $request->input('num_announcements_homepage');
        }

        // Handle Security/Recaptcha Toggle
        if ($request->input('tab') === 'security' || $request->has('is_recaptcha_enabled')) {
             // We only toggle the enablement here, keys are global
             $journal->is_recaptcha_enabled = $request->boolean('is_recaptcha_enabled');
        }

        // Save journal model changes
        $journal->save();

        // Handle other settings (stored in journal_settings table)
        $settingsConfig = $this->getSettingsConfig();

        foreach ($settingsConfig as $name => $config) {
            $value = $request->input($name);

            // Skip file types handled above
            if ($config['type'] === 'file') {
                continue;
            }

            // Handle multi-file uploads (indexed_in_images)
            if ($config['type'] === 'json' && $name === 'indexed_in_images') {
                $existingSetting = $journal->getWebsiteSetting('indexed_in_images', []);

                // Handle both array (already decoded) and string (raw JSON) formats
                if (is_array($existingSetting)) {
                    $existingImages = $existingSetting;
                } elseif (is_string($existingSetting)) {
                    $existingImages = json_decode($existingSetting, true) ?? [];
                } else {
                    $existingImages = [];
                }

                if ($request->hasFile('indexed_in_images')) {
                    foreach ($request->file('indexed_in_images') as $file) {
                        $path = $file->store("journals/{$journal->id}/website/indexers", 'public');
                        $existingImages[] = $path;
                    }
                }

                $value = json_encode(array_values($existingImages));
            }

            // Handle boolean toggles
            if ($config['type'] === 'boolean') {
                $value = $request->boolean($name);
            }

            // Skip if value is null and not explicitly set
            if ($value === null && !$request->has($name)) {
                continue;
            }

            $journal->setWebsiteSetting(
                $name,
                $value,
                $config['type'],
                $config['group']
            );
        }

        return redirect()
            ->route('journal.settings.website.edit', ['journal' => $journal->slug])
            ->with('success', 'Website settings updated successfully.');
    }

    /**
     * Delete an indexed image.
     */
    public function deleteIndexedImage(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        $path = $request->input('path');
        $existingSetting = $journal->getWebsiteSetting('indexed_in_images', []);

        // Handle both array and string formats
        if (is_array($existingSetting)) {
            $existingImages = $existingSetting;
        } elseif (is_string($existingSetting)) {
            $existingImages = json_decode($existingSetting, true) ?? [];
        } else {
            $existingImages = [];
        }

        // Remove from storage
        Storage::disk('public')->delete($path);

        // Remove from array
        $existingImages = array_filter($existingImages, fn($img) => $img !== $path);

        $journal->setWebsiteSetting('indexed_in_images', json_encode(array_values($existingImages)), 'json', 'content');

        return response()->json(['success' => true]);
    }

    /**
     * Delete logo image.
     */
    public function deleteLogo()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        if ($journal->logo_path) {
            Storage::disk('public')->delete($journal->logo_path);
            $journal->logo_path = null;
            $journal->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete favicon image.
     */
    public function deleteFavicon()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        if ($journal->favicon_path) {
            Storage::disk('public')->delete($journal->favicon_path);
            $journal->favicon_path = null;
            $journal->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete homepage image.
     */
    public function deleteHomepageImage()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        if ($journal->homepage_image_path) {
            Storage::disk('public')->delete($journal->homepage_image_path);
            $journal->homepage_image_path = null;
            $journal->show_homepage_image_in_header = false;
            $journal->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get default settings.
     */
    private function getDefaultSettings(Journal $journal): array
    {
        return [
            // Content
            'about' => '',
            'masthead' => ['about' => '', 'editorial_team' => ''],

            // Appearance
            'primary_color' => '#4F46E5',
            'secondary_color' => '#7C3AED',

            // Section Visibility
            'show_announcements' => true,
            'show_editorial_team' => true,
            'show_indexed_in' => true,

            // Indexed In
            'indexed_in_images' => [],

            // Footer
            'footer_description' => $journal->description ?? 'A leading academic journal publishing cutting-edge research.',
            'social_facebook' => '',
            'social_twitter' => '',
            'social_linkedin' => '',
            'social_instagram' => '',
            'contact_email' => '',
            'contact_phone' => '',
            'contact_address' => '',
        ];
    }

    /**
     * Get settings configuration (name => type, group).
     */
    private function getSettingsConfig(): array
    {
        return [
            // Content
            'about' => ['type' => 'string', 'group' => 'content'],
            'masthead' => ['type' => 'json', 'group' => 'content'],

            // Appearance (colors only - images now in journals table)
            'primary_color' => ['type' => 'string', 'group' => 'appearance'],
            'secondary_color' => ['type' => 'string', 'group' => 'appearance'],

            // Section Visibility
            'show_announcements' => ['type' => 'boolean', 'group' => 'content'],
            'show_editorial_team' => ['type' => 'boolean', 'group' => 'content'],
            'show_indexed_in' => ['type' => 'boolean', 'group' => 'content'],

            // Indexed In
            'indexed_in_images' => ['type' => 'json', 'group' => 'content'],

            // Footer
            'footer_description' => ['type' => 'string', 'group' => 'footer'],
            'social_facebook' => ['type' => 'string', 'group' => 'footer'],
            'social_twitter' => ['type' => 'string', 'group' => 'footer'],
            'social_linkedin' => ['type' => 'string', 'group' => 'footer'],
            'social_instagram' => ['type' => 'string', 'group' => 'footer'],
            'contact_email' => ['type' => 'string', 'group' => 'footer'],
            'contact_phone' => ['type' => 'string', 'group' => 'footer'],
            'contact_address' => ['type' => 'string', 'group' => 'footer'],
        ];
    }
}
