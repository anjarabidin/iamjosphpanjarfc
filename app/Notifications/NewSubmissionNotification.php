<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to Journal Managers and Editors when a new submission is created.
 */
class NewSubmissionNotification extends Notification
{
    use Queueable;

    protected Submission $submission;

    /**
     * Create a new notification instance.
     */
    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
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
        $url = url("/{$journal->slug}/submissions/{$this->submission->id}");

        return (new MailMessage)
            ->subject('New Submission - ' . $this->submission->title)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('A new submission has been submitted to ' . $journal->name . '.')
            ->line('**Submission Details:**')
            ->line('- **Title:** ' . $this->submission->title)
            ->line('- **Author:** ' . ($this->submission->authors->first()->name ?? 'Unknown'))
            ->line('- **Section:** ' . ($this->submission->section->title ?? 'Not specified'))
            ->line('- **Submitted:** ' . $this->submission->submitted_at->format('F j, Y'))
            ->action('View Submission', $url)
            ->line('Please review and assign an editor to handle this submission.')
            ->salutation('Best regards, ' . $journal->name);
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        $journal = $this->submission->journal;

        return [
            'type' => 'new_submission',
            'title' => 'New Submission Received',
            'message' => "A new submission has been submitted: \"{$this->submission->title}\".",
            'url' => "/{$journal->slug}/submissions/{$this->submission->slug}",
            'notification_type' => 'info',
            'icon' => 'fa-file-circle-plus',
            'submission_id' => $this->submission->id,
            'submission_title' => $this->submission->title,
            'journal_id' => $journal->id,
            'journal_slug' => $journal->slug,
            'author' => $this->submission->authors->first()->name ?? 'Unknown',
        ];
    }
}
