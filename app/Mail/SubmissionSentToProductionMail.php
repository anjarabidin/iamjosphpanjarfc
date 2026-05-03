<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionSentToProductionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Submission $submission;
    public string $emailBody;

    /**
     * Create a new message instance.
     */
    public function __construct(Submission $submission, string $emailBody)
    {
        $this->submission = $submission;
        $this->emailBody = $emailBody;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $journal = $this->submission->journal;

        return new Envelope(
            subject: "[{$journal->name}] Your submission is now in Production",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submission-sent-to-production',
            with: [
                'submissionTitle' => $this->submission->title,
                'authorName' => $this->submission->author->name ?? 'Author',
                'journalName' => $this->submission->journal->name ?? 'Journal',
                'body' => $this->emailBody,
                'submissionUrl' => route('journal.submissions.show', [
                    'journal' => $this->submission->journal->slug,
                    'submission' => $this->submission->slug,
                ]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
