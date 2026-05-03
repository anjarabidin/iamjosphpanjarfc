# IAMJOS Technical Specification

**Last Updated:** 2026-03-06  
**Version:** Laravel 12.x (PHP 8.4.16)  
**Audited by:** Senior Laravel Architect

---

## 1. Project Overview

**Project Name:** IAMJOS (Indonesian Academic Journal System)  
**Type:** SaaS / Multi-tenant Academic Journal Publishing Platform  
**Core Concept:** A modern, clean-code clone of OJS (Open Journal Systems) 3.3 with significantly improved UI/UX.  
**Goal:** Replicate the deep functionality of OJS 3.3 (Submission → Review → Editorial Workflow → Publishing) with a modern interface built on Laravel 12.  
**Target Users:** Academic institutions, journal editors, reviewers, and authors in Indonesia.

---

## 2. Tech Stack

| Component           | Technology         | Version |
| ------------------- | ------------------ | ------- |
| Framework           | Laravel            | ^12.0   |
| Language            | PHP                | ^8.4    |
| Database            | PostgreSQL         | —       |
| Frontend Templating | Laravel Blade      | —       |
| CSS Framework       | Tailwind CSS       | v3.x    |
| Interactivity       | Alpine.js          | v3.x    |
| Reactive Components | Livewire           | ^3.7    |
| Asset Bundling      | Vite               | —       |
| Icons               | FontAwesome (Free) | —       |
| Typography          | Inter (Sans-serif) | —       |

### Key Dependencies (composer.json)

| Package                              | Purpose                                          |
| ------------------------------------ | ------------------------------------------------ |
| `spatie/laravel-permission` ^6.21    | RBAC — Role & Permission management              |
| `livewire/livewire` ^3.7             | Reactive components (Stats, Sidebar, Merge User) |
| `barryvdh/laravel-dompdf` ^3.1       | PDF generation (Correspondence export)           |
| `maatwebsite/excel` ^3.1             | Excel import/export (Author, Publisher)          |
| `intervention/image` ^3.11           | Image processing & compression                   |
| `laravel/passport` ^13.0             | OAuth2 API authentication                        |
| `laravel/sanctum` ^4.0               | SPA/Token authentication                         |
| `laravel/socialite` ^5.24            | Google OAuth login                               |
| `mews/purifier` ^3.4                 | HTML sanitization                                |
| `opcodesio/log-viewer` ^3.21         | Log viewer dashboard                             |
| `openspout/openspout` ^4.28          | Spreadsheet read/write (alternative engine)      |
| `pbmedia/laravel-ffmpeg` ^8.7        | Video processing                                 |
| `stevebauman/location` ^7.6          | IP geolocation (article metrics)                 |
| `stichoza/google-translate-php` ^5.3 | Auto-translation feature                         |
| `yajra/laravel-datatables` 12.0      | Server-side DataTables                           |

### Dev Dependencies

| Package               | Purpose                |
| --------------------- | ---------------------- |
| `pestphp/pest` ^3.0   | Testing framework      |
| `laravel/pint` ^1.13  | Code style fixer       |
| `laravel/sail` ^1.41  | Docker dev environment |
| `laravel/pail` ^1.2.2 | Real-time log tailing  |

---

## 3. Database Schema

All primary keys use **UUID**. Soft deletes are enabled on most tables.

### 3.1 Core Tables

#### `users`

| Column                  | Type            | Notes              |
| ----------------------- | --------------- | ------------------ |
| id                      | uuid (PK)       | —                  |
| name                    | string          | —                  |
| email                   | string (unique) | —                  |
| email_verified_at       | timestamp?      | —                  |
| password                | string          | —                  |
| avatar                  | string?         | Profile photo path |
| affiliation             | string?         | OJS field          |
| orcid                   | string?         | OJS field          |
| phone                   | string?         | —                  |
| country                 | string?         | —                  |
| mailing_address         | text?           | —                  |
| biography               | text?           | —                  |
| google_id               | string?         | Social auth        |
| remember_token          | string?         | —                  |
| created_at / updated_at | timestamps      | —                  |
| deleted_at              | timestamp?      | Soft delete        |

#### `journals`

| Column                                                  | Type            | Notes                    |
| ------------------------------------------------------- | --------------- | ------------------------ |
| id                                                      | uuid (PK)       | —                        |
| name                                                    | string          | Full journal name        |
| path                                                    | string (unique) | URL slug                 |
| slug                                                    | string?         | Additional slug          |
| abbreviation                                            | string?         | e.g., "JTI"              |
| description                                             | text?           | —                        |
| publisher                                               | string?         | Institution name         |
| issn_print / issn_online                                | string?         | —                        |
| enabled / visible                                       | boolean         | default true             |
| logo_path / thumbnail_path / favicon_path               | string?         | Branding                 |
| settings                                                | jsonb?          | Flexible key-value store |
| author_guidelines                                       | text?           | Workflow setting         |
| submission_metadata_settings                            | json?           | —                        |
| review_mode                                             | string          | default 'double_blind'   |
| review_response_weeks                                   | int             | default 2                |
| review_completion_weeks                                 | int             | default 4                |
| reviewer_guidelines                                     | text?           | —                        |
| require_competing_interests                             | boolean         | default true             |
| email_signature / email_bounce_address / email_reply_to | string?         | —                        |
| license_terms / license_url / copyright_holder_type     | string?         | Distribution             |
| doi_enabled                                             | boolean         | DOI Plugin               |
| doi_prefix / doi_suffix_type / doi_custom_pattern       | string?         | DOI Config               |
| wa_notifications_enabled                                | boolean         | WhatsApp toggle          |
| enable_oai / enable_lockss / enable_clockss             | boolean         | Harvesting               |
| enable_announcements                                    | boolean         | —                        |
| editorial_team_description                              | text?           | —                        |
| info_readers / info_authors / info_librarians           | text?           | Information pages        |
| created_at / updated_at / deleted_at                    | timestamps      | —                        |

#### `submissions`

| Column                                    | Type               | Notes                                                                         |
| ----------------------------------------- | ------------------ | ----------------------------------------------------------------------------- |
| id                                        | uuid (PK)          | —                                                                             |
| journal_id                                | uuid (FK→journals) | indexed                                                                       |
| user_id                                   | uuid (FK→users)    | Author/submitter                                                              |
| section_id                                | uuid (FK→sections) | indexed                                                                       |
| issue_id                                  | uuid? (FK→issues)  | Null until assigned                                                           |
| title                                     | string             | —                                                                             |
| abstract                                  | text?              | —                                                                             |
| keywords                                  | text?              | Comma-separated                                                               |
| status                                    | string             | draft, submitted, in_review, revision_required, accepted, rejected, published |
| stage                                     | string             | submission, review, copyediting, production                                   |
| slug                                      | string?            | URL-friendly title                                                            |
| submission_code                           | string?            | e.g., JCO-2026-001                                                            |
| seq_id                                    | integer?           | Sequential ID for routes                                                      |
| submitted_at / accepted_at / published_at | timestamp?         | —                                                                             |
| metadata                                  | jsonb?             | DOI, page numbers, etc.                                                       |
| created_at / updated_at / deleted_at      | timestamps         | —                                                                             |

### 3.2 Workflow Tables

#### `editorial_assignments`

| Column                                              | Type                     | Notes                           |
| --------------------------------------------------- | ------------------------ | ------------------------------- |
| id                                                  | uuid (PK)                | —                               |
| submission_id                                       | uuid (FK)                | —                               |
| user_id                                             | uuid (FK)                | Assigned editor                 |
| assigned_by                                         | uuid?                    | Who assigned                    |
| role                                                | string                   | editor, section_editor, manager |
| is_active / can_edit / can_access_editorial_history | boolean                  | Permissions                     |
| date_assigned / date_notified                       | timestamp?               | —                               |
| **unique**                                          | (submission_id, user_id) | Prevent duplicates              |

#### `review_assignments`

| Column                                               | Type            | Notes                                             |
| ---------------------------------------------------- | --------------- | ------------------------------------------------- |
| id                                                   | uuid (PK)       | —                                                 |
| submission_id                                        | uuid (FK)       | —                                                 |
| reviewer_id                                          | uuid (FK→users) | —                                                 |
| status                                               | string          | pending, accepted, declined, completed, cancelled |
| recommendation                                       | string?         | accept, minor_revision, major_revision, reject    |
| comments_for_author / comments_for_editor            | text?           | —                                                 |
| quality_rating                                       | integer?        | 1-5                                               |
| review_method                                        | string?         | blind, double_blind, open                         |
| assigned_at / due_date / responded_at / completed_at | timestamp?      | —                                                 |
| round                                                | integer         | default 1                                         |
| metadata                                             | jsonb?          | —                                                 |

#### `review_rounds`

| Column        | Type      | Notes                                                                 |
| ------------- | --------- | --------------------------------------------------------------------- |
| id            | uuid (PK) | —                                                                     |
| submission_id | uuid (FK) | —                                                                     |
| round         | integer   | default 1                                                             |
| status        | string    | pending, revisions_requested, resubmit_for_review, approved, declined |

#### `discussions`

| Column        | Type      | Notes                                               |
| ------------- | --------- | --------------------------------------------------- |
| id            | uuid (PK) | —                                                   |
| submission_id | uuid (FK) | —                                                   |
| user_id       | uuid (FK) | Creator                                             |
| subject       | string    | —                                                   |
| stage_id      | integer   | 1=Submission, 2=Review, 3=Copyediting, 4=Production |
| is_open       | boolean   | default true                                        |

#### `discussion_messages` / `discussion_files` / `discussion_participants`

Supporting tables for threaded discussion system with file attachments and participant tracking.

### 3.3 Publishing Tables

#### `publications`

| Column                                          | Type      | Notes                                             |
| ----------------------------------------------- | --------- | ------------------------------------------------- |
| id                                              | uuid (PK) | —                                                 |
| submission_id                                   | uuid (FK) | —                                                 |
| section_id / issue_id                           | uuid?     | —                                                 |
| version                                         | integer   | default 1, unique per submission                  |
| status                                          | tinyint   | 1=queued, 2=scheduled, 3=published, 4=unpublished |
| title / subtitle / abstract / keywords          | —         | Content fields                                    |
| pages / url_path                                | string?   | Pagination & custom URL                           |
| doi / doi_suffix                                | string?   | Identifier                                        |
| copyright_holder / copyright_year / license_url | —         | Copyright info                                    |
| date_published                                  | date?     | —                                                 |
| metadata                                        | jsonb?    | —                                                 |

#### `publication_galleys`

| Column        | Type                        | Notes           |
| ------------- | --------------------------- | --------------- |
| id            | uuid (PK)                   | —               |
| submission_id | uuid (FK)                   | —               |
| file_id       | uuid? (FK→submission_files) | —               |
| label         | string                      | PDF, HTML, EPUB |
| locale        | string                      | default 'en'    |
| url_remote    | string?                     | External link   |
| seq           | integer                     | Ordering        |

#### `issues`

| Column                 | Type       | Notes                 |
| ---------------------- | ---------- | --------------------- |
| id                     | uuid (PK)  | —                     |
| journal_id             | uuid (FK)  | —                     |
| volume / number / year | integer    | —                     |
| title                  | string?    | Special edition label |
| is_published           | boolean    | default false         |
| published_at           | timestamp? | —                     |
| cover_path             | string?    | —                     |
| metadata               | jsonb?     | —                     |

### 3.4 File Management

#### `submission_files`

| Column                | Type             | Notes                                                                  |
| --------------------- | ---------------- | ---------------------------------------------------------------------- |
| id                    | uuid (PK)        | —                                                                      |
| submission_id         | uuid (FK)        | —                                                                      |
| uploaded_by           | uuid? (FK→users) | —                                                                      |
| file_path / file_name | string           | —                                                                      |
| file_type             | string           | manuscript, revision, supplementary, galley, reviewer_attachment, etc. |
| mime_type             | string?          | —                                                                      |
| file_size             | bigint?          | Bytes                                                                  |
| version               | integer          | default 1                                                              |
| stage                 | string           | submission, review, copyediting, production                            |
| metadata              | jsonb?           | —                                                                      |

### 3.5 Settings & Configuration Tables

| Table                    | Purpose                                                       |
| ------------------------ | ------------------------------------------------------------- |
| `sections`               | Journal sections (with meta fields: policy, word_count, etc.) |
| `categories`             | Journal categories                                            |
| `submission_checklists`  | Author submission checklists                                  |
| `review_forms`           | Reviewer evaluation forms (JSON elements)                     |
| `email_templates`        | Per-journal email templates (keyed, customizable)             |
| `library_files`          | Publisher library (contracts, templates, etc.)                |
| `journal_settings`       | Key-value website settings (EAV pattern)                      |
| `journal_user_roles`     | Many-to-many: user ↔ role ↔ journal                           |
| `notification_templates` | WhatsApp/custom notification templates                        |
| `keywords`               | Keywords taxonomy (many-to-many with submissions)             |

### 3.6 Other Tables

| Table                                                                             | Purpose                          |
| --------------------------------------------------------------------------------- | -------------------------------- |
| `announcements`                                                                   | Journal announcements            |
| `site_pages`                                                                      | CMS pages                        |
| `site_contents` / `site_content_blocks`                                           | Site-level page builder blocks   |
| `site_settings`                                                                   | Global site settings             |
| `navigation_menus` / `navigation_menu_items` / `navigation_menu_item_assignments` | Menu system                      |
| `sidebar_blocks`                                                                  | Journal sidebar blocks           |
| `article_metrics`                                                                 | Article views/downloads tracking |
| `submission_index_stats`                                                          | Google Scholar indexing status   |
| `submission_logs`                                                                 | Activity log/timeline            |
| `submission_notes`                                                                | Editor notes on submissions      |
| `malware_scans` / `malware_findings`                                              | Malware scanner results          |
| `application_settings`                                                            | Legacy app settings              |

### 3.7 System Tables

`sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`, `personal_access_tokens`, `oauth_*` (Passport tables), `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions` (Spatie Permission tables).

---

## 4. Application Architecture

```
app/
├── Console/Commands/      # 4 Artisan commands
├── Exports/               # 4 Excel exports (Author, Publisher + templates)
├── Helpers/               # journal_helpers.php (global helper functions)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         # 50+ Admin controllers (legacy + journal management)
│   │   │   ├── Reports/   # ReportController (Articles, Reviews, Usage CSV)
│   │   │   ├── Stats/     # ArticleStatsController, EditorialStatsController, UserStatsController
│   │   │   └── Tools/     # CrossrefExport, Import, NativeImportExport
│   │   ├── Api/           # ReviewerApiController
│   │   ├── Journal/       # PublicationController, AnnouncementController, WebsiteSettings, etc.
│   │   ├── Public/        # OaiController, SitemapController
│   │   └── (root)         # Core controllers (Submission, Review, Editorial, Dashboard, etc.)
│   └── Middleware/        # 5 middleware classes
├── Jobs/                  # 8 queued jobs
├── Livewire/              # 6 Livewire components
├── Mail/                  # 4 Mailable classes
├── Models/                # 45 Eloquent models
├── Notifications/         # 9 Notification classes
├── Observers/             # 1 Observer (JournalObserver)
├── Policies/              # 3 Policies (Submission, Issue, ReviewAssignment)
├── Providers/             # 3 Service Providers
├── Rules/                 # 1 Custom validation rule
├── Services/              # 16 Service classes
├── Traits/                # 4 Traits
└── View/                  # 5 View components/composers
```

### Key Middleware

| Middleware                 | Purpose                                     |
| -------------------------- | ------------------------------------------- |
| `DetectJournalContext`     | Detects journal from route and sets context |
| `JournalContextMiddleware` | Enforces journal context is present         |
| `CrudPermissionMiddleware` | Checks CRUD permissions                     |
| `ValidateApiKey`           | API key validation                          |
| `AdsTrackingRateLimit`     | Rate limiting for ad tracking               |

### Key Services

| Service                   | Purpose                                  |
| ------------------------- | ---------------------------------------- |
| `DoiService`              | DOI generation & management              |
| `JournalEmailService`     | Journal-scoped email sending             |
| `JournalSetupService`     | Initial journal setup/seeding            |
| `MalwareScannerService`   | Heuristic malware detection              |
| `OaiHarvesterService`     | OAI-PMH harvesting from external sources |
| `ScholarCheckerService`   | Google Scholar indexing status checks    |
| `ImageCompressionService` | Image optimization                       |
| `VideoCompressionService` | Video processing via FFmpeg              |
| `MergeUserService`        | User account merging                     |
| `WaGateway`               | WhatsApp notification gateway            |
| `GoogleScholarValidator`  | Scholar metadata validation              |
| `MetadataManager`         | Article metadata management              |
| `ExcelService`            | Excel operations                         |
| `MediaService`            | Media file management                    |
| `ImageService`            | Image operations                         |
| `YouTubeSearchService`    | YouTube integration                      |

---

## 5. Modules & Features

### 5.1 Authentication & User Management

**Status:** ✅ DONE

**Implementation:**

- Standard email/password login with Laravel Auth
- Google OAuth via Laravel Socialite
- Forgot/reset password flow
- Per-journal registration pages
- Profile management (avatar, bio, ORCID, affiliation, country)
- Role-based enrollment per journal
- User impersonation (Login As)
- User merge functionality
- Bulk email notification to users

**Roles (Spatie Permission + JournalUserRole):**

- Super Admin, Admin, Journal Manager, Editor, Section Editor, Reviewer, Author, Reader
- Journal-scoped role assignments via `journal_user_roles` table
- Permission levels with hierarchy enforcement

### 5.2 Submission Workflow

**Status:** ✅ DONE

**Full Flow:**

1. **Author Submits:** Multi-step wizard → draft → submitted
2. **Editor Review:** Assign editor → editorial assignment created
3. **Stage Progression:** Submission → Review → Copyediting → Production
4. **Decisions:** Send to Review, Accept (Skip Review), Decline
5. **File Management:** Multi-type file uploads per stage (manuscript, revision, supplementary)
6. **Submission Code:** Auto-generated `[ABBR]-[YEAR]-[SEQ]` format
7. **Activity Log:** Full timeline of all actions via `submission_logs`

**Controllers:** `SubmissionController` (Author CRUD), `SubmissionWorkflowController` (782 lines — editor workflow), `SubmissionFileController` (file management)

### 5.3 Review Workflow

**Status:** ✅ DONE

**Full Flow:**

1. **Assign Reviewer:** Search & assign with method selection (blind/double_blind/open)
2. **Reviewer Actions:** Accept/Decline invitation → Submit review (recommendation + comments + attachments)
3. **Editor Decisions:** Accept, Minor Revision, Major Revision, Reject, Resubmit for Review
4. **Revision Request:** OJS 3.3-style modal with file attachments and email notification
5. **Multi-Round Review:** Create new review round → promote revision files → assign new reviewers
6. **Copyediting Promotion:** Copy review files to draft files
7. **Rating:** Quality rating (1-5) for reviewer performance
8. **Due Date Management:** Configurable response/completion deadlines

**Controllers:** `ReviewWorkflowController` (1117 lines), `ReviewerController` (reviewer portal), `EditorDecisionController`

**Gap:** None critical — feature is comprehensive with multi-round review support.

### 5.4 Production & Publishing

**Status:** ✅ DONE

**Implementation:**

- Galley management (PDF, HTML, EPUB) with file uploads and remote URLs
- Publication versioning (multiple versions per submission)
- DOI assignment — configurable prefix/suffix patterns, per-article DOI
- Issue management — create, publish, unpublish, add/remove articles
- Schedule for publication → publish → unpublish cycle
- Metadata editing (title/abstract, keywords, references, contributors, license)
- Contributor management with reordering
- Citation export (RIS, BibTeX)
- Crossref XML export for DOI registration
- Native XML import/export

### 5.5 Notification & Email System

**Status:** ✅ DONE

**Notification Classes (9):**
| Class | Trigger | Channels |
|---|---|---|
| `NewSubmissionNotification` | New submission created | mail + database |
| `SubmissionReceived` | Author acknowledgment | mail + database |
| `EditorAssignmentNotification` | Editor assigned to submission | mail + database |
| `ReviewInvitation` | Reviewer invited | mail + database |
| `ReviewCompleted` | Reviewer submits review | mail + database |
| `SubmissionDecision` | Editor records decision | mail + database |
| `SubmissionDeclinedNotification` | Submission declined | mail + database |
| `NewDiscussionMessageNotification` | New discussion reply | mail + database |
| `ArticlePublished` | Article published | mail + database |

**Mailable Classes (4):**
| Class | Purpose |
|---|---|
| `RevisionRequestMail` | Revision request with file attachments |
| `SubmissionAcceptedMail` | Acceptance notification |
| `SubmissionSentToProductionMail` | Production stage notification |
| `GeneralNotificationMail` | Generic bulk email |

**Jobs (8):**
| Job | Purpose |
|---|---|
| `SendSubmissionNotifications` | Dispatch submission notifications |
| `SendDecisionEmailJob` | Send decision emails |
| `SendBroadcastNotificationJob` | Bulk notification dispatch |
| `SendToWhatsappNotificationJob` | WhatsApp notifications via WaGateway |
| `CheckArticleIndexJob` | Check Google Scholar indexing |
| `ImportOaiBatchJob` | OAI-PMH batch import |
| `ProcessFileBatchJob` | File batch processing |
| `ScanInitiatorJob` | Malware scan initiation |

**WhatsApp Integration:** Toggle per journal via `wa_notifications_enabled`, sent through `WaGateway` service.

**Email Templates:** Customizable per journal (`email_templates` table), with enable/disable and reset to default.

**Gap:**

- 🔧 Copyediting stage notification (author notified when copyediting starts) — partially covered by `SubmissionSentToProductionMail`
- ❌ Reviewer reminder email for overdue reviews — MISSING

### 5.6 Discussion System

**Status:** ✅ DONE

**Implementation:**

- Stage-aware discussions (filtered by workflow stage)
- Threaded replies with message editing
- File attachments in discussions
- Participant tracking
- Open/close/reopen discussions
- Read tracking per user
- CKEditor image uploads within discussions

### 5.7 Reporting & Statistics

**Status:** ✅ DONE

**Statistics Dashboards (3):**

1. **Article Statistics** (`ArticleStatsController`) — Views, downloads, geographic distribution with chart granularity (daily/weekly/monthly)
2. **Editorial Statistics** (`EditorialStatsController`) — Submission pipeline, review turnaround, acceptance rate
3. **User Statistics** (`UserStatsController`) — Registration trends, role distribution

**Report Generator** (`ReportController`) — 3 report types:

1. **Articles Report** — Submissions with code, title, author, section, status, dates
2. **Reviews Report** — Review assignments with turnaround time calculation
3. **Usage Report** — COUNTER-style article metrics by month

**Export:** CSV stream (memory-efficient chunked export with UTF-8 BOM)

**Google Scholar Monitor** (`ScholarMonitorController`) — Per-article Google Scholar indexing status tracking.

**Gap:** None — comprehensive with live dashboard and CSV export.

### 5.8 Journal Settings

**Status:** ✅ DONE

**Implemented Settings Panels:**

- **Masthead:** Name, abbreviation, ISSN, publisher, description
- **Contact:** Editorial contact info, support contact
- **Sections:** CRUD with meta fields (policy, word count limits)
- **Categories:** CRUD for article categorization
- **Workflow Settings:** Submission checklists, review forms, email templates, library files
- **Distribution:** License, copyright, DOI configuration, OAI-PMH, LOCKSS/CLOCKSS
- **Website Appearance:** Homepage image, favicon, sidebar blocks, custom headers, SEO
- **Navigation:** Menu system with drag-and-drop reordering
- **Sidebar:** Block-based sidebar manager
- **DOI Settings:** Enable DOI, prefix, suffix pattern, custom patterns

### 5.9 Public-Facing Pages

**Status:** ✅ DONE

- Journal homepage (customizable via settings)
- Current issue & archives
- About page, editorial team page
- Article view with galley download
- Article reader (inline PDF viewer)
- Search (full-text + quick search)
- Announcements list & detail
- Information pages (Readers, Authors, Librarians)
- Author guidelines
- Custom pages (from navigation menu)
- Issue view with article listing
- Citation export (RIS, BibTeX)
- Dynamic sitemap (XML)
- OAI-PMH endpoint (Google Scholar compatible)

### 5.10 Site Administration (Super Admin)

**Status:** ✅ DONE

- Site settings management
- System information display
- Journal CRUD (create, edit, enable/disable, delete)
- Site appearance (block-based page builder)
- Site pages (CMS)
- Site navigation management
- Expire sessions, clear cache, clear template cache, clear logs
- Malware Guard (heuristic file scanner)

### 5.11 Tools & Import/Export

**Status:** ✅ DONE

- **OAI-PMH Import:** Preview & harvest articles from external OJS instances
- **Native XML Import/Export:** Import/export articles and issues
- **Crossref XML Export:** Generate DOI registration XML
- **Permission Reset Tool:** Reset role permissions to defaults

### 5.12 OAI-PMH Protocol

**Status:** ✅ DONE

- Full OAI-PMH 2.0 implementation
- Verbs: Identify, ListRecords, ListIdentifiers, ListSets, GetRecord, ListMetadataFormats
- Dublin Core metadata format
- XSLT stylesheet for browser viewing
- Timestamp precision to second (PostgreSQL compatible)
- Configurable per journal (enable_oai setting)

---

## 6. API Endpoints

### 6.1 Portal & Public Routes

| Method | URI                      | Name            | Description         |
| ------ | ------------------------ | --------------- | ------------------- |
| GET    | `/`                      | portal.home     | Portal homepage     |
| GET    | `/search`                | portal.search   | Global search       |
| GET    | `/journals`              | portal.journals | Journal listing     |
| GET    | `/about`                 | portal.about    | About page          |
| GET    | `/page/{slug}`           | site.page       | CMS page            |
| GET    | `/sitemap.xml`           | sitemap         | Dynamic XML sitemap |
| GET    | `/files/{file}/download` | files.download  | File download       |

### 6.2 Auth Routes

| Method   | URI                                    | Name                  |
| -------- | -------------------------------------- | --------------------- |
| GET/POST | `/login`                               | login / authenticate  |
| POST     | `/logout`                              | logout                |
| GET/POST | `/register`                            | register              |
| GET/POST | `/forgot-password`                     | forgot-password       |
| GET/POST | `/change-password` / `/reset-password` | change/reset password |
| GET      | `/auth/google`                         | auth.google           |
| GET      | `/auth/google/callback`                | auth.google.callback  |

### 6.3 Admin Routes (Super Admin — `/admin/*`)

Site settings, system info, journals CRUD, site appearance (block builder), site pages CMS, site navigation, malware guard, tools/crossref.

### 6.4 Journal-Scoped Public Routes (`/{journal}/*`)

Article view, issue view, archives, current issue, search, announcements, editorial team, author guidelines, information pages, custom pages, galley download, citation export (RIS/BibTeX), OAI-PMH.

### 6.5 Journal Dashboard Routes (Auth — `/{journal}/*`)

| Module          | Route Prefix                      | Key Actions                                                        |
| --------------- | --------------------------------- | ------------------------------------------------------------------ |
| Dashboard       | `/dashboard`                      | Journal overview                                                   |
| Profile         | `/profile`                        | Edit profile, avatar, password, roles                              |
| Submissions     | `/submissions`                    | CRUD + file upload                                                 |
| Workflow        | `/workflow/{sub}`                 | Editor workflow (assign, stage change, decisions)                  |
| Review          | `/workflow/{sub}/assign-reviewer` | Reviewer assignment, review round management                       |
| Production      | `/workflow/{sub}/galley`          | Galley CRUD, publish/unpublish                                     |
| Publication     | `/workflow/{sub}/publication`     | Metadata, contributors, DOI, license                               |
| Editorial Queue | `/editorial/queue`                | Editor submission queue                                            |
| Issues          | `/issues`                         | Issue CRUD, publish/unpublish                                      |
| Reviewer Portal | `/reviewer`                       | Reviewer dashboard, submit review                                  |
| Editor Decision | `/editor/submission`              | Editor decision panel                                              |
| Announcements   | `/announcements`                  | CRUD                                                               |
| Settings        | `/settings`                       | Journal, workflow, distribution, website, DOI, navigation, sidebar |
| Statistics      | `/settings/statistics/*`          | Articles, editorial, users, reports                                |
| Scholar Monitor | `/settings/stats/scholar`         | Google Scholar index tracking                                      |
| Tools           | `/settings/tools/*`               | Import, export, permissions                                        |
| Users           | `/users`                          | User management, roles, bulk email, merge, impersonation           |

### 6.6 API Routes (`/api/*`)

| Method | URI                                | Name                  |
| ------ | ---------------------------------- | --------------------- |
| GET    | `/api/journal/{journal}/reviewers` | api.journal.reviewers |
| GET    | `/api/keywords`                    | api.keywords          |

---

## 7. Known Issues & Technical Debt

| #   | Issue                                          | Severity  | Notes                                                                                              |
| --- | ---------------------------------------------- | --------- | -------------------------------------------------------------------------------------------------- |
| 1   | **Duplicate route groups for User Management** | ⚠️ Medium | `journal.users.*` and `journal.admin.users.*` register identical routes — redundant                |
| 2   | **Role middleware commented out**              | ⚠️ Medium | Several route groups have `middleware('role:...')` commented out for development                   |
| 3   | **Legacy controllers not in use**              | ✅ Fixed  | 29 Admin legacy controllers have been audited and permanently deleted.                             |
| 4   | **No foreign key constraints**                 | ⚠️ Medium | Relations use index-only (no `foreign()` constraints) — data integrity relies on application logic |
| 5   | **Mixed slug resolution**                      | 🔧 Low    | Submissions resolve by `seq_id` with fallback to UUID/slug via 301 — adds complexity               |
| 6   | **Session table user_id type mismatch**        | 🔧 Low    | Two migrations to fix UUID type on sessions table — indicates schema evolution issues              |
| 7   | **No automated tests**                         | ⚠️ High   | Tests directory has only scaffolding (5 files). No meaningful test coverage                        |
| 8   | **Missing reviewer reminder**                  | 🔧 Medium | No scheduled job to remind overdue reviewers                                                       |

---

## 8. Pending Development

### HIGH Priority

| Task                                | Description                                                                   | Effort   |
| ----------------------------------- | ----------------------------------------------------------------------------- | -------- |
| Add reviewer reminder notifications | Scheduled job to email/WhatsApp reviewers approaching or past due dates       | 2-3 days |
| Re-enable role middleware           | Review and re-enable commented-out `middleware('role:...')` on route groups   | 1 day    |
| Add automated tests                 | Unit + Feature tests for submission workflow, review flow, and publishing     | 5-7 days |
| Clean up legacy controllers         | ~~Remove unused controllers (BookingIna, BPD, MeetingRoom, etc.)~~ **[DONE]** | 0.5 day  |

### MEDIUM Priority

| Task                                     | Description                                                                 | Effort   |
| ---------------------------------------- | --------------------------------------------------------------------------- | -------- |
| Add foreign key constraints              | Add proper FK constraints with cascade rules to all relation columns        | 2-3 days |
| Deduplicate user management routes       | Consolidate `journal.users.*` and `journal.admin.users.*` into single group | 0.5 day  |
| Add copyediting stage email notification | Notify author when submission moves to copyediting                          | 0.5 day  |
| Implement plagiarism check integration   | External API integration for plagiarism detection                           | 3-5 days |

### LOW Priority

| Task                                   | Description                                                   | Effort   |
| -------------------------------------- | ------------------------------------------------------------- | -------- |
| Add multi-language support             | i18n for UI labels and email templates                        | 5-7 days |
| Implement subscription/paywall support | Reader access control for pay-per-article journals            | 5-7 days |
| Add audit trail for settings changes   | Log who changed what setting and when                         | 2-3 days |
| Optimize article metrics queries       | Add database views or materialized views for stats dashboards | 2-3 days |

---

## 9. Summary

IAMJOS is a substantially complete OJS 3.3 clone with modern Laravel 12 architecture. The core editorial workflow (Submission → Review → Copyediting → Production → Publishing) is fully implemented with multi-round review support. The notification system covers all major workflow events across mail, database, and WhatsApp channels. Statistics and reporting are comprehensive with three dashboard types and CSV export.

**Architecture:** Monolithic MVC with Blade + Alpine.js + Livewire. PostgreSQL with UUID primary keys and JSONB for flexible metadata. Spatie Permission for RBAC with journal-scoped role assignments.

**Overall Status:**

- ✅ **DONE** — 11 major modules fully functional
- 🔧 **PARTIAL** — 1 item (reviewer reminders missing from otherwise complete notification system)
- ❌ **MISSING** — Automated tests, plagiarism check
- ⚠️ **TECH DEBT** — Commented middleware, no FK constraints
