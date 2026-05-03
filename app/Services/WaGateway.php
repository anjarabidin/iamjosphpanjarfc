<?php

namespace App\Services;

use App\Jobs\SendToWhatsappNotificationJob;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WaGateway
{
    /**
     * Safe dispatch method for WhatsApp Notifications.
     * 
     * Handles phone validation, normalization (08 → 628), and 
     * graceful failure if phone is empty or credentials missing.
     *
     * @param mixed $userOrNumber User Model instance or raw phone string
     * @param string $message The WhatsApp message to send
     * @return bool True if job was dispatched, false otherwise
     */
    public static function send($userOrNumber, string $message): bool
    {
        // 1. Resolve Phone Number from input
        $phone = null;
        $name = 'User';

        if ($userOrNumber instanceof User) {
            $phone = $userOrNumber->phone;
            $name = $userOrNumber->name ?? 'User';
        } elseif (is_string($userOrNumber)) {
            $phone = $userOrNumber;
        }

        // 2. Safety Check - Null/Empty phone
        if (empty($phone)) {
            Log::debug('WaGateway: Phone number is empty, skipping WhatsApp notification.', [
                'user_name' => $name,
            ]);
            return false; // Fail silently, do not dispatch
        }

        // 2.5. Journal Toggle Check
        $journal = current_journal();
        if ($journal && !$journal->wa_notifications_enabled) {
            Log::info("WhatsApp notification skipped: Disabled for journal {$journal->name}");
            return false;
        }

        // 3. Check if WhatsApp credentials are configured
        $settings = SiteSetting::first();

        if (!$settings || empty($settings->wa_api_url) || empty($settings->wa_device_id)) {
            Log::warning('WaGateway: WhatsApp credentials not configured in site_settings.', [
                'has_api_url' => !empty($settings?->wa_api_url),
                'has_device_id' => !empty($settings?->wa_device_id),
            ]);
            return false; // Fail silently if not configured
        }

        // 4. Normalize Phone Number
        $phone = self::normalizePhone($phone);

        // 5. Dispatch Job (using afterResponse to not block user)
        try {
            dispatch(new SendToWhatsappNotificationJob($phone, $message));

            Log::info('WaGateway: WhatsApp notification dispatched.', [
                'phone' => self::maskPhone($phone),
                'message_length' => strlen($message),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('WaGateway: Failed to dispatch WhatsApp job.', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Normalize Indonesian phone number format.
     * 
     * Converts:
     * - 08xxx → 628xxx
     * - +62xxx → 62xxx
     * - Removes spaces, dashes, and other non-numeric characters
     *
     * @param string $phone Raw phone number
     * @return string Normalized phone number
     */
    public static function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters except leading +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Remove leading + if present
        $phone = ltrim($phone, '+');

        // Convert 08 prefix to 628 (Indonesian mobile format)
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        // Convert 8 prefix (without country code) to 628
        if (str_starts_with($phone, '8') && strlen($phone) >= 9 && strlen($phone) <= 12) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Mask phone number for logging (privacy).
     *
     * @param string $phone Phone number
     * @return string Masked phone (e.g., 6281****5678)
     */
    private static function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 6) {
            return '****';
        }

        return substr($phone, 0, 4) . '****' . substr($phone, -4);
    }

    /**
     * Send WhatsApp notification using Dynamic Template (DB or Default).
     *
     * @param User $user Target user
     * @param string $templateKey Template key (e.g. 'welcome', 'submission_received')
     * @param array $params Parameters to replace in message template
     * @param int|null $journalId Journal ID context (optional). If null, uses global template only.
     * @return bool
     */
    public static function sendTemplate(User $user, string $templateKey, array $params = [], ?string $journalId = null): bool
    {
        // 1. Build Message (Logic DB + Fallback ada di sini)
        $message = self::buildMessage($templateKey, $params, $journalId);

        if (empty($message)) {
            Log::warning("WaGateway: Message content is empty for template '{$templateKey}'. Aborting send.");
            return false;
        }

        return self::send($user, $message);
    }

    /**
     * Build message logic: Check Journal DB -> Check Global DB -> Fallback to Hardcode.
     */
    private static function buildMessage(string $key, array $params, ?string $journalId = null): ?string
    {
        // 1. Ambil Default Templates (Hardcoded) sebagai Fallback Terakhir
        $defaults = self::getDefaultTemplates();

        // 2. Cek apakah Template Key valid/dikenali
        if (!array_key_exists($key, $defaults)) {
            Log::warning('WaGateway: Unknown template key.', ['template' => $key]);
            return null;
        }

        $messageTemplate = null;

        try {
            // 3. Priority 1: Check Journal Specific Template
            if ($journalId) {
                $journalTemplate = NotificationTemplate::where('event_key', $key)
                    ->where('channel', 'whatsapp')
                    ->where('journal_id', $journalId)
                    ->where('is_active', true)
                    ->first();
                
                if ($journalTemplate && !empty($journalTemplate->body)) {
                    $messageTemplate = $journalTemplate->body;
                }
            }

            // 4. Priority 2: Check Global/System Template (journal_id IS NULL)
            if (empty($messageTemplate)) {
                $globalTemplate = NotificationTemplate::where('event_key', $key)
                    ->where('channel', 'whatsapp')
                    ->whereNull('journal_id')
                    ->where('is_active', true)
                    ->first();

                if ($globalTemplate && !empty($globalTemplate->body)) {
                    $messageTemplate = $globalTemplate->body;
                }
            }

        } catch (\Exception $e) {
            // Jika error (misal migrasi belum jalan), abaikan dan lanjut pakai default
            Log::warning('WaGateway: Failed to fetch custom template from DB. Using default.', ['error' => $e->getMessage()]);
        }

        // 5. Priority 3: Fallback ke Hardcoded Default
        if (empty($messageTemplate)) {
            $messageTemplate = $defaults[$key];
        }

        // 6. Replace Placeholders (Variables)
        foreach ($params as $paramKey => $paramValue) {
            $safeValue = is_array($paramValue) ? json_encode($paramValue) : (string) $paramValue;
            $messageTemplate = str_replace('{' . $paramKey . '}', $safeValue, $messageTemplate);
        }

        return $messageTemplate;
    }

    /**
     * Menyediakan Default Template (Hardcoded).
     * Method ini dibuat public agar bisa dipanggil oleh Seeder/Controller 
     * untuk mengisi database saat pertama kali fitur ini diinstall.
     */
    public static function getDefaultTemplates(): array
    {
        return [
            'welcome' => "Selamat datang {name} di IAMJOS. Akun Anda berhasil dibuat.",
            'submission_received' => "Halo {name}, naskah Anda berjudul '{title}' telah berhasil disubmit. Pantau statusnya di dashboard.",
            'decision_update' => "Halo {name}, ada update status untuk naskah '{title}'. Status saat ini: {status}. Silakan cek dashboard.",
            'revision_request' => "Halo {name}, editor meminta revisi untuk naskah '{title}'. Silakan cek dashboard untuk detailnya.",
            'submission_accepted' => "Halo {name}, selamat! Naskah Anda '{title}' telah diterima untuk dipublikasikan.",
            'submission_rejected' => "Halo {name}, mohon maaf naskah Anda '{title}' tidak dapat kami terima. Silakan cek dashboard untuk feedback dari editor.",
            'new_submission_notification' => "Halo {name}, ada naskah baru berjudul '{title}'. Silakan tinjau di dashboard.",
            'reviewer_assigned' => "Halo {name}, Anda telah ditugaskan untuk mereview naskah '{title}' (Round {round}). Silakan cek dashboard untuk detailnya.",
            'discussion_message' => "Halo {name}, ada pesan baru di diskusi '{subject}' untuk naskah '{title}'. Silakan cek dashboard untuk detailnya.",
            'review_submitted' => "Halo {name}, reviewer telah mengirimkan review untuk naskah '{title}'. Silakan cek dashboard untuk detailnya.",
            'reviewer_accepted' => "Halo {name}, Reviewer {reviewer_name} telah menerima undangan review untuk naskah '{title}'.",
            'reviewer_declined' => "Halo {name}, Reviewer {reviewer_name} telah menolak undangan review untuk naskah '{title}'.",
        ];
    }
    
    /**
     * Helper untuk mendapatkan daftar variabel yang tersedia per template.
     * Berguna untuk ditampilkan di UI Settings.
     */
    public static function getTemplateVariables(): array
    {
        return [
            'welcome' => ['name'],
            'submission_received' => ['name', 'title'],
            'decision_update' => ['name', 'title', 'status'],
            'revision_request' => ['name', 'title'],
            'submission_accepted' => ['name', 'title'],
            'submission_rejected' => ['name', 'title'],
            'new_submission_notification' => ['name', 'title'],
            'reviewer_assigned' => ['name', 'title', 'round'],
            'discussion_message' => ['name', 'subject', 'title'],
            'review_submitted' => ['name', 'title'],
            'reviewer_accepted' => ['name', 'reviewer_name', 'title'],
            'reviewer_declined' => ['name', 'reviewer_name', 'title'],
        ];
    }
}
