<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionDecision extends Notification
{
    use Queueable;

    protected Submission $submission;
    protected string $decision;
    protected ?string $comments;

    /**
     * Create a new notification instance.
     */
    public function __construct(Submission $submission, string $decision, ?string $comments = null)
    {
        $this->submission = $submission;
        $this->decision = $decision;
        $this->comments = $comments;
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
        $mail = (new MailMessage)
            ->greeting('Dear ' . $notifiable->name . ',');

        switch ($this->decision) {
            case 'accepted':
                $mail->subject('Congratulations! Your Submission Has Been Accepted')
                    ->line('We are pleased to inform you that your submission has been accepted for publication.')
                    ->line('**Title:** ' . $this->submission->title);
                break;

            case 'rejected':
                $mail->subject('Decision on Your Submission')
                    ->line('Thank you for submitting your manuscript to our journal.')
                    ->line('After careful review, we regret to inform you that your submission has not been accepted for publication.')
                    ->line('**Title:** ' . $this->submission->title);
                break;

            case 'revision_required':
                $mail->subject('Revision Required for Your Submission')
                    ->line('Your submission has been reviewed and requires revision before we can make a final decision.')
                    ->line('**Title:** ' . $this->submission->title);
                break;

            default:
                $mail->subject('Update on Your Submission')
                    ->line('There is an update on your submission.')
                    ->line('**Title:** ' . $this->submission->title)
                    ->line('**Status:** ' . ucfirst($this->decision));
        }

        if ($this->comments) {
            $mail->line('')
                ->line('**Editor\'s Comments:**')
                ->line($this->comments);
        }

        return $mail
            ->action('View Submission', url('/submissions/' . $this->submission->id))
            ->salutation('Best regards, Editorial Team');
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        $journal = $this->submission->journal;

        $titles = [
            'accepted' => 'Submission Accepted',
            'rejected' => 'Submission Declined',
            'revision_required' => 'Revision Required',
        ];

        $types = [
            'accepted' => 'success',
            'rejected' => 'danger',
            'revision_required' => 'warning',
        ];

        $icons = [
            'accepted' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'revision_required' => 'fa-edit',
        ];

        $messages = [
            'accepted' => 'Congratulations! Your submission has been accepted.',
            'rejected' => 'Your submission has been declined.',
            'revision_required' => 'Revision required for your submission.',
        ];

        return [
            'type' => 'submission_decision',
            'title' => $titles[$this->decision] ?? 'Submission Update',
            'message' => $messages[$this->decision] ?? 'There is an update on your submission.',
            'url' => "/{$journal->slug}/submissions/{$this->submission->slug}",
            'notification_type' => $types[$this->decision] ?? 'info',
            'icon' => $icons[$this->decision] ?? 'fa-gavel',
            'submission_id' => $this->submission->id,
            'submission_title' => $this->submission->title,
            'journal_id' => $journal->id,
            'journal_slug' => $journal->slug,
            'decision' => $this->decision,
        ];
    }
}
