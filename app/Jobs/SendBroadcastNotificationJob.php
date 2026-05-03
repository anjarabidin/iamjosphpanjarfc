<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\GeneralNotificationMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBroadcastNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 60, 300];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $subject,
        public string $body,
        public string $journalName
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Basic SMTP configuration check
            if (!config('mail.mailers.smtp.host') || !config('mail.mailers.smtp.username')) {
                Log::warning("SendBroadcastNotificationJob: SMTP not configured. Skipping email for user {$this->user->id}.");
                return;
            }

            // Validate recipient email
            if (empty($this->user->email)) {
                Log::warning("SendBroadcastNotificationJob: No email found for user {$this->user->id}.");
                return;
            }

            // Dynamic Placeholder Replacement
            $placeholders = [
                '{$name}' => $this->user->name,
                '{$email}' => $this->user->email,
                '{$journal_name}' => $this->journalName,
                '{$site_url}' => config('app.url'),
            ];

            $finalSubject = str_replace(array_keys($placeholders), array_values($placeholders), $this->subject);
            $finalBody = str_replace(array_keys($placeholders), array_values($placeholders), $this->body);

            // Send the email synchronously (sendNow prevents the Queueable Mailable from re-queuing inside this job)
            Mail::to($this->user->email)->sendNow(
                new GeneralNotificationMail(
                    $finalSubject,
                    $finalBody,
                    $this->user->name,
                    $this->journalName
                )
            );

            Log::info("Broadcast notification sent successfully to {$this->user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send broadcast notification to user {$this->user->id}: " . $e->getMessage());
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendBroadcastNotificationJob permanently failed for user {$this->user->id}: " . $exception->getMessage());
    }
}
