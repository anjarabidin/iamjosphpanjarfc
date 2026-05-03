<?php

namespace App\Notifications;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to Editors when they are assigned to handle a submission.
 * Triggered when Manager/Journal Manager assigns an Editor to a submission.
 */
class EditorAssignmentNotification extends Notification
{
    use Queueable;

    protected Submission $submission;
    protected User $assignedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Submission $submission, User $assignedBy)
    {
        $this->submission = $submission;
        $this->assignedBy = $assignedBy;
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
            ->subject('Editor Assignment - ' . $this->submission->title)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('You have been assigned as editor for the following submission:')
            ->line('**Submission Details:**')
            ->line('- **Title:** ' . $this->submission->title)
            ->line('- **Author:** ' . ($this->submission->authors->first()->name ?? 'Unknown'))
            ->line('- **Section:** ' . ($this->submission->section->title ?? 'Not specified'))
            ->line('- **Assigned by:** ' . $this->assignedBy->name)
            ->action('View Submission', $url)
            ->line('Please review the submission and begin the editorial process.')
            ->salutation('Best regards, ' . $journal->name);
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        $journal = $this->submission->journal;

        return [
            'type' => 'editor_assignment',
            'title' => 'Editor Assignment',
            'message' => "You have been assigned as editor for \"{$this->submission->title}\".",
            'url' => "/{$journal->slug}/submissions/{$this->submission->slug}",
            'notification_type' => 'info',
            'icon' => 'fa-user-tie',
            'submission_id' => $this->submission->id,
            'journal_id' => $journal->id,
            'journal_slug' => $journal->slug,
            'assigned_by_id' => $this->assignedBy->id,
            'assigned_by_name' => $this->assignedBy->name,
        ];
    }
}
