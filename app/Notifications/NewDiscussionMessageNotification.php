<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Discussion;
use Illuminate\Bus\Queueable;
use App\Models\ReviewAssignment;
use App\Models\DiscussionMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification sent to discussion participants when a new message is posted.
 */
class NewDiscussionMessageNotification extends Notification
{
    use Queueable;

    protected Discussion $discussion;
    protected DiscussionMessage $message;
    protected User $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct(Discussion $discussion, DiscussionMessage $message, User $sender)
    {
        $this->discussion = $discussion;
        $this->message = $message;
        $this->sender = $sender;
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
        $submission = $this->discussion->submission;
        $journal = $submission->journal;

        // Check if user is reviewer
        $isReviewer = $notifiable->hasRole('Reviewer');

        if ($isReviewer) {
            // For reviewer, link to reviewer show page
            $assignment = ReviewAssignment::where('submission_id', $submission->id)
                ->where('reviewer_id', $notifiable->id)
                ->first();
            $url = $assignment ? route('journal.reviewer.show', ['journal' => $journal->slug, 'assignment' => $assignment->id]) : url("/{$journal->slug}/submissions/{$submission->slug}");
        } else {
            $url = url("/{$journal->slug}/submissions/{$submission->slug}");
        }

        return (new MailMessage)
            ->subject('New Message in Discussion: ' . $this->discussion->subject)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line($this->sender->name . ' posted a message in the discussion "' . $this->discussion->subject . '".')
            ->line('**Submission:** ' . $submission->title)
            ->line('**Message Preview:**')
            ->line(strip_tags(substr($this->message->body, 0, 200)) . '...')
            ->action('View Discussion', $url)
            ->salutation('Best regards, ' . $journal->name);
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        $submission = $this->discussion->submission;
        $journal = $submission->journal;

        // Check if user is reviewer
        $isReviewer = $notifiable->hasRole('Reviewer');

        if ($isReviewer) {
            // For reviewer, link to reviewer show page
            $assignment = ReviewAssignment::where('submission_id', $submission->id)
                ->where('reviewer_id', $notifiable->id)
                ->first();
            $url = $assignment ? "/{$journal->slug}/reviewer/{$assignment->id}" : "/{$journal->slug}/submissions/{$submission->slug}?open_discussion={$this->discussion->id}";
        } else {
            $url = "/{$journal->slug}/submissions/{$submission->slug}?open_discussion={$this->discussion->id}";
        }

        return [
            'type' => 'new_discussion_message',
            'title' => 'New Discussion Message',
            'message' => "{$this->sender->name} posted a message in \"{$this->discussion->subject}\".",
            'url' => $url,
            'notification_type' => 'info',
            'icon' => 'fa-comments',
            'discussion_id' => $this->discussion->id,
            'message_id' => $this->message->id,
            'submission_id' => $submission->id,
            'journal_id' => $journal->id,
            'journal_slug' => $journal->slug,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'subject' => $this->discussion->subject,
        ];
    }
}
