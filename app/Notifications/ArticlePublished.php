<?php

namespace App\Notifications;

use App\Models\Issue;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticlePublished extends Notification implements ShouldQueue
{
    use Queueable;

    protected Submission $submission;
    protected Issue $issue;

    /**
     * Create a new notification instance.
     */
    public function __construct(Submission $submission, Issue $issue)
    {
        $this->submission = $submission;
        $this->issue = $issue;
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
            ->subject('Your Article Has Been Published!')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Congratulations! Your article has been published in our journal.')
            ->line('**Article Details:**')
            ->line('- **Title:** ' . $this->submission->title)
            ->line('- **Issue:** ' . $this->issue->identifier)
            ->line('- **Published:** ' . $this->submission->published_at->format('F j, Y'))
            ->action('View Published Article', url('/articles/' . $this->submission->id))
            ->line('Thank you for your contribution to our journal.')
            ->line('You can now share this publication with your colleagues and on social media.')
            ->salutation('Best regards, Editorial Team');
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'article_published',
            'submission_id' => $this->submission->id,
            'issue_id' => $this->issue->id,
            'title' => $this->submission->title,
            'issue' => $this->issue->identifier,
            'message' => 'Your article "' . $this->submission->title . '" has been published!',
        ];
    }
}
