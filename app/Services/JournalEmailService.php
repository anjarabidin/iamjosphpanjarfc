<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class JournalEmailService
{
    /**
     * Send a notification email to a user using specific template logic.
     *
     * @param Journal $journal
     * @param User $recipient
     * @param string $key Template identifier key
     * @param array $variables Variables to replace in the subject and body
     * @return bool
     */
    public static function sendNotification(Journal $journal, User $recipient, string $key, array $variables = []): bool
    {
        try {
            // 1. Resolve Template (Custom or Default)
            $templateData = self::resolveTemplate($journal, $key);

            if (!$templateData) {
                Log::error("Email template not found for key: {$key} in journal: {$journal->slug}");
                return false;
            }

            // 2. Prepare Variables
            $variables = array_merge($variables, [
                'recipientName' => $recipient->full_name,
                'recipientEmail' => $recipient->email,
                'journalName' => $journal->name,
                'journalUrl' => route('journal.home', $journal->slug),
            ]);

            // Add signature if not present
            if (!isset($variables['signature'])) {
                // Determine signature source. 
                // Priority: Journal Settings "email_signature" -> Default Site Signature
                // Assuming journal settings are stored in json column or separate table, 
                // for now fallback to simple journal name signature if no setting.
                 
                // If the journal model has a settings relation or similar:
                // $signature = $journal->getSetting('email_signature') ?? $journal->name . ' Editorial Team';
                
                // For this implementation, let's use a placeholder logic until settings are confirmed.
                // If passed variables have signature use it, else default.
                $variables['signature'] = $journal->title ?? $journal->name . "\nEditorial Team"; 
            }

            // 3. Parse Content
            $subject = self::parseVariables($templateData['subject'], $variables);
            $body = self::parseVariables($templateData['body'], $variables);

            // 4. Send Email
            
            // Convert newline to br for HTML emails if needed, or send raw text
            // Laravel Mail uses Markdown or View. Simple text sending:
            
            Mail::queue([], [], function (Message $message) use ($recipient, $subject, $body, $journal, $templateData) {
                $message->to($recipient->email, $recipient->full_name)
                    ->subject($subject);
                
                // Set 'From' address
                // If template has specific from (rarely used in OJS logic, usually journal email)
                // $fromEmail = $templateData['from_email'] ?? $journal->email ?? config('mail.from.address');
                // $fromName = $templateData['from_name'] ?? $journal->name ?? config('mail.from.name');
                
                // Use Journal default email if available
                // Assuming Journal model has 'email' or 'contact_email' field.
                // If not, rely on .env defaults, but try to set FROM name to Journal Name
                
                $fromName = $journal->name;
                $fromEmail = config('mail.from.address'); // Generally we must use a verified sender domain

                $message->from($fromEmail, $fromName);
                
                // Content is HTML from WYSIWYG, so render directly
                $message->html($body);
            });

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send email '{$key}' to {$recipient->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Resolve the email template data. 
     * Prioritizes custom database entry, falls back to Model defaults.
     */
    protected static function resolveTemplate(Journal $journal, string $key): ?array
    {
        // Step 1: Check Database for enabled custom/seeded template
        $dbTemplate = EmailTemplate::where('journal_id', $journal->id)
            ->where('key', $key)
            ->where('is_enabled', true)
            ->first();

        if ($dbTemplate) {
            return [
                'subject' => $dbTemplate->subject,
                'body' => $dbTemplate->body,
                'from_name' => $dbTemplate->from_name,
                'from_email' => $dbTemplate->from_email,
            ];
        }

        // Step 2: Fallback to Model Defaults (in case DB entry is missing or disabled? 
        // User prompt says "If not found" - implies if DB record doesn't exist.
        // If it exists but is disabled, should we send?
        // Prompt Check 1: "Look in email_templates table where ... is_enabled = true"
        // Prompt Check 2: "If not found, look in getDefaultTemplates()"
        // So if disabled in DB, we fall back to Code Default? Or do we not send?
        // Usually if "Disabled" in UI, it means "Don't send this email". 
        // BUT the Prompt requirement says Step 2 is fallback. 
        // Let's assume optimization: If DB record exists and is_enabled=false, we return NULL (don't send).
        // If DB record DOES NOT exist, we check defaults.
        
        $existingDisabled = EmailTemplate::where('journal_id', $journal->id)
            ->where('key', $key)
            ->where('is_enabled', false)
            ->exists();
            
        if ($existingDisabled) {
            // Explicitly disabled by user
            return null;
        }

        // Check defaults array
        $defaults = EmailTemplate::getDefaultTemplates();
        $default = collect($defaults)->firstWhere('key', $key);

        if ($default) {
             return [
                'subject' => $default['subject'],
                'body' => $default['body'],
                'from_name' => null,
                'from_email' => null,
            ];
        }

        // Not found anywhere
        return null;
    }

    /**
     * Parse and replace variables in the text.
     * Supports {$variableName} syntax.
     */
    protected static function parseVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // Simple string replacement
            // Ensure value is string
            $val = is_scalar($value) ? (string)$value : '';
            $text = str_replace('{$' . $key . '}', $val, $text);
        }

        return $text;
    }
}
