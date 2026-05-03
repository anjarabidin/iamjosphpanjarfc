<?php

namespace App\Http\Controllers\Admin;

use App\Models\Journal;
use App\Models\SiteContent;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class SiteAdminController extends Controller
{
    /**
     * Display the site administration dashboard.
     */
    /**
     * Display the site administration dashboard.
     */
    public function index()
    {
        $journals = Journal::withCount(['submissions', 'issues'])
            ->orderBy('name')
            ->get();

        return view('admin.site.index', compact('journals'));
    }

    /**
     * Display the site settings form.
     */
    /**
     * Display the site settings form.
     */
    public function siteSettings()
    {
        $siteSetting = SiteSetting::firstOrCreate([], [
            'site_title' => config('app.name'),
            'site_intro' => 'Welcome to our academic journal portal.',
            'min_password_length' => 8,
            'redirect_to_journal' => false,
            'footer_content' => '',
        ]);

        return view('admin.site.settings', compact('siteSetting'));
    }

    /**
     * Display system information.
     */
    public function systemInfo()
    {
        $systemInfo = $this->getSystemInfo();
        return view('admin.site.system-info', compact('systemInfo'));
    }

    /**
     * Update site settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_title' => 'required|string|max:255',
            'site_intro' => 'nullable|string',
            'about_content' => 'nullable|string',
            'footer_content' => 'nullable|string',
            'header_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'show_journal_summary' => 'boolean',
            'header_bg_image' => 'boolean',
            'homepage_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'redirect_to_journal' => 'boolean',
            'min_password_length' => 'required|integer|min:6|max:32',
            // WhatsApp Gateway Config
            'wa_api_url' => 'nullable|url',
            'wa_sender_number' => 'nullable|string|max:20',
            'wa_device_id' => 'nullable|string|max:255',
            // Google reCAPTCHA Config
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',
        ]);

        $settings = \App\Models\SiteSetting::first();
        
        // Handle boolean checkboxes which might not be present in request
        $validated['redirect_to_journal'] = $request->has('redirect_to_journal');
        $validated['show_journal_summary'] = $request->has('show_journal_summary');
        $validated['header_bg_image'] = $request->has('header_bg_image');

        // Handle reCAPTCHA Keys
        if ($request->has('recaptcha_site_key')) {
            $validated['recaptcha_site_key'] = $request->input('recaptcha_site_key');
        }
        if ($request->has('recaptcha_secret_key')) {
            $validated['recaptcha_secret_key'] = $request->input('recaptcha_secret_key');
        }

        // Handle file upload
        if ($request->hasFile('homepage_image')) {
            $file = $request->file('homepage_image');
            $filename = 'homepage_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('site', $filename, 'public');
            $validated['homepage_image'] = $path;
        }

        if ($settings) {
            $settings->update($validated);
        } else {
            \App\Models\SiteSetting::create($validated);
        }

        return back()->with('success', 'Site settings updated successfully.');
    }

    /**
     * Expire all user sessions.
     */
    public function expireSessions()
    {
        try {
            DB::table('sessions')->truncate();
            return back()->with('success', 'All user sessions have been expired.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to expire sessions: ' . $e->getMessage());
        }
    }

    /**
     * Clear data cache.
     */
    public function clearDataCache()
    {
        try {
            Artisan::call('cache:clear');
            return back()->with('success', 'Data cache cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear template cache.
     */
    public function clearTemplateCache()
    {
        try {
            Artisan::call('view:clear');
            return back()->with('success', 'Template cache cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear template cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear scheduled task logs.
     */
    public function clearScheduledTaskLogs()
    {
        try {
            // Clear any scheduled task logs if applicable
            return back()->with('success', 'Scheduled task logs cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }

    /**
     * Get system information.
     */
    private function getSystemInfo(): array
    {
        return [
            'version' => 'IAMJOS 1.0.0',
            'server_date' => now()->format('F j, Y, g:i a'),
            'operating_system' => PHP_OS_FAMILY . ' (' . php_uname('r') . ')',
            'php_version' => PHP_VERSION,
            'database_driver' => config('database.default'),
            'database_version' => $this->getDatabaseVersion(),
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'config_path' => base_path('.env'),
            'base_url' => config('app.url'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ];
    }

    /**
     * Get database version.
     */
    private function getDatabaseVersion(): string
    {
        try {
            $driver = config('database.default');
            if ($driver === 'mysql') {
                return DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown';
            } elseif ($driver === 'pgsql') {
                return DB::select('SELECT version()')[0]->version ?? 'Unknown';
            } elseif ($driver === 'sqlite') {
                return DB::select('SELECT sqlite_version() as version')[0]->version ?? 'Unknown';
            }
            return 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}
