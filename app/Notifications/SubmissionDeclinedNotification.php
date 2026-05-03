<?php

namespace App\Notifications;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to author when their submission is declined.
 */
class SubmissionDeclinedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Submission $submission;
    protected User $declinedBy;
    protected string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Submission $submission, User $declinedBy, string $reason)
    {
        $this->submission = $submission;
        $this->declinedBy = $declinedBy;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $journal = $this->submission->journal;
        $url = url("/{$journal->slug}/submissions/{$this->submission->slug}");

        return (new MailMessage)
            ->subject('Submission Declined: ' . $this->submission->title)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We regret to inform you that your submission has been declined by the editorial team.')
            ->line('**Submission Title:** ' . $this->submission->title)
            ->line('**Journal:** ' . $journal->name)
            ->line('**Reason for Declining:**')
            ->line($this->reason)
            ->line('Thank you for considering our journal for your work. We encourage you to submit your manuscript to another appropriate venue.')
            ->action('View Submission', $url)
            ->salutation('Best regards, ' . $journal->name . ' Editorial Team');
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        $journal = $this->submission->journal;

        return [
            'type' => 'submission_declined',
            'submission_id' => $this->submission->id,
            'journal_id' => $journal->id,
            'journal_slug' => $journal->slug,
            'declined_by_id' => $this->declinedBy->id,
            'declined_by_name' => $this->declinedBy->name,
            'title' => $this->submission->title,
            'message' => 'Your submission "' . $this->submission->title . '" has been declined.',
            'url' => "/{$journal->slug}/submissions/{$this->submission->slug}",
        ];
    }
}
