<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $emailBody;

    /**
     * Create a new message instance.
     */
    public function __construct($submission, $emailBody)
    {
        $this->submission = $submission;
        $this->emailBody = $emailBody;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Editor Decision: Submission Accepted',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submission-accepted',
            with: [
                'submissionTitle' => $this->submission->title,
                'authorName' => $this->submission->author->name ?? 'Author',
                'body' => $this->emailBody,
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
