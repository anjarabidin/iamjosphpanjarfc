<?php

namespace App\Notifications;

use App\Models\ReviewAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewInvitation extends Notification
{
    use Queueable;

    protected ReviewAssignment $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(ReviewAssignment $review)
    {
        $this->review = $review;
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
        $submission = $this->review->submission;
        $dueDate = $this->review->due_date?->format('F j, Y') ?? 'Not specified';

        return (new MailMessage)
            ->subject('Review Invitation - ' . $submission->title)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('You have been invited to review a manuscript for our journal.')
            ->line('**Manuscript Details:**')
            ->line('- **Title:** ' . $submission->title)
            ->line('- **Abstract:** ' . \Str::limit($submission->abstract, 200))
            ->line('- **Due Date:** ' . $dueDate)
            ->line('')
            ->line('Please log in to accept or decline this invitation.')
            ->action('View Invitation', url('/reviews/' . $this->review->id))
            ->line('If you are unable to review this manuscript, please decline as soon as possible so we can invite another reviewer.')
            ->salutation('Best regards, Editorial Team');
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        $submission = $this->review->submission;
        $journal = $submission->journal;

        return [
            'type' => 'review_invitation',
            'title' => 'New Review Assignment',
            'message' => "You have been invited to review \"{$submission->title}\".",
            'url' => "/{$journal->slug}/reviewer/{$this->review->id}",
            'notification_type' => 'info',
            'icon' => 'fa-clipboard-check',
            'review_id' => $this->review->id,
            'submission_id' => $submission->id,
            'submission_title' => $submission->title,
            'journal_id' => $journal->id,
            'journal_slug' => $journal->slug,
            'due_date' => $this->review->due_date?->format('Y-m-d'),
        ];
    }
}
