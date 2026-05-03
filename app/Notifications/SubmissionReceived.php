<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReceived extends Notification
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
        return (new MailMessage)
            ->subject('Submission Received - ' . $this->submission->title)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your submission has been received and is now under editorial review.')
            ->line('**Submission Details:**')
            ->line('- **Title:** ' . $this->submission->title)
            ->line('- **Submission ID:** ' . $this->submission->id)
            ->line('- **Submitted:** ' . $this->submission->submitted_at->format('F j, Y'))
            ->action('View Submission', url('/submissions/' . $this->submission->id))
            ->line('You will be notified when there are updates on your submission.')
            ->salutation('Best regards, Editorial Team');
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        $journal = $this->submission->journal;

        return [
            'type' => 'submission_received',
            'title' => 'Submission Received',
            'message' => "Your submission \"{$this->submission->title}\" has been received and is under review.",
            'url' => "/{$journal->slug}/submissions/{$this->submission->slug}",
            'notification_type' => 'success',
            'icon' => 'fa-check-circle',
            'submission_id' => $this->submission->id,
            'submission_title' => $this->submission->title,
        ];
    }
}
