<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDecisionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $submission;
    public $emailBody;
    public $decisionType;

    /**
     * Create a new job instance.
     */
    public function __construct($submission, $emailBody, $decisionType)
    {
        $this->submission = $submission;
        $this->emailBody = $emailBody;
        $this->decisionType = $decisionType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Basic SMTP configuration check
            if (!config('mail.mailers.smtp.host') || !config('mail.mailers.smtp.username')) {
                Log::warning("SendDecisionEmailJob: SMTP not configured. Skipping email for submission {$this->submission->id}.");
                return;
            }

            $recipient = $this->submission->author;
            if (!$recipient) {
                Log::warning("SendDecisionEmailJob: No author found for submission {$this->submission->id}.");
                return;
            }

            if ($this->decisionType === 'accepted') {
                Mail::to($recipient->email)->send(new \App\Mail\SubmissionAcceptedMail($this->submission, $this->emailBody));
            } elseif ($this->decisionType === 'declined') {
                // Future implementation for declined email
                // Mail::to($recipient->email)->send(new \App\Mail\SubmissionDeclinedMail($this->submission, $this->emailBody));
            } elseif ($this->decisionType === 'revisions') {
                // Mail::to($recipient->email)->send(new \App\Mail\RevisionRequestMail($this->submission, $this->emailBody));
            } elseif ($this->decisionType === 'send_to_production') {
                Mail::to($recipient->email)->send(new \App\Mail\SubmissionSentToProductionMail($this->submission, $this->emailBody));
            }

            Log::info("Decision email sent successfully " . $this->decisionType);
        } catch (\Exception $e) {
            // Log error but do not fail the job/workflow
            Log::error("Failed to send decision email for submission {$this->submission->id}: " . $e->getMessage());
        }
    }
}
