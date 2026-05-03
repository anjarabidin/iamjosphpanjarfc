# SECURITY CHANGELOG

## 2026-03-07 — Comprehensive Security Patching v1.1

### 🔴 CRITICAL

- **SEC-001/002/003**: Created `FileUploadSecurityService` with 6-layer validation (double extension, null byte, extension whitelist, MIME magic bytes, PHP code detection, file size). Integrated into `ProductionWorkflowController`, `SubmissionWorkflowController`, and `ReviewWorkflowController`.
- Created `security_logs` table for tracking rejected upload attempts.

### 🟠 HIGH

- **SEC-004**: Applied HTMLPurifier `clean()` to 23 user-content `{!!` instances across 17 Blade views. Installed `mews/purifier` package. XSS test suite with 9 tests (25 assertions) all passing.
- **SEC-005**: Wrapped `/api/journal/{journal}/reviewers` and `/api/keywords` endpoints inside `validate_api_key` middleware.
- **SEC-006**: Removed `disabled`, `disabled_reason`, `must_change_password` from User model `$fillable`.
- **SEC-007**: Changed manuscript upload disk from `public` to `local` in `SubmissionController`.

### 🟡 MEDIUM

- **SEC-008/009**: Hardened session defaults — `secure` and `encrypt` now default to `true`.
- **SEC-010/012**: Moved CKEditor images and discussion files from `public` to `local` disk.
- **SEC-011**: Changed `ValidateApiKey` from `env('API_KEY')` to `config('app.api_key')`. Added `api_key` to `config/app.php`.

### 🟢 LOW

- **SEC-013**: Replaced Google Docs Viewer with self-hosted PDF.js component. Files no longer leave the server.
- **SEC-014**: Replaced `$guarded = []` with explicit `$fillable` in `MalwareScan` and `MalwareFinding` models.
- **SEC-015**: Replaced `$request->all()` with `$request->only()` in `JournalUserManagementController` error logs.
- **SEC-016**: Added `throttle:60,1` middleware to OAI-PMH route.

### Additional Hardening

- Added `.htaccess` rules in `public/` blocking PHP/executable file execution and double-extension attacks.
- Created `storage/app/.htaccess` denying all direct HTTP access.
- Published HTMLPurifier config (`config/purifier.php`) with academic-safe tag whitelist.

### Patched Date: 2026-03-07

**Result: 16/16 findings PATCHED**
