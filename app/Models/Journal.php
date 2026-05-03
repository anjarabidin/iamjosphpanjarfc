<?php

namespace App\Models;

use App\Observers\JournalObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([JournalObserver::class])]
class Journal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'path',
        'slug',
        'abbreviation',
        'description',
        'publisher',
        'issn_print',
        'url_issn_print',
        'issn_online',
        'url_issn_online',
        'enabled',
        'wa_notifications_enabled',
        'visible',
        'logo_path',
        'thumbnail_path',
        'favicon_path',
        'homepage_image_path',
        'show_homepage_image_in_header',
        'page_footer',
        'additional_content',
        'settings',
        'license_terms',
        'license_url',
        'copyright_holder_type',
        'copyright_holder_other',
        'copyright_year_basis',
        'search_description',
        'custom_headers',
        'open_access_policy',
        'enable_oai',
        'enable_lockss',
        'enable_clockss',
        'archiving_policy',
        'author_guidelines',
        'summary',
        'show_summary',
        'editorial_team_description',
        'info_readers',
        'info_authors',
        'info_librarians',
        'enable_announcements',
        'announcements_introduction',
        'show_announcements_on_homepage',
        'num_announcements_homepage',
        // DOI Settings (OJS 3.3 DOI Plugin)
        'doi_enabled',
        'doi_objects',
        'doi_prefix',
        'doi_suffix_type',
        'doi_custom_pattern',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'wa_notifications_enabled' => 'boolean',
            'visible' => 'boolean',
            'settings' => 'array', // JSONB to array
            'enable_oai' => 'boolean',
            'enable_lockss' => 'boolean',
            'enable_clockss' => 'boolean',
            'show_homepage_image_in_header' => 'boolean',
            'show_summary' => 'boolean',
            // DOI Settings
            'doi_enabled' => 'boolean',
            'doi_objects' => 'array',
        ];
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get sections for this journal
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'journal_id');
    }

    /**
     * Get issues for this journal
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'journal_id');
    }

    /**
     * Get the current (latest published) issue for this journal
     */
    public function currentIssue(): HasOne
    {
        return $this->hasOne(Issue::class, 'journal_id')->where('is_published', true)->latest('published_at');
    }

    /**
     * Get submissions for this journal
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'journal_id');
    }

    /**
     * Get categories for this journal
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'journal_id');
    }

    /**
     * Get user role assignments for this journal
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(JournalUserRole::class, 'journal_id');
    }

    /**
     * Get roles defined for this journal
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'journal_id');
    }

    /**
     * Get all users registered with this journal
     * Uses proper many-to-many relationship to avoid N+1 queries
     */
    public function registeredUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'journal_user_roles',
            'journal_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Get users with a specific role in this journal
     * Optimized: single query with join instead of multiple queries
     */
    public function usersWithRole(string $roleName): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->registeredUsers()
            ->whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            });
    }

    /**
     * Get submission checklists for this journal
     */
    public function submissionChecklists(): HasMany
    {
        return $this->hasMany(SubmissionChecklist::class, 'journal_id');
    }

    /**
     * Get review forms for this journal
     */
    public function reviewForms(): HasMany
    {
        return $this->hasMany(ReviewForm::class, 'journal_id');
    }

    /**
     * Get library files for this journal
     */
    public function libraryFiles(): HasMany
    {
        return $this->hasMany(LibraryFile::class, 'journal_id');
    }

    /**
     * Get email templates for this journal
     */
    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class, 'journal_id');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    /**
     * Scope to only include enabled journals
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope to only include visible journals
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    // =====================================================
    // ACCESSORS & HELPERS
    // =====================================================

    /**
     * Get a specific setting value
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set a specific setting value
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
    }

    // =====================================================
    // JOURNAL SETTINGS (Key-Value Storage)
    // =====================================================

    /**
     * Get website settings for this journal
     */
    public function websiteSettings(): HasMany
    {
        return $this->hasMany(JournalSetting::class, 'journal_id');
    }

    /**
     * Get a website setting value
     */
    public function getWebsiteSetting(string $name, mixed $default = null): mixed
    {
        return JournalSetting::get($this, $name, $default);
    }

    /**
     * Set a website setting value
     */
    public function setWebsiteSetting(string $name, mixed $value, string $type = 'string', string $group = 'general'): JournalSetting
    {
        return JournalSetting::set($this, $name, $value, $type, $group);
    }

    /**
     * Get all website settings, optionally by group
     */
    public function getWebsiteSettings(?string $group = null): array
    {
        return JournalSetting::getAllForJournal($this, $group);
    }

    /**
     * Get announcements for this journal
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'journal_id');
    }
}
